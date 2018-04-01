<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;


    /**
     * Display flash message
     *
     * @param $class
     * @param $message
     */
    public function flashMessage($class, $message)
    {
        \Session::flash("class", $class);
        \Session::flash("message", $message);
    }

    /**
     * Display info flash message
     *
     * @param $message
     */
    public function info($message)
    {
        $this->flashMessage("alert-info", $message);
    }

    /**
     * Display error flash message
     *
     * @param $message
     */
    public function error($message)
    {
        $this->flashMessage("alert-danger", $message);
    }
}
