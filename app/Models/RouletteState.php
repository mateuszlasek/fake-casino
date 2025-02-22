<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RouletteState extends Model
{
    protected $fillable = ['spinning', 'winning_number', 'randomize', 'start_time'];
}
