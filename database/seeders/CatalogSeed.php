<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CatalogSeed extends Seeder
{
    public function run(): void
    {
        $colors = [
            ['name_en' => 'Black', 'name_kh' => 'ខ្មៅ', 'code' => '#000000'],
            ['name_en' => 'White', 'name_kh' => 'ស', 'code' => '#FFFFFF'],
            ['name_en' => 'Red', 'name_kh' => 'ក្រហម', 'code' => '#FF0000'],
            ['name_en' => 'Green', 'name_kh' => 'បៃតង', 'code' => '#008000'],
            ['name_en' => 'Blue', 'name_kh' => 'ខៀវ', 'code' => '#0000FF'],
            ['name_en' => 'Yellow', 'name_kh' => 'លឿង', 'code' => '#FFFF00'],
            ['name_en' => 'Orange', 'name_kh' => 'ទឹកក្រូច', 'code' => '#FFA500'],
            ['name_en' => 'Purple', 'name_kh' => 'ស្វាយ', 'code' => '#800080'],
            ['name_en' => 'Pink', 'name_kh' => 'ផ្កាឈូក', 'code' => '#FFC0CB'],
            ['name_en' => 'Gray', 'name_kh' => 'ប្រផេះ', 'code' => '#808080'],
            ['name_en' => 'Brown', 'name_kh' => 'ត្នោត', 'code' => '#A52A2A'],
            ['name_en' => 'Navy', 'name_kh' => 'ខៀវចាស់', 'code' => '#000080'],
            ['name_en' => 'Light Blue', 'name_kh' => 'ខៀវស្រាល', 'code' => '#ADD8E6'],
            ['name_en' => 'Dark Green', 'name_kh' => 'បៃតងចាស់', 'code' => '#006400'],
            ['name_en' => 'Gold', 'name_kh' => 'មាស', 'code' => '#FFD700'],
            ['name_en' => 'Silver', 'name_kh' => 'ប្រាក់', 'code' => '#C0C0C0'],
            ['name_en' => 'Beige', 'name_kh' => 'បន៍ត្នោតខ្ចី', 'code' => '#F5F5DC'],
            ['name_en' => 'Maroon', 'name_kh' => 'ក្រហមចាស់', 'code' => '#800000'],
            ['name_en' => 'Cream', 'name_kh' => 'ពណ៌ក្រែម', 'code' => '#FFFDD0'],
            ['name_en' => 'Mixed', 'name_kh' => 'ចម្រុះ', 'code' => null],
        ];

        foreach ($colors as $color) {
            DB::table('colors')->updateOrInsert(
                ['name_en' => $color['name_en']],
                [
                    'name_kh' => $color['name_kh'],
                    'code' => $color['code'],
                    'is_active' => true,
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }

        $sizes = [
            ['name_en' => 'XS', 'name_kh' => 'តូចបំផុត'],
            ['name_en' => 'S', 'name_kh' => 'តូច'],
            ['name_en' => 'M', 'name_kh' => 'មធ្យម'],
            ['name_en' => 'L', 'name_kh' => 'ធំ'],
            ['name_en' => 'XL', 'name_kh' => 'ធំ'],
            ['name_en' => 'XXL', 'name_kh' => 'ធំ'],
            ['name_en' => 'XXXL', 'name_kh' => 'ធំ'],
            ['name_en' => 'Free Size', 'name_kh' => 'ទំហំមួយ'],
            ['name_en' => 'Small', 'name_kh' => 'តូច'],
            ['name_en' => 'Medium', 'name_kh' => 'មធ្យម'],
            ['name_en' => 'Large', 'name_kh' => 'ធំ'],
            ['name_en' => '28', 'name_kh' => null],
            ['name_en' => '29', 'name_kh' => null],
            ['name_en' => '30', 'name_kh' => null],
            ['name_en' => '31', 'name_kh' => null],
            ['name_en' => '32', 'name_kh' => null],
            ['name_en' => '33', 'name_kh' => null],
            ['name_en' => '34', 'name_kh' => null],
            ['name_en' => '35', 'name_kh' => null],
            ['name_en' => '36', 'name_kh' => null],
            ['name_en' => '37', 'name_kh' => null],
            ['name_en' => '38', 'name_kh' => null],
            ['name_en' => '39', 'name_kh' => null],
            ['name_en' => '40', 'name_kh' => null],
            ['name_en' => '41', 'name_kh' => null],
            ['name_en' => '42', 'name_kh' => null],
            ['name_en' => '43', 'name_kh' => null],
            ['name_en' => '44', 'name_kh' => null],
            ['name_en' => '45', 'name_kh' => null],
        ];

        foreach ($sizes as $size) {
            DB::table('sizes')->updateOrInsert(
                ['name_en' => $size['name_en']],
                [
                    'name_kh' => $size['name_kh'],
                    'is_active' => true,
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }
    }
}
