<?php

namespace App\Model;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    public function wishes()
    {
        return $this->belongsToMany('App\Model\Product', 'wish', 'user_id', 'product_id');
    }

    /**
     * Users that I shared my list to
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function sharees()
    {
        return $this->belongsToMany('App\Model\User', 'share', 'sharer', 'sharee');
    }

    /**
     * Users that shared their lists to me
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function sharers()
    {
        return $this->belongsToMany('App\Model\User', 'share', 'sharee', 'sharer');
    }
}
