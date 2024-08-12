<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model as Eloquent;
class User extends Eloquent
{
    protected $table = 'users';

    public function products(){
        return $this->hasMany(Product::class,'user_id');
    }

}
