<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Size extends Model
{
    protected $table = 'sizes';
    protected $fillable = ['id', 'name_en', 'name_kh', 'is_active', 'created_at', 'updated_at'];
}
