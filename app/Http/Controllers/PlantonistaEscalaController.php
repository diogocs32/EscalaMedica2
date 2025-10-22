<?php

namespace App\Http\Controllers;

use App\Models\Plantonista;
use App\Models\AlocacaoTemplate;
use Illuminate\Http\Request;

class PlantonistaEscalaController extends Controller
{
    /**
     * Exibe todas as escalas padrão (alocações template) de um plantonista
     * agrupadas por cidade → unidade → dia da semana → turno
     */
    public function index(Request $request)
    {
        // Todos os plantonistas ativos para o select
        $plantonistas = Plantonista::query()
            ->when(method_exists(Plantonista::class, 'scopeAtivo'), fn($q) => $q->ativo())
            ->orderBy('nome')
            ->get();

        $plantonistaId = $request->input('plantonista_id');
        $alocacoes = collect();
        $plantonistaSelecionado = null;

        if ($plantonistaId) {
            $plantonistaSelecionado = Plantonista::find($plantonistaId);

            // Buscar todas as alocações template do plantonista
            $alocacoes = AlocacaoTemplate::query()
                ->where('plantonista_id', $plantonistaId)
                ->with([
                    'escalaPadrao.unidade.cidade',
                    'turno',
                    'setor'
                ])
                ->get();
        }

        $totalAlocacoes = $alocacoes->count();

        // Agrupar por cidade → unidade
        $escalasPorCidade = $alocacoes->groupBy(function ($aloc) {
            return $aloc->escalaPadrao->unidade->cidade->nome ?? 'Cidade Desconhecida';
        })->map(function ($alocsPorCidade) {
            return $alocsPorCidade->groupBy(function ($aloc) {
                return $aloc->escalaPadrao->unidade->nome ?? 'Unidade Desconhecida';
            });
        });

        return view('plantonista-escalas.index', compact(
            'plantonistas',
            'plantonistaSelecionado',
            'escalasPorCidade',
            'totalAlocacoes'
        ));
    }
}
