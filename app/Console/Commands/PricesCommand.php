<?php

namespace App\Console\Commands;

use App\Product;
use App\YaMarket;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;
use Maatwebsite\Excel\Facades\Excel;

class PricesCommand extends Command
{
    protected $HEADERS = [
        "Магазин",
        "Название товара в магазине",
        "Цена (руб)",
        "Доступность",
        "В домашнем регионе"
    ];

    const SHOP_NAME = 'Памира';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'yml:prices {outfile=-}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    protected static function getNameFromNumber($num) {
        $numeric = $num % 26;
        $letter = chr(65 + $numeric);
        $num2 = intval($num / 26);
        if ($num2 > 0) {
            return self::getNameFromNumber($num2 - 1) . $letter;
        } else {
            return $letter;
        }
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $output_file = $this->argument('outfile');
        $output_file === '-' ? 'php://output' : $output_file;

        $region_id = \Config::get('yandex-market.region_id');

        $yam = App::make(YaMarket::class);

        $products = Product::where("deleted", 0)
            ->where('export_to_yml', 1)
            ->get();

        $file_name = 'prices_' . date('d.m.Y H;i;s,u');

        $export_result = Excel::create($file_name, function($excel) use ($yam, $products, $region_id) {

            $excel->sheet('цены обновлены ' . date('d.m.y H;i'), function($sheet) use ($yam, $products, $region_id) {
                $sheet->setOrientation('landscape');

                $row = 1;
                $this->dumpHeader($sheet);
                $row++;


                $b = microtime(true);
                $i = 0;
                foreach ($products as $product) {
                    $prices = $yam->getSimilarPrices($product->name, $region_id);

                    $row = $this->dump($sheet, $row, $product, $prices, $region_id);

                    $i++;
                    if (0 === ($i % 1)) {
                        $e = microtime(true);
                        $mps = $i / ($e - $b);

                        $this->info(sprintf("%d completed in %.2f; speed = %.2f models per sec", $i, $e - $b, $mps));

                        if($i > 2) {
                            //break;
                        }
                    }
                }
                $e = microtime(true);

                $this->info("Done in " . ($e - $b) . ' s');
            });
        })->save('xls', storage_path('prices'), true);

        copy($export_result['full'], storage_path('prices') . '/last_prices.xls');

        return 0;
    }

    protected function getEndColumn() {
        return $this->getNameFromNumber(count($this->HEADERS) - 1);
    }

    /**
     * @param $sheet
     */
    protected function dumpHeader($sheet)
    {
        $headers = $this->HEADERS;
        $end_column = self::getEndColumn();

        $sheet->appendRow($headers);

        $sheet->setHeight(1, 50);
        $sheet->setAutosize(true);

        $sheet->cells('A1:' . $end_column . '1', function ($cells) {
            $cells->setBackground('#F3F3F3');

            $cells->setFont(array(
                'family' => 'Calibri',
                'size' => '14',
                'bold' => true
            ));

        });
        $sheet->setBorder('A1:' . $end_column . '1', 'thin');
    }

    protected function dump($sheet, $row, $product, $prices, $homeRegionId)
    {
        if(false === $prices) return $row;

        $end_column = self::getEndColumn();

        $sheet->appendRow([
            $product->name,
            "",
            "цена розн/опт",
            $product->getCostWithMargin(),
            $product->getCostWithMargin(true),
        ]);

        $sheet->mergeCells("A{$row}:B{$row}");

        $sheet->cells("A{$row}:B{$row}", function($cells) {
            $cells->setFont(array(
                'size'       => '20',
                'bold'       =>  true
            ));

            $cells->setAlignment('center');
        });

        $row++;

        foreach($prices->models as $model) {
            if(!count($model->offers)) continue;

            $sheet->appendRow([
                $model->name,
                "цена в Я.Маркет макс./средн./мин.",
                $model->prices->max,
                $model->prices->avg,
                $model->prices->min,
            ]);

            $sheet->cells("A{$row}:A{$row}", function($cells) {
                $cells->setFont(array(
                    'size'       => '16',
                    'bold'       =>  false
                ));

                $cells->setAlignment('center');
            });

            $row++;


            foreach ($model->offers as $offer) {
                if($offer->shopName === self::SHOP_NAME) continue;

                $sheet->appendRow([
                    $offer->shopName,
                    $offer->name,
                    $offer->price,
                    $offer->inStock ? 'В наличиии' : '',
                    $homeRegionId == $offer->regionId ? 'да' : ''
                ]);

                $sheet->cells("C{$row}:C{$row}", function($cells) use ($product, $offer) {
                    if($product->getCostWithMargin() > $offer->price) {
                        $cells->setBackground('#ffdbdb');
                    } else if($product->getCostWithMargin() < $offer->price) {
                        $cells->setBackground('#dbffdb');
                    }
                });


                $row++;
            }
        }

        $sheet->appendRow([""]);
        $row++;

        return $row;
    }
}
