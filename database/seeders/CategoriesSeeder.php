<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategoriesSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            ['name' => 'Vida Positiva', 'productId' => 'vida_positiva_mensal'],
            ['name' => 'Vida Positiva', 'productId' => 'vida_positiva_trimestral'],
            ['name' => 'Vida Positiva', 'productId' => 'vida_positiva_anual'],
            ['name' => 'Futuro Financeiro', 'productId' => 'futuro_financeiro_mensal'],
            ['name' => 'Futuro Financeiro', 'productId' => 'futuro_financeiro_trimestral'],
            ['name' => 'Futuro Financeiro', 'productId' => 'futuro_financeiro_anual'],
            ['name' => 'Cuidar & Crescer', 'productId' => 'cuidar_e_crescer_mensal'],
            ['name' => 'Cuidar & Crescer', 'productId' => 'cuidar_e_crescer_trimestral'],
            ['name' => 'Cuidar & Crescer', 'productId' => 'cuidar_e_crescer_anual'],
            ['name' => 'Conexão Íntima', 'productId' => 'conexao_intima_mensal'],
            ['name' => 'Conexão Íntima', 'productId' => 'conexao_intima_trimestral'],
            ['name' => 'Conexão Íntima', 'productId' => 'conexao_intima_anual'],
            ['name' => 'Evolução Diária', 'productId' => 'evolucao_diaria_mensal'],
            ['name' => 'Evolução Diária', 'productId' => 'evolucao_diaria_trimestral'],
            ['name' => 'Evolução Diária', 'productId' => 'evolucao_diaria_anual'],
            ['name' => 'Gratuito', 'productId' => 'gratuito'],
        ];

        foreach ($categories as $categorie) {
            Category::create($categorie);
        }
    }
}
