<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategoriesSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Vida Positiva',
            'Futuro Financeiro',
            'Cuidar & Crescer',
            'Conexão Íntima',
            'Evolução Diária',
            'Gratuito'
        ];

        $productId = [
            'com.app.livall',
            'teste',
            'teste',
            'teste',
            'teste',
            'teste',
        ];

        foreach ($categories as $index => $category) {
            Category::create([
                'name' => $category,
                'productId' => $productId[$index], // Associa o productId correspondente
            ]);
        }
    }
}
