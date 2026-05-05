<?php

namespace Database\Seeders;

use App\Models\Region;
use Illuminate\Database\Seeder;

class RegionSeeder extends Seeder
{
    public function run(): void
    {
        $regions = [
            ['name' => 'Челябинск', 'slug' => 'chelyabinsk', 'sort_order' => 1],
            ['name' => 'Москва', 'slug' => 'moskva', 'sort_order' => 2],
            ['name' => 'Санкт-Петербург', 'slug' => 'spb', 'sort_order' => 3],
            ['name' => 'Екатеринбург', 'slug' => 'ekaterinburg', 'sort_order' => 4],
            ['name' => 'Новосибирск', 'slug' => 'novosibirsk', 'sort_order' => 5],
        ];

        foreach ($regions as $region) {
            Region::create($region);
        }
    }
}