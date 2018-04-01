<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = "product";

    protected $guarded = [];

    public $timestamps = false;

    public function setFeaturesAttribute($value)
    {
        $this->attributes["features"] = json_encode($value);
    }

    public function getFeaturesAttribute($value)
    {
        return json_decode($value, false);
    }

    /**
     * Convert human-readable money format to a float
     *
     * @param $value
     */
    public function setPriceAttribute($value)
    {
        $this->attributes["price"] = (float)preg_replace("/([^0-9\\.])/i", "", $value);
    }

    public function getPriceAttribute($value)
    {
        return money_format('%.2n', $value);
    }

}
