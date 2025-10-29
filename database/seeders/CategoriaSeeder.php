<?php

namespace Database\Seeders;


use Illuminate\Database\Seeder;
use App\Models\Categoria;

class CategoriaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $nomes = [
            'Pessoal',
            'Trabalho',
            'Estudos',
            'Casa',
            'Compras',
            'Saúde',
            'Finanças',
            'Projetos',
            'Lembretes',
            'Urgente',
        ];

        foreach ($nomes as $nome) {
            Categoria::firstOrCreate(['nome' => $nome]);
        }
    }
}
