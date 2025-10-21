<?php

namespace Database\Seeders;

use App\Models\Plantonista;
use Illuminate\Database\Seeder;

class PlantonistaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plantonistas = [
            [
                'nome' => 'Ana Oliveira',
                'crm' => '456789',
                'email' => 'ana.oliveira@hospital.com',
                'telefone' => '(17) 99999-9999',
                'status' => 'ativo'
            ],
            [
                'nome' => 'Carlos Souza',
                'crm' => '567890',
                'email' => 'carlos.souza@hospital.com',
                'telefone' => '(17) 99999-9999',
                'status' => 'ativo'
            ],
            [
                'nome' => 'diogo cabral',
                'crm' => '213371',
                'email' => 'diogoccs32@gmail.com',
                'telefone' => '17988340999',
                'status' => 'ativo'
            ],
            [
                'nome' => 'João Silva',
                'crm' => '123456',
                'email' => 'joao.silva@hospital.com',
                'telefone' => '(17) 99999-9999',
                'status' => 'ativo'
            ],
            [
                'nome' => 'Julia Lima',
                'crm' => '678901',
                'email' => 'julia.lima@hospital.com',
                'telefone' => '(17) 99999-9999',
                'status' => 'ativo'
            ],
            [
                'nome' => 'Maria Santos',
                'crm' => '234567',
                'email' => 'maria.santos@hospital.com',
                'telefone' => '(17) 99999-9999',
                'status' => 'ativo'
            ],
            [
                'nome' => 'Patricia Rocha',
                'crm' => '890123',
                'email' => 'patricia.rocha@hospital.com',
                'telefone' => '(17) 99999-9999',
                'status' => 'ativo'
            ],
            [
                'nome' => 'Pedro Costa',
                'crm' => '345678',
                'email' => 'pedro.costa@hospital.com',
                'telefone' => '(17) 99999-9999',
                'status' => 'ativo'
            ],
            [
                'nome' => 'Roberto Dias',
                'crm' => '789012',
                'email' => 'roberto.dias@hospital.com',
                'telefone' => '(17) 99999-9999',
                'status' => 'ativo'
            ],
        ];

        foreach ($plantonistas as $plantonista) {
            Plantonista::firstOrCreate(
                ['crm' => $plantonista['crm']],
                $plantonista
            );
        }
    }
}
