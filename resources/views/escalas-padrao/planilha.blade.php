<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Planilha da Escala Padrão - {{ $unidade->nome }}</title>
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

        .metric-badge {
            font-weight: 600;
            font-size: .95rem;
        }

        .table-fixed thead th {
            position: sticky;
            top: 0;
            background: #fff;
            z-index: 2;
        }

        .turno-header {
            background: #f1f3f8;
            font-weight: 600;
            text-align: center;
            border-bottom: 0;
        }

        .setor-header {
            background: #fafbfe;
            text-align: center;
            font-size: .9rem;
        }

        .semana-title {
            font-weight: 700;
            color: #495057;
        }

        .day-col {
            width: 160px;
            white-space: nowrap;
        }

        .cell-empty {
            color: #adb5bd;
        }

        .card-slab {
            border: 0;
            border-radius: 14px;
            box-shadow: 0 8px 28px rgba(0, 0, 0, .06);
        }

        .soft {
            color: #6c757d;
        }

        .badge-soft {
            background: rgba(220, 53, 69, .08);
            color: #dc3545;
            border: 1px solid rgba(220, 53, 69, .25);
        }

        .table-responsive {
            border-radius: 12px;
            border: 1px solid #eef0f6;
            background: #fff;
        }

        .thead-floating {
            position: sticky;
            top: 0;
            background: #fff;
            z-index: 3;
        }

        .legend .badge {
            font-size: .8rem;
        }

        .toolbar .btn {
            border-radius: 10px;
        }

        .week-block {
            scroll-margin-top: 80px;
        }
    </style>
</head>

<body>
    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="page-title h3 mb-0">
                <i class="bi bi-calendar3 me-1"></i>
                Planilha da Escala Padrão — {{ $unidade->nome }}
            </h1>
            <div class="toolbar d-flex gap-2">
                <a href="{{ route('schedule-patterns') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left"></i> Voltar
                </a>
                <button class="btn btn-success btn-sm" disabled title="Em breve">
                    <i class="bi bi-lightning-charge"></i> Atribuição Rápida
                </button>
            </div>
        </div>

        <div class="row g-3 mb-4">
            <div class="col-auto">
                <span class="badge bg-light text-dark metric-badge">Total de Slots: <span class="fw-bold">{{ number_format($metricas['totalSlots']) }}</span></span>
            </div>
            <div class="col-auto">
                <span class="badge bg-success-subtle text-success border metric-badge">Preenchidos: <span class="fw-bold">{{ number_format($metricas['preenchidos']) }}</span></span>
            </div>
            <div class="col-auto">
                <span class="badge bg-danger-subtle text-danger border metric-badge">Buracos: <span class="fw-bold">{{ number_format($metricas['buracos']) }}</span></span>
            </div>
            <div class="col-auto">
                <span class="badge bg-primary-subtle text-primary border metric-badge">Taxa: <span class="fw-bold">{{ $metricas['taxa'] }}%</span></span>
            </div>
            <div class="col-12 soft small">Vigência: {{ $escala->vigencia_inicio ? \Carbon\Carbon::parse($escala->vigencia_inicio)->format('d/m/Y') : 'N/D' }} • Status: <strong>{{ ucfirst($escala->status) }}</strong></div>
        </div>

        <div class="legend mb-3">
            <span class="badge bg-danger-subtle text-danger border me-2"><i class="bi bi-exclamation-triangle me-1"></i> Buraco (quantidade necessária)</span>
            <span class="badge bg-light text-muted border"><i class="bi bi-dash me-1"></i> Sem configuração</span>
        </div>

        @php
        $diasLabels = [
        'domingo' => 'Domingo',
        'segunda' => 'Segunda-feira',
        'terca' => 'Terça-feira',
        'quarta' => 'Quarta-feira',
        'quinta' => 'Quinta-feira',
        'sexta' => 'Sexta-feira',
        'sabado' => 'Sábado',
        ];
        @endphp

        @foreach(range(1,5) as $sem)
        @if(isset($grid[$sem]))
        <div id="semana-{{ $sem }}" class="week-block mb-4">
            <div class="d-flex align-items-center mb-2">
                <span class="semana-title">Semana {{ $sem }}</span>
                <div class="ms-auto small soft">Configurações exibidas por Turno → Setor</div>
            </div>
            <div class="table-responsive">
                <table class="table table-sm align-middle mb-0">
                    <thead class="thead-floating">
                        <tr class="turno-header">
                            <th class="day-col">Dia</th>
                            @foreach($turnosMapa as $tId => $info)
                            <th class="text-center" colspan="{{ count($info['setores']) }}">
                                {{ $info['turno']->nome }}
                                @if($info['turno']->hora_inicio && $info['turno']->hora_fim)
                                <div class="small soft">{{ substr($info['turno']->hora_inicio,0,5) }} – {{ substr($info['turno']->hora_fim,0,5) }}</div>
                                @endif
                            </th>
                            @endforeach
                        </tr>
                        <tr class="setor-header">
                            <th class="day-col"></th>
                            @foreach($turnosMapa as $tId => $info)
                            @foreach($info['setores'] as $sId => $setor)
                            <th class="text-center">{{ $setor->nome }}</th>
                            @endforeach
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($diasOrdem as $dKey)
                        <tr>
                            <td class="fw-semibold">{{ $diasLabels[$dKey] ?? ucfirst($dKey) }}</td>
                            @foreach($turnosMapa as $tId => $info)
                            @foreach($info['setores'] as $sId => $setor)
                            @php
                            $qtd = $grid[$sem][$dKey][$tId][$sId] ?? 0;
                            @endphp
                            <td class="text-center">
                                @if($qtd > 0)
                                <span class="badge badge-soft">Buraco ({{ $qtd }})</span>
                                @else
                                <span class="cell-empty">0</span>
                                @endif
                            </td>
                            @endforeach
                            @endforeach
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif
        @endforeach
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>