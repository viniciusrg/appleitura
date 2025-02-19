<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategoriesSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Vida Positiva'],
            ['name' => 'Futuro Financeiro'],
            ['name' => 'Cuidar & Crescer'],
            ['name' => 'Conexão Íntima'],
            ['name' => 'Evolução Diária'],
            ['name' => 'Gratuito'],
        ];

        foreach ($categories as $categorie) {
            Category::create($categorie);
        }
    }
}
