<?php

namespace Database\Seeders;

use App\Http\Helpers\MainHelper;
use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $mainHelper = new MainHelper();
        $data = $mainHelper->csv_to_array('categories.csv');
        foreach ($data as $row) {
            Category::create([
                'name' => $row['name'],
            ]);
        }
    }
}
