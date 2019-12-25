<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dealer extends Model
{
    protected $table = 'cx_dealers';

    public $timestamps = false;

    protected $fillable = [
        'office_id',
        'dealers_name',
        'is_show',
        'create_time',
        'update_time',
        'delete_time',
    ];

    protected $casts = [
        'is_show' => 'boolean',
    ];

    public function office()
    {
        return $this->belongsTo(Office::class, 'office_id');
    }
}
