<?php

namespace Database\Seeders;

use App\Models\Subcategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SubcategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $subcategories = [
            ['category_id' => '1', 'productId' => 'vida_positiva_mensal'],
            ['category_id' => '1', 'productId' => 'vida_positiva_trimestral'],
            ['category_id' => '1', 'productId' => 'vida_positiva_anual'],
            ['category_id' => '2', 'productId' => 'futuro_financeiro_mensal'],
            ['category_id' => '2', 'productId' => 'futuro_financeiro_trimestral'],
            ['category_id' => '2', 'productId' => 'futuro_financeiro_anual'],
            ['category_id' => '3', 'productId' => 'cuidar_e_crescer_mensal'],
            ['category_id' => '3', 'productId' => 'cuidar_e_crescer_trimestral'],
            ['category_id' => '3', 'productId' => 'cuidar_e_crescer_anual'],
            ['category_id' => '4', 'productId' => 'conexao_intima_mensal'],
            ['category_id' => '4', 'productId' => 'conexao_intima_trimestral'],
            ['category_id' => '4', 'productId' => 'conexao_intima_anual'],
            ['category_id' => '5', 'productId' => 'evolucao_diaria_mensal'],
            ['category_id' => '5', 'productId' => 'evolucao_diaria_trimestral'],
            ['category_id' => '5', 'productId' => 'evolucao_diaria_anual'],
            ['category_id' => '6', 'productId' => 'gratuito'],
        ];

        foreach ($subcategories as $item) {
            Subcategory::create($item);
        }
    }
}
