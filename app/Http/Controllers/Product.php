<?php

namespace App\Http\Controllers;


use App\Model\User;
use Illuminate\Auth\Access\AuthorizationException;

class Product extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Index page, displays all the products
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        return view("product_list")->with("products", $this->getProductQuery()->paginate(15));
    }

    /**
     * Filter the products by the category id
     *
     * @param $categoryId
     * @return \Illuminate\Contracts\View\View
     */
    public function filterByCategory($categoryId)
    {
        return view("product_list")->with("products",
            $this->getProductQuery()->where('category_id', $categoryId)->paginate(15))
            ->with("title", $this->getCategory($categoryId)["title"]);
    }

    /**
     * Display user's wishes
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function wishes()
    {
        return view("product_list")->with("products", $this->getProductQuery(\Auth::getUser()->wishes())->paginate(15))
            ->with('title', "My Wish List")->with('shareable', true);
    }

    /**
     * Display a shared list
     *
     * @param $sharerId
     * @throws \Exception
     * @return \Illuminate\Contracts\View\View
     */
    public function seeShare($sharerId)
    {
        $sharer = User::findOrFail($sharerId);
        $this->authShare($sharer);
        return view('product_list')->with('products', $this->getProductQuery($sharer->wishes())->paginate(15))
            ->with('title', "Wish List of " . $sharer->name)->with('shared_list', true)->with('sharer', $sharer);
    }

    /**
     * Add the product to the user's wish list
     *
     * @param $productId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function addList($productId)
    {
        $product = \App\Model\Product::findOrFail($productId);
        \Auth::getUser()->wishes()->attach($product);
        $this->info("Added to the wish list!");
        return redirect()->to(\URL::previous() . "#product-" . $product->id);
    }


    /**
     * Remove product from user's wish list
     *
     * @param $productId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function removeList($productId)
    {
        $product = \App\Model\Product::findOrFail($productId);
        \Auth::getUser()->wishes()->detach($product);
        $this->info("Removed from the wishlist!");
        return redirect()->to(\URL::previous() . "#product-" . $product->id);
    }

    /**
     * Remove a product from a shared wish list
     *
     * @param $productId
     * @param $sharerId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function removeShared($productId, $sharerId)
    {
        $sharer = User::findOrFail($sharerId);
        $this->authShare($sharer);
        $product = \App\Model\Product::findOrFail($productId);
        $sharer->wishes()->detach($product);
        $this->info("Removed from the wishlist!");
        return redirect()->to(\URL::previous() . "#product-" . $product->id);
    }

    protected function authShare($sharer)
    {
        if (!\Auth::getUser()->sharers->contains($sharer))
        {
            throw new AuthorizationException();
        }
    }

    /**
     * Generate query for ordering items
     *
     * @param \App\Model\Product $query
     *
     * @return \App\Model\Product|mixed
     * @throws \Exception
     */
    protected function getProductQuery($query = null)
    {
        if (is_null($query))
        {
            $query = \App::make('\App\Model\Product');
        }
        if (!is_null(\Request::get("order_by", null)))
        {
            list($column, $order) = explode("_", \Request::get("order_by"));
            if ((!in_array($column, ["price", "name"])) || (!in_array($order, ["asc", "desc"])))
            {
                throw new \Exception("Invalid order request");
            }
            $query = $query->orderBy($column, $order);
        }
        else {
            $query = $query->orderBy("name", "ASC");
        }
        return $query;
    }

    /**
     * Get the category information as array
     *
     * @param $id
     * @return array
     */
    protected function getCategory($id)
    {
        foreach (config("app.product_categories") as $category)
        {
            if ($category["id"] == $id)
            {
                return $category;
            }
        }
        return null;
    }
}
