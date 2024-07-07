<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = ['Empreendedorismo', 'Desenvolvimento pessoal', 'Romance', 'TDAH', 'Criptomoedas'];

        foreach ($categories as $category) {
            Category::create(['name' => $category]);
        }
    }
}
