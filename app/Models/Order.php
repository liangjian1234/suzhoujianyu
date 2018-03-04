<?php

namespace App\Models;

class Order extends Base
{
    protected $table = 'book_order';

    public function item()
    {
        return $this->hasMany('App\Models\OrderItem','order_id','id');
    }
}
