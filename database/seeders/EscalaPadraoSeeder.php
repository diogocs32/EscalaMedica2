<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EscalaPadrao;
use App\Models\Unidade;
use App\Models\Turno;
use App\Models\Setor;

class EscalaPadraoSeeder extends Seeder
{
    /**
     * Seed de exemplo: Cria uma escala padrão de 5 semanas
     * para a primeira unidade do sistema
     */
    public function run(): void
    {
        // Buscar primeira unidade
        $unidade = Unidade::first();

        if (!$unidade) {
            $this->command->warn('Nenhuma unidade encontrada. Execute UnidadesSeeder primeiro.');
            return;
        }

        // Buscar turnos e setores
        $turnos = Turno::where('status', 'ativo')->get();
        $setores = Setor::where('status', 'ativo')->get();

        if ($turnos->isEmpty() || $setores->isEmpty()) {
            $this->command->warn('Turnos ou Setores não encontrados. Execute seeders necessários.');
            return;
        }

        // Criar escala padrão
        $escala = EscalaPadrao::create([
            'unidade_id' => $unidade->id,
            'nome' => 'Escala Padrão 2025 - ' . $unidade->nome,
            'descricao' => 'Template rotativo de 5 semanas criado automaticamente',
            'status' => 'ativo',
            'vigencia_inicio' => now()->startOfWeek(),
        ]);

        $this->command->info("✅ Escala padrão criada para {$unidade->nome}");

        // Criar estrutura: 5 semanas x 7 dias
        $escala->criarEstruturaPadrao();
        $this->command->info("✅ Estrutura de 5 semanas x 7 dias criada");

        // Configurar exemplo: Semana 1 - Segunda - Manhã
        $semana1 = $escala->semanas()->where('numero_semana', 1)->first();
        $segunda = $semana1->dias()->where('dia_semana', 'segunda')->first();

        // Exemplo de configurações
        $configuracoes = [
            // Manhã
            ['turno' => 'Manhã', 'setor' => 'UTI', 'qtd' => 3],
            ['turno' => 'Manhã', 'setor' => 'Emergência', 'qtd' => 2],
            ['turno' => 'Manhã', 'setor' => 'Teleconsulta', 'qtd' => 1],

            // Tarde
            ['turno' => 'Tarde', 'setor' => 'Teleconsulta', 'qtd' => 2],
            ['turno' => 'Tarde', 'setor' => 'Emergência', 'qtd' => 1],

            // Noite
            ['turno' => 'Noite', 'setor' => 'Emergência', 'qtd' => 2],
            ['turno' => 'Noite', 'setor' => 'UTI', 'qtd' => 1],
        ];

        foreach ($configuracoes as $config) {
            $turno = $turnos->firstWhere('nome', $config['turno']);
            $setor = $setores->firstWhere('nome', $config['setor']);

            if ($turno && $setor) {
                $segunda->configuracoes()->create([
                    'turno_id' => $turno->id,
                    'setor_id' => $setor->id,
                    'quantidade_necessaria' => $config['qtd'],
                    'status' => 'ativo',
                    'observacoes' => "Configuração exemplo - Semana 1 - Segunda"
                ]);
            }
        }

        $this->command->info("✅ Exemplo de configurações criado: Semana 1 - Segunda");
        $this->command->info("📋 Escala padrão completa e pronta para uso!");
        $this->command->line("");
        $this->command->line("ℹ️  Estrutura criada:");
        $this->command->line("   • 5 semanas template");
        $this->command->line("   • 35 dias configuráveis (5 semanas x 7 dias)");
        $this->command->line("   • 7 configurações exemplo na Segunda da Semana 1");
        $this->command->line("");
        $this->command->line("🎯 Próximo passo: Criar interface para gerenciar as configurações");
    }
}
