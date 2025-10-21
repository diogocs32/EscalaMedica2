<?php

namespace App\Http\Controllers;

use App\Models\Alocacao;
use App\Models\Plantonista;
use App\Models\Setor;
use App\Models\Turno;
use App\Models\Vaga;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display the dashboard.
     */
    public function index()
    {
        // Estatísticas principais
        $stats = [
            'score_atual' => $this->calculateCurrentScore(),
            'score_3_meses' => $this->calculateThreeMonthScore(),
            'proximos_plantoes' => $this->getUpcomingShifts(),
            'ofertas_disponiveis' => $this->getAvailableOffers(),
        ];

        // Próximos plantões do usuário (simulado como todos por enquanto)
        $meusProximosPlantoes = Alocacao::with(['plantonista', 'vaga.unidade', 'vaga.setor', 'vaga.turno'])
            ->where('data_plantao', '>=', Carbon::today())
            ->where('status', '!=', 'cancelada')
            ->orderBy('data_plantao')
            ->take(5)
            ->get();

        // Ofertas disponíveis no marketplace (vagas sem alocação)
        $ofertasDisponiveis = Vaga::with(['unidade', 'setor', 'turno'])
            ->whereDoesntHave('alocacoes', function ($query) {
                $query->where('data_plantao', '>=', Carbon::today())
                    ->where('status', '!=', 'cancelada');
            })
            ->where('status', 'ativo')
            ->take(5)
            ->get();

        return view('dashboard.index', compact('stats', 'meusProximosPlantoes', 'ofertasDisponiveis'));
    }

    private function calculateCurrentScore()
    {
        // Simula pontuação baseada em plantões realizados no mês atual
        return Alocacao::where('data_plantao', '>=', Carbon::now()->startOfMonth())
            ->where('data_plantao', '<=', Carbon::now())
            ->where('status', 'confirmada')
            ->count() * 10;
    }

    private function calculateThreeMonthScore()
    {
        // Simula pontuação dos últimos 3 meses
        return Alocacao::where('data_plantao', '>=', Carbon::now()->subMonths(3))
            ->where('data_plantao', '<=', Carbon::now())
            ->where('status', 'confirmada')
            ->count() * 10;
    }

    private function getUpcomingShifts()
    {
        return Alocacao::where('data_plantao', '>=', Carbon::today())
            ->where('status', '!=', 'cancelada')
            ->count();
    }

    private function getAvailableOffers()
    {
        return Vaga::whereDoesntHave('alocacoes', function ($query) {
            $query->where('data_plantao', '>=', Carbon::today())
                ->where('status', '!=', 'cancelada');
        })
            ->where('status', 'ativo')
            ->count();
    }
}
