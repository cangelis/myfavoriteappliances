<?php
namespace App\Model\DataSource;

use App\Exceptions\ServiceNotAvailableException;

interface ProductListDataSourceInterface {

    /**
     * Returns the array of items and total page count
     *
     * Example return;
     *
     * [
     *  'pages' => 27,
     *  'products' => [
     *      ['name' => 'Beko Dishwasher', 'id' => 0, 'product_url' => '...', ...],
     *      ['name' => 'Samsung Dishwasher', 'id' => 1, 'product_url' => '...', ...]
     *   ]
     * ]
     *
     * @param $path
     * @param int $page
     * @throws ServiceNotAvailableException
     * @return array
     */
    public function load($path, $page=1);

}