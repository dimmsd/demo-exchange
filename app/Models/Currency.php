<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class Currency extends Model
{
    public $incrementing = false;

    public $timestamps = false;

    protected $primaryKey = ['code'];

    protected $fillable = ['code', 'name', 'nominal', 'iso_num_code', 'iso_char_code'];

    protected function setKeysForSaveQuery(Builder $query)
    {
        return $query->where('code', $this->getAttribute('code'));
    }
}
