<?php

namespace Database\Seeders;

use App\Models\ItemCategory;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class DefaultCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $defaultCategories = ['Food', 'Electronics/Others', 'Land'];

        // check if the categories already exist
        foreach ($defaultCategories as $category) {
            $categoryExists = ItemCategory::query()->where('name', $category)->first();

            if (!$categoryExists) {
                ItemCategory::create([
                    'name' => $category,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]);
            }
        }

    }
}
