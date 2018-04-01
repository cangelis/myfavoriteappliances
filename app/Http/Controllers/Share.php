<?php

namespace App\Http\Controllers;

use App\Model\User;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class Share extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display and manage users that I shared my wish list to
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        return view('share');
    }

    /**
     * Share the wish list with someone
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function newSharee()
    {
        try
        {
            // Dont share with yourself it is schizophrenic
            if (\Request::get('email') == \Auth::getUser()->email)
            {
                throw new ModelNotFoundException();
            }
            $sharee = User::where('email', \Request::get('email'))->firstOrFail();
            \Auth::getUser()->sharees()->attach($sharee);
            $this->info('Shared successfully!');
        }
        catch (ModelNotFoundException $e)
        {
            $this->error('User with this email could not be found');
        }
        return redirect()->back();
    }

    /**
     * Unshare the wish list
     *
     * @param $shareeId
     * @return \Illuminate\Http\RedirectResponse
     */
    public function removeSharee($shareeId)
    {
        $sharee = User::findOrFail($shareeId);
        \Auth::getUser()->sharees()->detach($sharee);
        $this->info('Share removed successfully!');
        return redirect()->back();
    }

    /**
     * Display user's sharers
     *
     * @return \Illuminate\Contracts\View\View
     */
    public function sharers()
    {
        return view('sharer')->with('sharers', \Auth::getUser()->sharers);
    }

}
