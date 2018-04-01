<?php

namespace App\Console\Commands;

use App\Exceptions\ServiceNotAvailableException;
use App\Model\DataSource\ProductListDataSourceInterface;
use App\Model\Product;
use Illuminate\Console\Command;
use Symfony\Component\Console\Output\OutputInterface;

class SyncItems extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync_items';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync Items from the API';

    /**
     * @var ProductListDataSourceInterface
     */
    protected $itemList;

    protected $product;

    /**
     * SyncItems constructor.
     * @param ProductListDataSourceInterface $itemList
     */
    public function __construct(ProductListDataSourceInterface $itemList)
    {
        parent::__construct();
        $this->itemList = $itemList;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->output->setVerbosity(OutputInterface::VERBOSITY_VERY_VERBOSE);
        foreach (config("app.product_categories") as $category)
        {
            $currentPage = 1;
            $pages = 1;
            $this->info("Fetching from url: " . $category["path"]);
            while ($pages >= $currentPage)
            {
                $this->info("Processing page #" . $currentPage . "...");
                try
                {
                    extract($this->itemList->load($category["path"], $currentPage));
                }
                catch (ServiceNotAvailableException $e)
                {
                    $this->error("Cannot connect to the service" . $e->getMessage());
                    return 1;
                }
                foreach ($products as $product) {
                    $product["category_id"] = $category["id"];
                    $this->info("Saving product to database: " . $product["name"]);
                    Product::updateOrCreate(['id' => $product["id"]], $product);
                }
                $currentPage++;
                sleep(0.5);
            }
        }
        return 0;
    }
}
