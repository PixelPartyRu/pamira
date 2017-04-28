<?php

namespace App\Console\Commands;

use App\Product;
use App\YaMarket;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;

class PricesCommand extends Command
{
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

        $fh = fopen($output_file, 'w');
        fputs($fh, "\xEF\xBB\xBF"); // utf-8 bom for excel

        $this->writeRow($fh, ["Название товара в pamira.ru", "Модель товара(в Яндекс.Маркет)", "Магазин", "Название товара в магазине", "Цена (руб)", "Доступность", "В домашнем регионе"]);
        $this->writeRow($fh, ["Дата обновления", date('d.m.Y H:i') . ' UTC', "", "", "", "", ""]);
        $this->writeRow($fh, ["", "", "", "", "", "", ""]);

        try {
            $yam = App::make(YaMarket::class);

            $products = Product::where("deleted", 0)
                ->where('export_to_yml', 1)
                ->get();

            $b = microtime(true);
            $i = 0;
            foreach ($products as $product) {
                $prices = $yam->getSimilarPrices($product->name, $region_id);

                $this->dump($fh, $product, $prices, $region_id);

                $i++;
                if (0 === ($i % 5)) {
                    $e = microtime(true);
                    $mps = $i / ($e - $b);

                    $this->info(sprintf("%d completed in %.2f; speed = %.2f models per sec\n", $i, $e - $b, $mps));

                    if($i > 1000) {
                        //break;
                    }
                }
            }
            $e = microtime(true);

            $this->info("Done in " . ($e - $b) . ' s');
        } finally {
            fclose($fh);
        }

        return 0;
    }

    protected function dump($fh, $product, $prices, $homeRegionId)
    {
        if(false === $prices) return false;

        foreach($prices->models as $model) {
            foreach ($model->offers as $offer) {
                $this->writeRow($fh, [
                    $product->name,
                    $model->name,
                    $offer->shopName,
                    $offer->name,
                    $offer->price,
                    $offer->inStock ? 'В наличиии' : 'Нет в наличии',
                    $homeRegionId == $offer->regionId ? 'да' : 'нет'
                ]);
            }
        }

        $this->writeRow($fh, ["", "", "", "", "", "", ""]);
    }

    protected function writeRow($fh, $row)
    {
        foreach ($row as &$v) {
            if ($row[0] === '-' || $row[0] === '+' || $row[0] === '=') {
                $v = " $v";
            }
        }

        fputcsv($fh, $row, ';');
    }
}
