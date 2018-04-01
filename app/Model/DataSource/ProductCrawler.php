<?php
namespace App\Model\DataSource;

use App\Exceptions\ServiceNotAvailableException;

class ProductCrawler implements ProductListDataSourceInterface {

    /**
     * @var \Requests_Session
     */
    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    /**
     * Returns the array of items and total page count
     *
     * Example return;
     *
     * [
     *  'pages' => 27,
     *  'products' => [
     *      ['name' => 'Beko Dishwasher', 'id' => 0, 'url' => '...', ...],
     *      ['name' => 'Samsung Dishwasher', 'id' => 1, 'url' => '...', ...]
     *   ]
     * ]
     *
     * @param $path
     * @param int $page
     * @throws ServiceNotAvailableException
     * @return array
     */
    public function load($path, $page = 1)
    {
        try {
            $response = $this->request->get($this->appendQuery("page", $page, config("app.crawler_base_url") . $path));
            if ($response->status_code != 200)
            {
                throw new ServiceNotAvailableException($response->body, $response->status_code);
            }
        }
        catch (\Requests_Exception $e)
        {
            throw new ServiceNotAvailableException($e->getMessage(), 1);
        }

        return ['products' => $this->parseProducts($response->body), 'pages' => $this->getTotalPages($response->body)];
    }

    /**
     * Parses the HTTP response and returns the total number of pages for the given category
     *
     * @param $body
     * @return int
     */
    public function getTotalPages($body)
    {
        $pattern = '/\<a href=".+?(&|\?|&amp;)page=(?<page_number>[0-9]+)"\>.+?\<\/a\>/';
        preg_match_all($pattern, $body, $matches);
        $pageNumberCount = 1;
        foreach ($matches["page_number"] as $pageNumber)
        {
            $pageNumberCount = max(intval($pageNumber), $pageNumberCount);
        }
        return $pageNumberCount;
    }

    /**
     * Parses the HTTP Response and returns array of product informations
     *
     * Example return value:
     *
     * [
     *  ['name' => 'Beko Dishwasher', 'id' => 0, 'url' => '...', ...],
     *  ['name' => 'Samsung Dishwasher', 'id' => 1, 'url' => '...', ...]
     * ]
     *
     * @param $body
     * @return array
     */
    public function parseProducts($body)
    {
        $pattern = <<<FOO
/\<div class="search-results-product row"\>.+?\<div class="product-image col-xs-4 col-sm-4"\>
.+?\<a href='.+?\/(?<id>[0-9]+)'\>
.+?<img class="img-responsive" src="(?<image_url>.+?)".+?\>
.+?\<div class="product-description col-xs-8 col-sm-8"\>.+?<img class="article-brand" src="(?<brand_image_url>.+?)".+?\/>
.+?<a[\s]href='(?<url>.+?)'\>(?<name>.+?)\<\/a\>\<\/h4\>
.+?\<ul class="result-list-item-desc-list hidden-xs"\>(?<features>.+?)\<\/ul\>
.+?\<h3 class="section-title"\>&euro;(?<price>.+?)\<\/h3\>/is
FOO;
        preg_match_all($pattern, $body, $matches);
        $result = [];
        foreach ($matches[0] as $matchNumber=>$value)
        {
            $itemData = [];
            foreach (["name", "id", "brand_image_url", "url", "price", "image_url"] as $itemIndex)
            {
                $itemData[$itemIndex] = $matches[$itemIndex][$matchNumber];
            }
            preg_match_all('/\<li\>(.+?)\<\/li\>/', $matches["features"][$matchNumber], $features);
            $itemData["features"] = array_splice($features, 1)[0];
            array_push($result, $itemData);
        }
        return $result;
    }

    /**
     * Appends query key value pair to the given path by appending "&<key>=<value>" or "?<key>=<value>"
     *
     * @param $key
     * @param $value
     * @param $base_url
     * @return string
     */
    public function appendQuery($key, $value, $base_url)
    {
        $prepend = "?";
        if (strpos($base_url, '?') !== false)
        {
            $prepend = '&';
        }
        return $base_url . $prepend . $key . "=" . $value;
    }

}