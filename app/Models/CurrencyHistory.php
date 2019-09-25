<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Currency;

class CurrencyHistory extends Model
{
    protected $fillable = ['currency_code', 'value', 'request_at'];

    public function currency()
    {
        return $this->hasOne(Currency::class, 'code', 'currency_code');
    }
}
