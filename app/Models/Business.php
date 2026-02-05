<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Business extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'logo', 'mobile', 'email', 'address', 'city', 'country', 
        'postal_code', 'currency', 'currency_symbol', 'tax_rate', 'tax_name', 
        'tax_enabled', 'timezone', 'date_format', 'time_format', 'footer_text', 
        'website', 'facebook', 'instagram', 'telegram'
    ];

    protected $casts = [
        'tax_enabled' => 'boolean',
        'tax_rate' => 'decimal:2',
    ];

    public function getLogoUrlAttribute(): ?string
    {
        if ($this->logo) {
            return asset('uploads/business/' . $this->logo);
        }
        return null;
    }
}
