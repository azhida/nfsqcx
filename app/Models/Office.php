<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Office extends Model
{
    protected $table = 'cx_office';

    protected $fillable = [
        'name',
    ];
}
