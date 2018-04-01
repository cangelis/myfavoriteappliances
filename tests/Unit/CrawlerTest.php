<?php

namespace Tests\Unit;

use App\Model\DataSource\ProductCrawler;
use Tests\TestCase;
use Mockery as m;

class CrawlerTest extends TestCase
{

    public function tearDown()
    {
        m::close();
    }

    public function testAppendQueryAppendsCorrectly()
    {
        $crawler = new ProductCrawler(new \stdClass());
        $this->assertEquals($crawler->appendQuery("foo", "bar", "http://foo.bar/"), "http://foo.bar/?foo=bar");
        $this->assertEquals($crawler->appendQuery("foo", "bar", "http://foo.bar/?baz=bazzer"), "http://foo.bar/?baz=bazzer&foo=bar");
    }

    /**
     * @expectedException \App\Exceptions\ServiceNotAvailableException
     */
    public function testLoadThrowsExceptionWhen200NotReturn()
    {
        extract($this->makeRequest(""));
        $response->status_code = 500;
        (new ProductCrawler($request))->load("/");
    }

    public function makeRequest($body = null)
    {
        $request = m::mock('\Requests_Session');
        $response = m::mock('\Requests_Response');
        if ($body != null)
        {
            $response->body = $body;
        }
        $request->shouldReceive('get')->andReturn($response);
        return ['response' => $response, 'request' => $request];
    }

    /**
     * @expectedException \App\Exceptions\ServiceNotAvailableException
     */
    public function testLoadThrowsExceptionWhenCouldNotConnectToTheServer()
    {
        $request = m::mock('\Requests_Session');
        $request->shouldReceive('get')->andThrow(new \Requests_Exception("Could not connect to the server", ""));
        (new ProductCrawler($request))->load("/");
    }

    public function testLoadReturnsProductsAndPageNumberWhenStatusCodeIs200()
    {
        extract($this->makeRequest(""));
        $response->status_code = 200;
        $crawler = m::mock('App\Model\DataSource\ProductCrawler[getTotalPages, parseProducts]', [$request]);
        $crawler->shouldReceive("getTotalPages")->andReturn(1);
        $crawler->shouldReceive("parseProducts")->andReturn([]);
        $products = $crawler->load("/");
        $this->assertEquals($products["products"], []);
        $this->assertEquals($products["pages"], 1);
    }

    public function testParseProductsReturnsProductsWhenPageIsAsExpected()
    {
        $crawler = new ProductCrawler(new \stdClass());
        $products = $crawler->parseProducts(file_get_contents(__DIR__ . "/fixtures/products_response.html"));
        $this->assertEquals($products[0]["name"], "Samsung 800w Black Glass Built-In Combination Microwave Oven NQ50H5537KB");
        $this->assertEquals($products[0]["image_url"], "https://img.resized.co/appliancesdelivered/eyJkYXRhIjoie1widXJsXCI6XCJodHRwczpcXFwvXFxcL3MzLWV1LXdlc3QtMS5hbWF6b25hd3MuY29tXFxcL3N0b3JhZ2UuYnV5YW5kc2VsbC5pZVxcXC91cGxvYWRzXFxcLzMzNDhcXFwvNWEyN2ZlMGMyMjMzNC02YTMwZjU0MGJjOGQ5NmJmODk0MjkyMmM5MmRhMGE5MlwiLFwid2lkdGhcIjoyNTAsXCJoZWlnaHRcIjpcIlwiLFwiZGVmYXVsdFwiOlwiaHR0cHM6XFxcL1xcXC9zMy1ldS13ZXN0LTEuYW1hem9uYXdzLmNvbVxcXC9zdG9yYWdlLmJ1eWFuZHNlbGwuaWVcXFwvYXBwbGlhbmNlcy1kZWxpdmVyZWQtbm9pbWFnZS5wbmdcIn0iLCJoYXNoIjoiMzlhZTAwMTg0OGVhZjE2Mjg3N2NlYTk2YjYwMWU0YmYyNWNmOWRlMCJ9/samsung-800w-black-glass-built-in-combination-microwave-oven-nq50h5537kb");
        $this->assertEquals($products[0]["brand_image_url"], "https://img.resized.co/appliancesdelivered/eyJkYXRhIjoie1widXJsXCI6XCJodHRwczpcXFwvXFxcL3MzLWV1LXdlc3QtMS5hbWF6b25hd3MuY29tXFxcL3N0b3JhZ2UuYnV5YW5kc2VsbC5pZVxcXC9jb250ZW50XFxcL2JyYW5kc1xcXC9pbWFnZXNcXFwvYmNkZGE5Yzc4N2M3YjcyMDRlZTcxMmFiNThhMjAyYWJzYW1zdW5nZDllZTM0YjY5NDA3Y2I3ZTk1NmRlY2I5NjU4NWRkMTMuanBnXCIsXCJ3aWR0aFwiOjEwMCxcImhlaWdodFwiOlwiXCIsXCJkZWZhdWx0XCI6XCJodHRwczpcXFwvXFxcL3MzLWV1LXdlc3QtMS5hbWF6b25hd3MuY29tXFxcL3N0b3JhZ2UuYnV5YW5kc2VsbC5pZVxcXC9hcHBsaWFuY2VzLWRlbGl2ZXJlZC1ub2ltYWdlLnBuZ1wifSIsImhhc2giOiI3MzFhN2IxYjE4YmYwZWQ2MjY3YjkxOWY4Y2M3OTIwZWQ4YjBlODAwIn0=/bcdda9c787c7b7204ee712ab58a202absamsungd9ee34b69407cb7e956decb96585dd13.jpg");
        $this->assertEquals($products[0]["id"], 3348);
        $this->assertTrue(in_array("Brand: Samsung", $products[0]["features"]));
        $this->assertTrue(in_array("Power: 800W", $products[0]["features"]));
        $this->assertTrue(in_array("Capacity: 50L", $products[0]["features"]));
        $this->assertTrue(in_array("Type: Combination Microwave", $products[0]["features"]));
        $this->assertEquals($products[0]["price"], "999.95");
        $this->assertEquals($products[0]["url"], "https://www.appliancesdelivered.ie/samsung-800w-black-glass-built-in-combination-microwave-oven-nq50h5537kb/3348");
    }

    public function testParseProductsReturnsEmptyArrayWhenPageIsNotAsExpected()
    {
        $crawler = new ProductCrawler(new \stdClass());
        $products = $crawler->parseProducts("<foo>bar</foo><baz>bazzer</baz>");
        $this->assertEquals($products, []);
    }

    public function testTotalPagesReturnOneWhenNoPagesExistOnThePage()
    {
        $crawler = new ProductCrawler(new \stdClass());
        $this->assertEquals($crawler->getTotalPages(""), 1);
    }

    public function testTotalPagesReturnOneWhenOnlyOnePageAvailable()
    {
        $crawler = new ProductCrawler(new \stdClass());
        $this->assertEquals($crawler->getTotalPages('<a href="http://foobar.com/?page=1"></a>'), 1);
    }

    public function testTotalPagesReturnsMaximumWhenMorePagesAvailable()
    {
        $crawler = new ProductCrawler(new \stdClass());
        $response = <<<FOO
<a href="http://foobar.com/?page=1">1</a>
<a href="http://foobar.com/?page=2">2/a>
<a href="http://foobar.com/?page=3">3</a>
<a href="http://foobar.com/?page=21">last</a>
FOO;
        $this->assertEquals($crawler->getTotalPages($response), 21);
    }

    public function testTotalPagesDetectPageNumberWithAmpQuery()
    {
        $crawler = new ProductCrawler(new \stdClass());
        $response = <<<FOO
<a href="http://foobar.com/?q=a&page=1">1</a>
<a href="http://foobar.com/?q=a&page=2">2/a>
<a href="http://foobar.com/?q=a&page=3">3</a>
FOO;
        $this->assertEquals($crawler->getTotalPages($response), 3);
    }

}
