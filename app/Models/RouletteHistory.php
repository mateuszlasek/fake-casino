<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RouletteHistory extends Model
{
    protected $table = 'roulette_history';
    protected $fillable = ['color'];
}
