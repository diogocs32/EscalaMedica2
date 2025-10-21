<?php

namespace Database\Seeders;

use App\Models\Cidade;
use App\Models\Unidade;
use Illuminate\Database\Seeder;

class UnidadeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $olimpia = Cidade::where('nome', 'Olímpia')->first();
        $sjrp = Cidade::where('nome', 'São José do Rio Preto')->first();

        $unidades = [
            [
                'nome' => 'Telemedicina',
                'endereco' => 'xxxxxxxxx',
                'cidade_id' => $olimpia->id,
                'status' => 'ativo'
            ],
            [
                'nome' => 'Jaguare',
                'endereco' => 'fdfdasd',
                'cidade_id' => $sjrp->id,
                'status' => 'ativo'
            ],
            [
                'nome' => 'UPA de Olímpia',
                'endereco' => 'Rua Teste, 123',
                'cidade_id' => $olimpia->id,
                'status' => 'ativo'
            ],
            [
                'nome' => 'UPA Santo Antonio',
                'endereco' => 'Rua Tagliavini',
                'cidade_id' => $sjrp->id,
                'status' => 'ativo'
            ],
        ];

        foreach ($unidades as $unidade) {
            Unidade::firstOrCreate(
                [
                    'nome' => $unidade['nome'],
                    'cidade_id' => $unidade['cidade_id']
                ],
                $unidade
            );
        }
    }
}
