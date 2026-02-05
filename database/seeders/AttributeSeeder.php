<?php

namespace Database\Seeders;

use App\Models\Attribute;
use Illuminate\Database\Seeder;

class AttributeSeeder extends Seeder
{
    public function run(): void
    {
        $attributes = [
            'Color' => ['Red', 'Blue', 'Black', 'White', 'Green', 'Grey', 'Wood', 'Oak', 'Walnut'],
            'Material' => ['Wood', 'Metal', 'Leather', 'Fabric', 'Plastic', 'Glass', 'Velvet'],
            'Size' => ['Small', 'Medium', 'Large', 'Extra Large', 'King Size', 'Queen Size'],
            'Condition' => ['New', 'Refurbished', 'Used'],
        ];

        foreach ($attributes as $name => $values) {
            $attr = Attribute::create(['name' => $name]);
            foreach ($values as $value) {
                $attr->values()->create(['value' => $value]);
            }
        }
    }
}
