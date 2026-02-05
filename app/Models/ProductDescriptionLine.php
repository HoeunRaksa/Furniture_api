<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductDescriptionLine extends Model
{
    use HasFactory;

    protected $fillable = ['product_id', 'text', 'sort_order'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
