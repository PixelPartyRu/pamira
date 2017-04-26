<?php
namespace App;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Database\Eloquent\Collection;

class YaMarket {
    private $oauth_token;
    private $client_id;
    private $shop_name;
    private $company_name;
    private $company_url;

    function __construct($shop_name, $company_name, $company_url, $outhToken = null, $clientId = null)
    {
        $this->shop_name = $shop_name;
        $this->company_name = $company_name;
        $this->company_url = $company_url;
        $this->oauth_token = $outhToken;
        $this->client_id = $clientId;
    }

    public function getSimilarPrices($modelName, $regionId = null) {
        if(null === $regionId) {
            $regionId = \Config::get('yandex-market.region_id');
        }

        if(false === $result = $this->suggestModel($modelName, $regionId)) {
            return false;
        }

        foreach($result->models as $model) {
            $similar = $this->callMarketApi("models/{$model->id}/offers", [
                'regionId' => $regionId,
                'currency' => 'RUR',
            ]);

            $model->offers = isset($similar->models[0]->offers) ? $similar->models[0]->offers : null;
        }

        return $result;
    }

    protected function sanitizeModelName($modelName) {
        $to_remove = "
            холодильник однокамерный двухкамерный винный шкаф холодильная морозильная камера
            смеситель мойка миска лоток
            дозатор мыла
            измельчитель
            сортер
            духовой шкаф
            плита кухонная
            варочная поверхность
            Микроволновая печь
            вытяжка угольный фильтр
            Посудомоечная машина
            Сушильная машина Сушильный шкаф
            сковорода кастрюля
            чайник
        ";

        $to_remove = array_filter(array_map('trim', explode(" ", $to_remove)));

        $result = trim(preg_replace('/' . implode('|', array_map('preg_quote', $to_remove)) . '/iu', '', $modelName));

        //dd($modelName, $result);

        return $result;
    }

    protected function suggestModel($modelName, $regionId) {
        $modelName = $this->sanitizeModelName($modelName);
        if(empty($modelName)) {
            return false;
        }

        //var_dump("Try $modelName");

        $result = $this->callMarketApi('models', [
            'query' => $modelName,
            'regionId' => $regionId,
            'pageSize' => 10
        ]);

        if(!count($result->models)) {
            $new_model = trim(implode(' ', array_slice(explode(' ', $modelName), 0, -1)));
            return $this->suggestModel($new_model, $regionId);
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

        //var_dump($response->getHeaders());

        return \GuzzleHttp\json_decode($response->getBody());
    }

    public function exportToYml($outputFile) {
        $categories = $this->sanitizeCategoriesForExport(Catalog::all());
        $categories_id = $categories->pluck('id');
        $products_name = [ ];

        /**
         * @var $products Product[]
         */
        $products = Product::where("deleted", 0)
            ->where('export_to_yml', 1)
            ->get();

        $hars_text = $this->prepareHaracteristicsForExport(\App\Product::getProductHaracteristic());

        $writer = new \XMLWriter();
        $writer->openURI($outputFile);
        $writer->setIndent(true);

        $writer->startDocument('1.0', 'utf-8'); {
            $writer->startElement('yml_catalog'); {
                $writer->writeAttribute('date', date('Y-m-d H:i'));

                $writer->startElement('shop'); {
                    $writer->writeElement('name', $this->shop_name);
                    $writer->writeElement('company', $this->company_name);
                    $writer->writeElement('url', $this->company_url);

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

                                $writer->writeElement('url', $this->company_url . 'product_catalog/get/' . (!is_null($product->catalog) ? $product->catalog->alias : "_") . '/' . $product->alias);
                                $writer->writeElement('price', sprintf('%.02f', $product->getCostWithMargin()));
                                $writer->writeElement('currencyId', 'RUR');
                                $writer->writeElement('categoryId', $product->catalog->id);

                                foreach($product->getImageArr() as $k => $img) {
                                    if(!empty($img)) {
                                        $writer->writeElement('picture', $this->company_url . 'uploads/product/img' . ($k + 1) . '/' . $img);
                                    }
                                }

                                $writer->writeElement('name', $product->name);
                                $writer->writeElement('vendor', $product->brand()->first()->title);
                                if(!empty($product->article)) $writer->writeElement('vendorCode', $product->article);

                                $writer->startElement('description'); {
                                    $writer->writeCData($this->sanitizeDescriptionForExport($product->haracteristic));
                                }
                                $writer->endElement();

                                $sales_notes = $this->getSalesNotes($product);
                                if(!empty($sales_notes)) {
                                    $writer->startElement('sales_notes'); {
                                        $writer->writeCData($sales_notes);
                                    }
                                    $writer->endElement();
                                }

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

    private function getSalesNotes(Product $product)
    {
        return $product->product_in_stock() ? '' : 'Необходима предоплата 50%.';
    }
}