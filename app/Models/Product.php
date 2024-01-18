<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table="products";
    public $primaryKey="id";
    public $incrementing=true;
    public $keyType="int";
    use HasFactory;

    public function  categories()
    {
        return $this->belongsTo(Category::class,'categories_id', 'id');
    }

}
