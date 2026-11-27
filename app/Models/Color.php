<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Color extends Model
{
    protected $table = 'colors';
    protected $fillable = ['id', 'name_en', 'name_kh', 'code', 'is_active', 'created_at', 'updated_at'];
}

