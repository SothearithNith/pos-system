<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $table = 'categories';
    protected $fillable = ['id', 'client_id', 'parent_id', 'name_en', 'name_kh', 'is_active', 'created_at', 'updated_at'];
}
