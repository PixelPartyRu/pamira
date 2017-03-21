<?php
namespace App;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Database\Eloquent\Collection;

class YaMarket {
    private $oauth_token;
    private $client_id;

    function __construct($outhToken = null, $clientId = null)
    {
        $this->oauth_token = null !== $outhToken ? $outhToken : \Config::get('yandex-market.token');
        $this->client_id = null !== $clientId ? $clientId : \Config::get('yandex-market.client_id');
    }

    public function getSimilarPrices($modelName, $regionId = null) {
        if(null === $regionId) {
            $regionId = \Config::get('yandex-market.region_id');
        }

        $result = $this->callMarketApi('models', [
            'query' => $modelName,
            'regionId' => $regionId,
            'pageSize' => 10
        ]);

        foreach($result->models as $model) {
            $similar = $this->callMarketApi("models/{$model->id}/offers", [
                'regionId' => $regionId,
                'currency' => 'RUR',
            ]);

            $model->similar = $similar;
        }

        return $result;
    }

    protected function callMarketApi($method, $args) {
        $client = new \GuzzleHttp\Client();

        $request = $client->createRequest(
            'GET',
            "https://api.partner.market.yandex.ru/v2/{$method}.json", [
            'query' => $args,
            'headers' => [
                'Authorization' => "OAuth oauth_token=\"{$this->oauth_token}\", oauth_client_id=\"{$this->client_id}\""
            ]
        ]);

        try {
            $response = $client->send($request);
        } catch (ClientException $e) {
            $response = $e->getRequest();

            $extra_message = '';
            if(false !== $response = json_decode($response->getBody())) {
                $extra_message = isset($response->error->message) ? $response->error->message . ';' : '';
            }

            throw new ClientException(
                "Can`t invoke yandex market method '$method': $extra_message " . $e->getMessage(),
                $e->getRequest(),
                $e->getResponse(),
                $e
            );
        }

        return \GuzzleHttp\json_decode($response->getBody());
    }

    public function exportToYml($outputFile, $shopName, $companyName, $companyUrl) {
        $categories = $this->sanitizeCategoriesForExport(Catalog::all());
        $categories_id = $categories->pluck('id');
        $products_name = [ ];

        $products = Product::where("deleted", 0)->get();

        $hars_text = $this->prepareHaracteristicsForExport(\App\Product::getProductHaracteristic());

        $writer = new \XMLWriter();
        $writer->openURI($outputFile);
        $writer->setIndent(true);

        $writer->startDocument('1.0', 'utf-8'); {
            $writer->startElement('yml_catalog'); {
                $writer->writeAttribute('date', date('Y-m-d H:i'));

                $writer->startElement('shop'); {
                    $writer->writeElement('name', $shopName);
                    $writer->writeElement('company', $companyName);
                    $writer->writeElement('url', $companyUrl);

                    $writer->startElement('currencies'); {
                        $writer->startElement('currency'); {
                            $writer->writeAttribute('id', 'RUR');
                            $writer->writeAttribute('rate', '1');
                        }
                        $writer->endElement();
                    }
                    $writer->endElement();

                    $writer->startElement('categories'); {
                        foreach($categories as $category) {
                            $writer->startElement('category'); {
                                $writer->writeAttribute('id', $category->id);
                                $writer->writeRaw(htmlspecialchars($category->name));
                            }
                            $writer->endElement();
                        }
                    }
                    $writer->endElement();

                    $writer->startElement('offers'); {
                        $i = 0;
                        foreach($products as $product) {
                            if(!$product->viewcost) continue;
                            if(empty($product->catalog_id)) continue;
                            if(false === $categories_id->search($product->catalog_id)) continue;
                            if(isset($products_name[$product->name])) continue;

                            $products_name[$product->name] = true;

                            $writer->startElement('offer'); {
                                $writer->writeAttribute('available', $product->product_in_stock() ? 'true' : 'false');

                                $writer->writeElement('url', $companyUrl . 'product_catalog/get/' . (!is_null($product->catalog) ? $product->catalog->alias : "_") . '/' . $product->alias);
                                $writer->writeElement('price', sprintf('%.02f', $product->getCostWithMargin()));
                                $writer->writeElement('currencyId', 'RUR');
                                $writer->writeElement('categoryId', $product->catalog->id);

                                foreach($product->getImageArr() as $k => $img) {
                                    if(!empty($img)) {
                                        $writer->writeElement('picture', $companyUrl . 'uploads/product/img' . ($k + 1) . '/' . $img);
                                    }
                                }

                                $writer->writeElement('name', $product->name);
                                $writer->writeElement('vendor', $product->brand()->first()->title);
                                if(!empty($product->article)) $writer->writeElement('vendorCode', $product->article);

                                $writer->startElement('description'); {
                                    $writer->writeCData($this->sanitizeDescriptionForExport($product->haracteristic));
                                }
                                $writer->endElement();

                                if(!empty($product->country)) {
                                    $country = $this->sanitizeCountryForExport(
                                        mb_convert_case($product->country, MB_CASE_TITLE, 'utf-8')
                                    );

                                    $writer->writeElement('country_of_origin', $country);
                                }

                                foreach($product->getPHs() as $value) {
                                    if(!isset($hars_text[$value->name])) continue;

                                    $writer->startElement('param'); {
                                        $writer->writeAttribute('name', $hars_text[$value->name]);
                                        $writer->writeRaw(htmlspecialchars($value->value));
                                    }
                                    $writer->endElement();
                                }

                            }
                            $writer->endElement();

                            //if($i++ > 500) break;
                        }
                    }
                    $writer->endElement();
                }
                $writer->endElement();
            }
            $writer->endElement();
        }
        $writer->endDocument();
    }

    protected function sanitizeCategoriesForExport($categories) {
        $ignore_list = [
            'мелкая бытовая техника',
            'посуда для приготовления',
            'дозаторы мыла',
            'кухонные смесители',
        ];

        return $categories->filter(function($cat) use ($ignore_list) {
            return !in_array(mb_strtolower($cat->name, 'utf-8'), $ignore_list);
        });
    }

    protected function sanitizeCountryForExport($country) {
        return str_replace([
            'Соединенные Штаты',
            'Соединенное Королевство',
            'Корея, Республика'
        ], [
            'США',
            'Великобритания',
            'Южная Корея',
        ], $country);
    }

    protected function sanitizeDescriptionForExport($desc) {
        $desc = html_entity_decode($desc, ENT_COMPAT | ENT_HTML401, 'utf-8');
        $desc = str_replace('&</li>', '</li>', $desc);

        return $desc;
    }

    protected function prepareHaracteristicsForExport($hars_text) {
        $hars_text['width'] = 'Ширина';
        unset($hars_text['device']);

        return $hars_text;
    }
}