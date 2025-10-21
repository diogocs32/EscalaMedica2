<?php

namespace Database\Seeders;

use App\Models\Turno;
use Illuminate\Database\Seeder;

class TurnoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $turnos = [
            [
                'nome' => 'Corujão',
                'hora_inicio' => '00:00:00',
                'hora_fim' => '07:00:00',
                'duracao_horas' => 7,
                'periodo' => 'noturno',
                'status' => 'ativo'
            ],
            [
                'nome' => 'Manhã',
                'hora_inicio' => '07:00:00',
                'hora_fim' => '13:00:00',
                'duracao_horas' => 6,
                'periodo' => 'diurno',
                'status' => 'ativo'
            ],
            [
                'nome' => 'Suporte Manhã',
                'hora_inicio' => '09:00:00',
                'hora_fim' => '15:00:00',
                'duracao_horas' => 6,
                'periodo' => 'diurno',
                'status' => 'ativo'
            ],
            [
                'nome' => 'Tarde',
                'hora_inicio' => '13:00:00',
                'hora_fim' => '19:00:00',
                'duracao_horas' => 6,
                'periodo' => 'diurno',
                'status' => 'ativo'
            ],
            [
                'nome' => 'Suporte Tarde',
                'hora_inicio' => '15:00:00',
                'hora_fim' => '21:00:00',
                'duracao_horas' => 6,
                'periodo' => 'diurno',
                'status' => 'ativo'
            ],
            [
                'nome' => 'Apoio',
                'hora_inicio' => '19:00:00',
                'hora_fim' => '00:00:00',
                'duracao_horas' => 5,
                'periodo' => 'noturno',
                'status' => 'ativo'
            ],
        ];

        foreach ($turnos as $turno) {
            Turno::firstOrCreate(
                ['nome' => $turno['nome']],
                $turno
            );
        }
    }
}
