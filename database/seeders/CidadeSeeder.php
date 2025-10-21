<?php

namespace Database\Seeders;

use App\Models\Cidade;
use Illuminate\Database\Seeder;

class CidadeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cidades = [
            ['nome' => 'Olímpia'],
            ['nome' => 'São José do Rio Preto'],
        ];

        foreach ($cidades as $cidade) {
            Cidade::firstOrCreate(
                ['nome' => $cidade['nome']],
                $cidade
            );
        }
    }
}
