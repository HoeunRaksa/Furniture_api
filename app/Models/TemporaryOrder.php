<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TemporaryOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'invoice_no',
        'total_price',
        'items_json', // Stores the items array as JSON
        'tran_id',
        'shipping_address',
        'lat',
        'long',
        'phone_number',
        'shipping_charged',
        'method',
    ];

    protected $casts = [
        'items_json' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
