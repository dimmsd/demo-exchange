<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Currency;
use App\User;

class Order extends Model
{
    protected $fillable = ['currency_code_from', 'currency_code_to', 'user_id', 'summa', 'value_from', 'value_to'];

    public function currency_from()
    {
        return $this->hasOne(Currency::class, 'code', 'currency_code_from');
    }

    public function currency_to()
    {
        return $this->hasOne(Currency::class, 'code', 'currency_code_to');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
