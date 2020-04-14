<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ShopifySync extends Command
{
    public $skuToIgnore = ['HP-600-M602-CE390A','HP-400-M401dn-CF280X','HP-P2035-CE505A','HP-400-M401dn-CF280X','HP-P2055dn-05A-CE505A','HP-600-M601-CE390A','HP-400-m401' ];
    
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'Shopify:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

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


        die("sync shopify");
    }
}
