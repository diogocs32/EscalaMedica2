<?php

namespace Database\Seeders;

use App\Models\Setor;
use Illuminate\Database\Seeder;

class SetorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $setores = [
            [
                'nome' => 'Teleconsulta',
                'descricao' => 'Atendimento médico à distância',
                'unidade_id' => 1,
                'status' => 'ativo'
            ],
            [
                'nome' => 'Emergência',
                'descricao' => 'Atendimento de emergência médica',
                'unidade_id' => 1,
                'status' => 'ativo'
            ],
            [
                'nome' => 'Enfermaria',
                'descricao' => 'Cuidados de enfermagem',
                'unidade_id' => 1,
                'status' => 'ativo'
            ],
            [
                'nome' => 'Porta',
                'descricao' => 'Portaria e recepção',
                'unidade_id' => 2,
                'status' => 'ativo'
            ],
            [
                'nome' => 'Consultorio',
                'descricao' => 'Consultas médicas ambulatoriais',
                'unidade_id' => 2,
                'status' => 'ativo'
            ],
            [
                'nome' => 'PS Adulto',
                'descricao' => 'Pronto Socorro Adulto',
                'unidade_id' => 3,
                'status' => 'ativo'
            ],
            [
                'nome' => 'PS Infantil',
                'descricao' => 'Pronto Socorro Infantil',
                'unidade_id' => 3,
                'status' => 'ativo'
            ],
        ];

        foreach ($setores as $setor) {
            Setor::firstOrCreate(
                ['nome' => $setor['nome']],
                $setor
            );
        }
    }
}
