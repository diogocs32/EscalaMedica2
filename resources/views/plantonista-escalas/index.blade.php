<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Escalas do Plantonista</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background: #f6f7fb;
        }

        .page-title {
            display: flex;
            align-items: center;
            gap: .75rem;
        }

        .card-slab {
            border: 0;
            border-radius: 14px;
            box-shadow: 0 8px 28px rgba(0, 0, 0, .06);
        }

        .cidade-section {
            margin-bottom: 2rem;
        }

        .cidade-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 1rem 1.5rem;
            border-radius: 12px 12px 0 0;
            font-size: 1.25rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: .5rem;
        }

        .unidade-block {
            border-left: 4px solid #667eea;
            padding-left: 1.5rem;
            margin-bottom: 1.5rem;
        }

        .unidade-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: #495057;
            margin-bottom: .75rem;
            display: flex;
            align-items: center;
            gap: .5rem;
        }

        .alocacao-item {
            background: white;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            padding: .75rem 1rem;
            margin-bottom: .5rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            transition: all 0.2s;
        }

        .alocacao-item:hover {
            background: #f8f9fa;
            border-color: #667eea;
            transform: translateX(4px);
        }

        .dia-badge {
            background: #e7f3ff;
            color: #0066cc;
            padding: .4rem .75rem;
            border-radius: 6px;
            font-weight: 600;
            min-width: 140px;
            text-align: center;
        }

        .turno-badge {
            background: #fff3cd;
            color: #856404;
            padding: .4rem .75rem;
            border-radius: 6px;
            font-weight: 500;
            flex: 1;
        }

        .horario-badge {
            background: #d1ecf1;
            color: #0c5460;
            padding: .4rem .75rem;
            border-radius: 6px;
            font-family: 'Courier New', monospace;
            font-weight: 600;
            min-width: 130px;
            text-align: center;
        }

        .setor-badge {
            background: #f8d7da;
            color: #721c24;
            padding: .3rem .6rem;
            border-radius: 6px;
            font-size: .85rem;
        }

        .empty-state {
            text-align: center;
            padding: 3rem;
            color: #6c757d;
        }

        .empty-state i {
            font-size: 4rem;
            margin-bottom: 1rem;
            opacity: .3;
        }
    </style>
</head>

<body>
    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="page-title h4 mb-0">
                <i class="bi bi-person-badge"></i> Escalas do Plantonista
            </h1>
            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-left"></i> Voltar
            </a>
        </div>

        <div class="card card-slab mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('plantonista.escalas') }}">
                    <div class="row align-items-end">
                        <div class="col-md-8">
                            <label class="form-label fw-semibold">Selecione o Plantonista</label>
                            <select name="plantonista_id" class="form-select form-select-lg" onchange="this.form.submit()">
                                <option value="">— Escolha um plantonista —</option>
                                @foreach($plantonistas as $p)
                                <option value="{{ $p->id }}" {{ request('plantonista_id') == $p->id ? 'selected' : '' }}>
                                    {{ $p->nome }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-primary btn-lg w-100">
                                <i class="bi bi-search"></i> Buscar Escalas
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        @if($plantonistaSelecionado)
        <div class="card card-slab mb-3">
            <div class="card-body">
                <h5 class="mb-3">
                    <i class="bi bi-person-circle"></i>
                    <strong>{{ $plantonistaSelecionado->nome }}</strong>
                </h5>
                <div class="row g-3">
                    @if($plantonistaSelecionado->crm)
                    <div class="col-md-3">
                        <small class="text-muted">CRM</small>
                        <div>{{ $plantonistaSelecionado->crm }}</div>
                    </div>
                    @endif
                    @if($plantonistaSelecionado->especialidade)
                    <div class="col-md-3">
                        <small class="text-muted">Especialidade</small>
                        <div>{{ $plantonistaSelecionado->especialidade }}</div>
                    </div>
                    @endif
                    @if($plantonistaSelecionado->telefone)
                    <div class="col-md-3">
                        <small class="text-muted">Telefone</small>
                        <div>{{ $plantonistaSelecionado->telefone }}</div>
                    </div>
                    @endif
                    @if($plantonistaSelecionado->email)
                    <div class="col-md-3">
                        <small class="text-muted">E-mail</small>
                        <div>{{ $plantonistaSelecionado->email }}</div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        @if($escalasPorCidade->isEmpty())
        <div class="card card-slab">
            <div class="card-body empty-state">
                <i class="bi bi-calendar-x"></i>
                <h5>Nenhuma escala encontrada</h5>
                <p class="text-muted mb-0">Este plantonista ainda não possui alocações nas escalas padrão.</p>
            </div>
        </div>
        @else
        @foreach($escalasPorCidade as $nomeCidade => $unidades)
        <div class="cidade-section">
            <div class="card card-slab">
                <div class="cidade-header">
                    <i class="bi bi-geo-alt-fill"></i>
                    {{ strtoupper($nomeCidade) }}
                </div>
                <div class="card-body">
                    @foreach($unidades as $nomeUnidade => $alocacoes)
                    <div class="unidade-block">
                        <div class="unidade-title">
                            <i class="bi bi-hospital"></i>
                            {{ $nomeUnidade }}
                        </div>

                        @php
                        // Resumo: agrupar por (dia, turno, setor) e consolidar semanas
                        $grupos = $alocacoes->groupBy(function($a){
                        return ($a->dia ?? 0) . '|' . ($a->turno_id ?? 0) . '|' . ($a->setor_id ?? 0);
                        });

                        $resumo = $grupos->map(function($colecao){
                        $primeiro = $colecao->first();
                        $dia = $primeiro->dia ?? null;
                        $turno = $primeiro->turno ?? null;
                        $setor = $primeiro->setor ?? null;
                        $semanas = $colecao->pluck('semana')->filter()->unique()->sort()->values()->all();

                        $diaNomes = [1=>'Segundas-feiras',2=>'Terças-feiras',3=>'Quartas-feiras',4=>'Quintas-feiras',5=>'Sextas-feiras',6=>'Sábados',7=>'Domingos'];
                        $diaTexto = $diaNomes[$dia] ?? 'Dia';

                        $horaIni = $turno ? \Carbon\Carbon::parse($turno->hora_inicio)->format('H:i') : '--:--';
                        $horaFim = $turno ? \Carbon\Carbon::parse($turno->hora_fim)->format('H:i') : '--:--';

                        return [
                        'dia' => $dia,
                        'dia_texto' => $diaTexto,
                        'turno_nome' => $turno->nome ?? 'Turno N/A',
                        'hora_inicio' => $horaIni,
                        'hora_fim' => $horaFim,
                        'setor_nome' => $setor->nome ?? null,
                        'semanas' => $semanas,
                        ];
                        })->sortBy([
                        ['dia','asc'],
                        ['hora_inicio','asc'],
                        ['setor_nome','asc'],
                        ])->values();
                        @endphp

                        @foreach($resumo as $item)
                        <div class="alocacao-item">
                            <div class="dia-badge">
                                <i class="bi bi-calendar-event"></i> {{ $item['dia_texto'] }}
                            </div>
                            <div class="turno-badge">
                                <i class="bi bi-clock"></i> {{ $item['turno_nome'] }}
                            </div>
                            <div class="horario-badge">
                                {{ $item['hora_inicio'] }} às {{ $item['hora_fim'] }}
                            </div>
                            @if(!empty($item['setor_nome']))
                            <div class="setor-badge">
                                {{ $item['setor_nome'] }}
                            </div>
                            @endif
                            <span class="ms-auto text-muted small">sem. {{ implode(',', $item['semanas']) }}</span>
                        </div>
                        @endforeach
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endforeach
        @endif
        @endif
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>