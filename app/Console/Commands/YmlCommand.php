<?php

namespace App\Console\Commands;

use App\YaMarket;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;

class YmlCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'yml:export {outfile=-}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export catalog in yml format';

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

        /**
         * @var $yam YaMarket
         */
        $yam = App::make(YaMarket::class);

        $yam->exportToYml($output_file === '-' ? 'php://output' : $output_file);

        $this->info("Yml updated at " . date("r"));

        return 0;
    }
}
