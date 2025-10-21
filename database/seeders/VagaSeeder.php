<?php

namespace Database\Seeders;

use App\Models\Unidade;
use App\Models\Setor;
use App\Models\Turno;
use App\Models\Vaga;
use Illuminate\Database\Seeder;

class VagaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buscar entidades
        $unidades = Unidade::all();
        $setores = Setor::all();
        $turnos = Turno::all();

        // Definir combinações específicas baseadas nas imagens
        $combinacoes = [
            // Jaguare
            ['unidade' => 'Jaguare', 'setor' => 'Emergência', 'turnos' => ['Corujão', 'Manhã', 'Tarde', 'Apoio']],
            ['unidade' => 'Jaguare', 'setor' => 'Enfermaria', 'turnos' => ['Corujão', 'Manhã', 'Tarde', 'Apoio']],
            ['unidade' => 'Jaguare', 'setor' => 'Porta', 'turnos' => ['Corujão', 'Manhã', 'Tarde', 'Apoio']],

            // Telemedicina
            ['unidade' => 'Telemedicina', 'setor' => 'Teleconsulta', 'turnos' => ['Manhã', 'Tarde']],

            // UPA de Olímpia
            ['unidade' => 'UPA de Olímpia', 'setor' => 'PS Adulto', 'turnos' => ['Corujão', 'Manhã', 'Tarde', 'Apoio']],
            ['unidade' => 'UPA de Olímpia', 'setor' => 'PS Infantil', 'turnos' => ['Corujão', 'Manhã', 'Tarde', 'Apoio']],

            // UPA Santo Antonio
            ['unidade' => 'UPA Santo Antonio', 'setor' => 'Consultorio', 'turnos' => ['Manhã', 'Tarde', 'Apoio']],
            ['unidade' => 'UPA Santo Antonio', 'setor' => 'Emergência', 'turnos' => ['Corujão', 'Manhã', 'Tarde', 'Apoio']],
            ['unidade' => 'UPA Santo Antonio', 'setor' => 'Enfermaria', 'turnos' => ['Corujão', 'Manhã', 'Tarde', 'Apoio']],
        ];

        foreach ($combinacoes as $combo) {
            $unidade = $unidades->where('nome', $combo['unidade'])->first();
            $setor = $setores->where('nome', $combo['setor'])->first();

            if (!$unidade || !$setor) {
                continue;
            }

            foreach ($combo['turnos'] as $nometurno) {
                $turno = $turnos->where('nome', $nometurno)->first();

                if ($turno) {
                    Vaga::firstOrCreate(
                        [
                            'unidade_id' => $unidade->id,
                            'setor_id' => $setor->id,
                            'turno_id' => $turno->id,
                        ],
                        [
                            'quantidade_necessaria' => 1,
                            'observacoes' => 'Vaga criada automaticamente pelo seeder',
                            'status' => 'ativo'
                        ]
                    );
                }
            }
        }
    }
}
