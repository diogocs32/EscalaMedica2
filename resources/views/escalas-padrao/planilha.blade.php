<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Planilha da Escala Padrão - {{ $unidade->nome }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet">
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
            display: block;
            font-size: .7rem;
            padding: .25rem .5rem;
            border-radius: 4px;
        }

        td {
            vertical-align: middle;
            padding: .4rem;
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

        /* Atribuição Rápida */
        .badge-slot {
            cursor: pointer;
            transition: all 0.2s;
            position: relative;
        }

        .badge-slot:hover {
            transform: scale(1.05);
            box-shadow: 0 2px 8px rgba(0, 0, 0, .15);
        }

        .badge-slot.disponivel {
            background: rgba(25, 135, 84, .12);
            color: #198754;
            border-color: rgba(25, 135, 84, .35);
            animation: pulse 2s infinite;
        }

        .badge-slot.indisponivel {
            background: rgba(108, 117, 125, .08);
            color: #6c757d;
            border-color: rgba(108, 117, 125, .2);
            cursor: not-allowed;
            opacity: 0.5;
        }

        .badge-slot.ocupado {
            background: rgba(13, 110, 253, .12);
            color: #0d6efd;
            border-color: rgba(13, 110, 253, .35);
        }

        /* Buraco padrão (não alocado) – mesma paleta dos badges de métricas "Buracos" */
        .badge-slot.buraco {
            background: rgba(220, 53, 69, .12);
            /* danger-subtle */
            color: #dc3545;
            /* text-danger */
            border-color: rgba(220, 53, 69, .35);
        }

        /* Buraco disponível para o plantonista selecionado (mantém vermelho, apenas dá destaque) */
        .badge-slot.buraco-disponivel {
            animation: pulse 2s infinite;
            border-color: rgba(220, 53, 69, .55);
        }

        /* Buraco indisponível (conflito) – mantém vermelho, porém indica bloqueio */
        .badge-slot.buraco-indisponivel {
            cursor: not-allowed;
            opacity: 1;
            border-style: dashed;
            background: rgb(235 206 209 / 12%) !important;
            color: #fbc6c6 !important;
            border-color: rgba(220, 53, 69, .35);
        }

        /* Ocupado pelo plantonista atualmente selecionado – mesma paleta de "Preenchidos" */
        .badge-slot.ocupado-selecionado {
            background: rgba(25, 135, 84, .12);
            /* success-subtle */
            color: #198754;
            /* text-success */
            border-color: rgba(25, 135, 84, .35);
        }

        @keyframes pulse {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.7;
            }
        }

        .plantonista-selecionado {
            background: #d1e7dd;
            border: 2px solid #198754;
        }

        .search-plantonista {
            position: sticky;
            top: 0;
            background: white;
            z-index: 1000;
            padding-bottom: 1rem;
            border-bottom: 2px solid #e9ecef;
        }

        .plantonista-card {
            cursor: pointer;
            transition: all 0.2s;
            border: 2px solid transparent;
        }

        .plantonista-card:hover {
            background: #f8f9fa;
            border-color: #dee2e6;
        }

        .plantonista-card.selected {
            background: #d1e7dd;
            border-color: #198754;
        }

        .info-badge {
            font-size: .7rem;
            padding: .2rem .4rem;
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
                <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#modalClonarDiaLote">
                    <i class="bi bi-copy"></i> Clonar Dia
                </button>
                <button class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#modalClonarSemanaLote">
                    <i class="bi bi-calendar-week"></i> Clonar Semana
                </button>
            </div>
        </div>

        <!-- Barra de Atribuição Rápida -->
        <div class="card card-slab mb-4" id="atribuicaoRapidaBar">
            <div class="card-body">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <h6 class="mb-0">
                            <i class="bi bi-lightning-charge text-success me-2"></i>
                            Atribuição Rápida
                        </h6>
                    </div>
                    <div class="col-md-5">
                        <select id="selectPlantonista" class="form-select" style="width: 100%;">
                            <option value="">Selecione um plantonista...</option>
                        </select>
                    </div>
                    <div class="col-auto">
                        <button class="btn btn-sm btn-outline-danger" id="btnLimparSelecao" style="display: none;">
                            <i class="bi bi-x-circle"></i> Limpar Seleção
                        </button>
                    </div>
                    <div class="col">
                        <div class="small text-muted">
                            <span class="badge bg-success-subtle text-success border me-1" style="font-size: .7rem;">
                                <i class="bi bi-check-circle"></i> Disponível
                            </span>
                            <span class="badge bg-secondary-subtle text-secondary border me-1" style="font-size: .7rem;">
                                <i class="bi bi-x-circle"></i> Conflito
                            </span>
                            <span class="badge bg-primary-subtle text-primary border" style="font-size: .7rem;">
                                <i class="bi bi-person-check"></i> Alocado
                            </span>
                        </div>
                    </div>
                </div>
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
                                @for($i = 1; $i <= $qtd; $i++)
                                    @php
                                    // Mapear dia para número: domingo=1, segunda=2, ..., sabado=7
                                    $diasMap=['domingo'=> 1, 'segunda' => 2, 'terca' => 3, 'quarta' => 4, 'quinta' => 5, 'sexta' => 6, 'sabado' => 7];
                                    $diaNum = $diasMap[$dKey] ?? 1;
                                    $slotKey = $sem . '-' . $diaNum . '-' . $tId . '-' . $sId . '-' . $i;
                                    @endphp
                                    <span class="badge badge-soft badge-slot buraco mb-1"
                                        data-semana="{{ $sem }}"
                                        data-dia="{{ $diaNum }}"
                                        data-turno="{{ $tId }}"
                                        data-setor="{{ $sId }}"
                                        data-slot="{{ $i }}"
                                        data-slot-key="{{ $slotKey }}"
                                        data-turno-inicio="{{ $info['turno']->hora_inicio }}"
                                        data-turno-fim="{{ $info['turno']->hora_fim }}">
                                        Buraco {{ $i }}
                                    </span>
                                    @endfor
                                    @else
                                    <span class="cell-empty">—</span>
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

    <!-- Modal Atribuição Rápida -->
    <div class="modal fade" id="modalAtribuicaoRapida" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-lightning-charge text-success me-2"></i>
                        Atribuição Rápida de Plantonistas
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="search-plantonista mb-3">
                        <label class="form-label fw-semibold">Buscar Plantonista</label>
                        <input type="text" id="searchPlantonista" class="form-control" placeholder="Digite o nome, CRM ou especialidade...">
                        <div class="mt-2">
                            <span class="badge bg-success-subtle text-success border info-badge">
                                <i class="bi bi-check-circle"></i> Disponível
                            </span>
                            <span class="badge bg-secondary-subtle text-secondary border info-badge ms-2">
                                <i class="bi bi-x-circle"></i> Conflito de horário
                            </span>
                            <span class="badge bg-primary-subtle text-primary border info-badge ms-2">
                                <i class="bi bi-person-check"></i> Já alocado
                            </span>
                        </div>
                    </div>

                    <div id="listaPlantonistas" class="row g-2">
                        <div class="col-12 text-center text-muted py-5">
                            <div class="spinner-border spinner-border-sm me-2" role="status">
                                <span class="visually-hidden">Carregando...</span>
                            </div>
                            Carregando plantonistas...
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="text-muted small me-auto">
                        Clique em um plantonista e depois nos slots disponíveis na planilha
                    </div>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Clonar Dia em Lote -->
    <div class="modal fade" id="modalClonarDiaLote" tabindex="-1">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-copy me-2"></i>Clonar Configurações de um Dia</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="form-label">Semana de Referência</label>
                            <select id="cloneSemanaRef" class="form-select">
                                @foreach(range(1,5) as $s)
                                <option value="{{ $s }}">Semana {{ $s }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-8">
                            <label class="form-label">Dia de Referência</label>
                            <select id="cloneDiaRef" class="form-select">
                                @foreach($diasOrdem as $d)
                                <option value="{{ $d }}">{{ $diasLabels[$d] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <div id="infoReferencia" class="alert alert-info small mb-0" style="display: none;">
                                <i class="bi bi-info-circle me-1"></i>
                                <span id="textoInfoReferencia"></span>
                            </div>
                            <div id="avisoSemConfigs" class="alert alert-warning small mb-0" style="display: none;">
                                <i class="bi bi-exclamation-triangle me-1"></i>
                                O dia de referência selecionado não possui plantonistas alocados. Aloque plantonistas primeiro nesse dia.
                            </div>
                        </div>
                    </div>

                    <hr>
                    <div class="mb-2 fw-semibold">Selecione os destinos</div>
                    <div class="small text-muted mb-2">Escolha as semanas e dias que receberão uma cópia exata das configurações do dia de referência.</div>

                    <div class="table-responsive">
                        <table class="table table-sm align-middle">
                            <thead>
                                <tr>
                                    <th>Semana</th>
                                    @foreach($diasOrdem as $d)
                                    <th class="text-center">{{ $diasLabels[$d] }}</th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                                @foreach(range(1,5) as $s)
                                <tr>
                                    <td><span class="fw-semibold">Semana {{ $s }}</span></td>
                                    @foreach($diasOrdem as $d)
                                    <td class="text-center">
                                        <input class="form-check-input destino-checkbox" type="checkbox" data-semana="{{ $s }}" data-dia="{{ $d }}">
                                    </td>
                                    @endforeach
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div class="form-check mt-2">
                        <input class="form-check-input" type="checkbox" id="cloneSobrescrever">
                        <label class="form-check-label" for="cloneSobrescrever">
                            Sobrescrever destinos (apaga as configurações atuais dos destinos antes de clonar)
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="text-muted small me-auto">A operação pode levar alguns segundos. A página será recarregada após concluir.</div>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                    <button type="button" class="btn btn-primary" id="btnConfirmarClone">
                        <i class="bi bi-clipboard-check"></i> Confirmar Clonagem
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Clonar Semana em Lote -->
    <div class="modal fade" id="modalClonarSemanaLote" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-calendar-week text-success me-2"></i>
                        Clonar Semana Completa
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info small">
                        <i class="bi bi-info-circle me-1"></i>
                        Esta ferramenta copia <strong>todas as alocações de plantonistas</strong> de uma semana inteira para outras semanas.
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold">Semana de Referência</label>
                            <select id="cloneSemanaRefSemanal" class="form-select">
                                @foreach(range(1,5) as $s)
                                <option value="{{ $s }}">Semana {{ $s }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <div id="infoReferenciaSemana" class="alert alert-info small mb-0" style="display: none;">
                                <i class="bi bi-info-circle me-1"></i>
                                <span id="textoInfoReferenciaSemana"></span>
                            </div>
                            <div id="avisoSemAlocacoesSemana" class="alert alert-warning small mb-0" style="display: none;">
                                <i class="bi bi-exclamation-triangle me-1"></i>
                                A semana de referência selecionada não possui plantonistas alocados. Aloque plantonistas primeiro nessa semana.
                            </div>
                        </div>
                    </div>

                    <hr>
                    <div class="mb-2 fw-semibold">Selecione as semanas de destino</div>
                    <div class="small text-muted mb-3">Escolha as semanas que receberão uma cópia de todas as alocações da semana de referência.</div>

                    <div class="d-flex flex-wrap gap-2">
                        @foreach(range(1,5) as $s)
                        <div class="form-check form-check-inline">
                            <input class="form-check-input destino-semana-checkbox" type="checkbox" id="destSemana{{ $s }}" value="{{ $s }}">
                            <label class="form-check-label" for="destSemana{{ $s }}">
                                Semana {{ $s }}
                            </label>
                        </div>
                        @endforeach
                    </div>

                    <div class="form-check mt-3">
                        <input class="form-check-input" type="checkbox" id="cloneSemanalSobrescrever">
                        <label class="form-check-label" for="cloneSemanalSobrescrever">
                            Sobrescrever destinos (apaga as alocações atuais das semanas de destino antes de clonar)
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <div class="text-muted small me-auto">A operação pode levar alguns segundos. A página será recarregada após concluir.</div>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                    <button type="button" class="btn btn-success" id="btnConfirmarCloneSemana">
                        <i class="bi bi-calendar-check"></i> Confirmar Clonagem
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        // Estado global
        let plantonistaSelecionado = null;
        let plantonistas = [];
        let alocacoes = {}; // {semana-dia-turno-setor-slot: plantonista_id}
        const unidadeId = parseInt('{{ $unidade->id }}');

        // Mapear dia nome para número
        const diasMap = {
            'domingo': 1,
            'segunda': 2,
            'terca': 3,
            'quarta': 4,
            'quinta': 5,
            'sexta': 6,
            'sabado': 7
        };

        // Função para contar alocações de plantonistas em um dia
        function contarAlocacoesDia(semana, dia) {
            const diaNum = diasMap[dia];
            let count = 0;

            // Percorrer as alocações em memória
            for (const key in alocacoes) {
                const [s, d, t, st, slot] = key.split('-').map(Number);
                if (s === semana && d === diaNum) {
                    count++;
                }
            }
            return count;
        }

        // Função para contar alocações de plantonistas em uma semana inteira
        function contarAlocacoesSemana(semana) {
            let count = 0;

            // Percorrer as alocações em memória
            for (const key in alocacoes) {
                const [s, d, t, st, slot] = key.split('-').map(Number);
                if (s === semana) {
                    count++;
                }
            }
            return count;
        }

        // Atualizar info da referência de DIA quando mudar semana ou dia
        function atualizarInfoReferencia() {
            const semana = parseInt(document.getElementById('cloneSemanaRef').value);
            const dia = document.getElementById('cloneDiaRef').value;
            const numAlocacoes = contarAlocacoesDia(semana, dia);

            const infoDiv = document.getElementById('infoReferencia');
            const avisoDiv = document.getElementById('avisoSemConfigs');
            const textoInfo = document.getElementById('textoInfoReferencia');
            const btnConfirmar = document.getElementById('btnConfirmarClone');

            if (numAlocacoes > 0) {
                textoInfo.textContent = `Este dia possui ${numAlocacoes} plantonista${numAlocacoes > 1 ? 's alocado' : ' alocado'}${numAlocacoes > 1 ? 's' : ''} que ${numAlocacoes > 1 ? 'serão copiados' : 'será copiado'}.`;
                infoDiv.style.display = 'block';
                avisoDiv.style.display = 'none';
                btnConfirmar.disabled = false;
            } else {
                infoDiv.style.display = 'none';
                avisoDiv.style.display = 'block';
                btnConfirmar.disabled = true;
            }
        }

        // Atualizar info da referência de SEMANA quando mudar semana
        function atualizarInfoReferenciaSemana() {
            const semana = parseInt(document.getElementById('cloneSemanaRefSemanal').value);
            const numAlocacoes = contarAlocacoesSemana(semana);

            const infoDiv = document.getElementById('infoReferenciaSemana');
            const avisoDiv = document.getElementById('avisoSemAlocacoesSemana');
            const textoInfo = document.getElementById('textoInfoReferenciaSemana');
            const btnConfirmar = document.getElementById('btnConfirmarCloneSemana');

            if (numAlocacoes > 0) {
                textoInfo.textContent = `Esta semana possui ${numAlocacoes} plantonista${numAlocacoes > 1 ? 's alocado' : ' alocado'}${numAlocacoes > 1 ? 's' : ''} que ${numAlocacoes > 1 ? 'serão copiados' : 'será copiado'}.`;
                infoDiv.style.display = 'block';
                avisoDiv.style.display = 'none';
                btnConfirmar.disabled = false;
            } else {
                infoDiv.style.display = 'none';
                avisoDiv.style.display = 'block';
                btnConfirmar.disabled = true;
            }
        }

        // Handler do modal de clonagem
        document.addEventListener('DOMContentLoaded', () => {
            console.log('🔧 Inicializando handler de clonagem...');
            const btnConfirmarClone = document.getElementById('btnConfirmarClone');
            console.log('🔘 Botão encontrado:', btnConfirmarClone);

            // Listeners para atualizar info quando mudar referência
            document.getElementById('cloneSemanaRef').addEventListener('change', atualizarInfoReferencia);
            document.getElementById('cloneDiaRef').addEventListener('change', atualizarInfoReferencia);

            // Atualizar info inicial quando abrir o modal
            document.getElementById('modalClonarDiaLote').addEventListener('shown.bs.modal', atualizarInfoReferencia);

            if (btnConfirmarClone) {
                btnConfirmarClone.addEventListener('click', async () => {
                    console.log('🚀 Clique no botão Confirmar Clonagem detectado!');

                    const semanaRef = parseInt(document.getElementById('cloneSemanaRef').value);
                    const diaRef = document.getElementById('cloneDiaRef').value;
                    const sobrescrever = document.getElementById('cloneSobrescrever').checked;

                    console.log('📋 Referência:', {
                        semanaRef,
                        diaRef,
                        sobrescrever
                    });

                    const destinos = Array.from(document.querySelectorAll('.destino-checkbox:checked')).map(chk => ({
                        semana: parseInt(chk.dataset.semana),
                        dia: chk.dataset.dia
                    }));

                    console.log('🎯 Destinos selecionados:', destinos);

                    if (!destinos.length) {
                        alert('Selecione pelo menos um destino para clonar.');
                        return;
                    }

                    // Remover destino igual à referência, se marcado
                    const destinosFiltrados = destinos.filter(d => !(d.semana === semanaRef && d.dia === diaRef));
                    console.log('✅ Destinos filtrados:', destinosFiltrados);

                    if (!destinosFiltrados.length) {
                        alert('Os destinos selecionados coincidem com o dia de referência. Selecione outros destinos.');
                        return;
                    }

                    const payload = {
                        referencia: {
                            semana: semanaRef,
                            dia: diaRef
                        },
                        destinos: destinosFiltrados,
                        sobrescrever: sobrescrever
                    };

                    console.log('📦 Payload completo:', payload);

                    try {
                        const url = '{{ url("/api/escalas-padrao") }}/' + unidadeId + '/clonar-dia';
                        console.log('🌐 URL da requisição:', url);

                        const resp = await fetch(url, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify(payload)
                        });

                        console.log('📡 Resposta recebida. Status:', resp.status);

                        const text = await resp.text();
                        console.log('📄 Texto da resposta:', text.substring(0, 500));

                        let result;
                        try {
                            result = JSON.parse(text);
                        } catch (_) {
                            result = {
                                success: false,
                                message: text.substring(0, 200)
                            };
                        }

                        console.log('✨ Resultado parseado:', result);

                        if (!resp.ok || !result.success) {
                            alert(result.message || 'Erro ao clonar.');
                            return;
                        }

                        alert(result.message || 'Clonagem concluída. A página será recarregada.');
                        window.location.reload();
                    } catch (e) {
                        console.error('❌ Erro na clonagem:', e);
                        alert('Erro na clonagem. Verifique a conexão.');
                    }
                });
            } else {
                console.error('❌ Botão btnConfirmarClone não encontrado!');
            }

            // === Handler para CLONAR SEMANA ===
            const btnConfirmarCloneSemana = document.getElementById('btnConfirmarCloneSemana');
            console.log('🔘 Botão clonar semana encontrado:', btnConfirmarCloneSemana);

            // Listener para atualizar info quando mudar semana de referência
            document.getElementById('cloneSemanaRefSemanal').addEventListener('change', atualizarInfoReferenciaSemana);

            // Atualizar info inicial quando abrir o modal
            document.getElementById('modalClonarSemanaLote').addEventListener('shown.bs.modal', atualizarInfoReferenciaSemana);

            if (btnConfirmarCloneSemana) {
                btnConfirmarCloneSemana.addEventListener('click', async () => {
                    console.log('📅 Clique no botão Confirmar Clonagem de Semana detectado!');

                    const semanaRef = parseInt(document.getElementById('cloneSemanaRefSemanal').value);
                    const sobrescrever = document.getElementById('cloneSemanalSobrescrever').checked;

                    console.log('📋 Referência de semana:', {
                        semanaRef,
                        sobrescrever
                    });

                    const destinos = Array.from(document.querySelectorAll('.destino-semana-checkbox:checked')).map(chk => parseInt(chk.value));

                    console.log('🎯 Semanas de destino selecionadas:', destinos);

                    if (!destinos.length) {
                        alert('Selecione pelo menos uma semana de destino para clonar.');
                        return;
                    }

                    const payload = {
                        referencia_semana: semanaRef,
                        destinos: destinos,
                        sobrescrever: sobrescrever
                    };

                    console.log('📦 Payload completo:', payload);

                    try {
                        const url = '{{ url("/api/escalas-padrao") }}/' + unidadeId + '/clonar-semana';
                        console.log('🌐 URL da requisição:', url);

                        const resp = await fetch(url, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json'
                            },
                            body: JSON.stringify(payload)
                        });

                        console.log('📡 Resposta recebida. Status:', resp.status);

                        const text = await resp.text();
                        console.log('📄 Texto da resposta:', text.substring(0, 500));

                        let result;
                        try {
                            result = JSON.parse(text);
                        } catch (_) {
                            result = {
                                success: false,
                                message: text.substring(0, 200)
                            };
                        }

                        console.log('✨ Resultado parseado:', result);

                        if (!resp.ok || !result.success) {
                            alert(result.message || 'Erro ao clonar semana.');
                            return;
                        }

                        alert(result.message || 'Clonagem de semana concluída. A página será recarregada.');
                        window.location.reload();
                    } catch (e) {
                        console.error('❌ Erro na clonagem de semana:', e);
                        alert('Erro na clonagem. Verifique a conexão.');
                    }
                });
            } else {
                console.error('❌ Botão btnConfirmarCloneSemana não encontrado!');
            }
        });

        // Carregar plantonistas e inicializar Select2
        $(document).ready(function() {
            carregarPlantonistas();
        });

        // Função para carregar plantonistas
        async function carregarPlantonistas() {
            try {
                console.log('Carregando plantonistas...');
                const response = await fetch('{{ url("/api/plantonistas-ativos") }}');
                plantonistas = await response.json();
                console.log('Plantonistas carregados:', plantonistas.length);

                // Inicializar Select2
                $('#selectPlantonista').select2({
                    theme: 'bootstrap-5',
                    placeholder: 'Selecione ou pesquise um plantonista...',
                    allowClear: true,
                    data: plantonistas.map(p => ({
                        id: p.id,
                        text: `${p.nome} - CRM: ${p.crm || 'N/D'} - ${p.especialidade || 'N/D'}`,
                        nome: p.nome,
                        crm: p.crm,
                        especialidade: p.especialidade
                    })),
                    matcher: matchCustom
                }).on('select2:select', function(e) {
                    selecionarPlantonista(e.params.data.id);
                }).on('select2:unselect', function() {
                    limparSelecao();
                });

                // Carregar alocações DEPOIS dos plantonistas
                await carregarAlocacoes();

            } catch (error) {
                console.error('Erro ao carregar plantonistas:', error);
                alert('Erro ao carregar plantonistas. Verifique a conexão.');
            }
        }

        // Função para carregar alocações existentes
        async function carregarAlocacoes() {
            try {
                const response = await fetch('{{ url("/api/escalas-padrao") }}/' + unidadeId + '/alocacoes');
                const data = await response.json();

                // Normalizar: manter mapa key -> plantonista_id (compatível com o restante do código)
                alocacoes = {};

                // Renderizar alocações na planilha
                for (const [key, aloc] of Object.entries(data)) {
                    alocacoes[key] = aloc.plantonista_id;
                    const slot = document.querySelector(`[data-slot-key="${key}"]`);
                    if (slot) {
                        const plantonista = plantonistas.find(p => p.id == aloc.plantonista_id);
                        if (plantonista) {
                            slot.classList.remove('disponivel', 'indisponivel', 'buraco', 'ocupado-selecionado', 'buraco-disponivel', 'buraco-indisponivel');
                            slot.classList.add('ocupado');
                            slot.textContent = plantonista.nome.split(' ')[0];
                            slot.title = `${plantonista.nome}\nCRM: ${plantonista.crm || 'N/D'}`;
                        }
                    }
                }

                atualizarSlotsDisponiveis();
            } catch (error) {
                console.error('Erro ao carregar alocações:', error);
            }
        }

        // Matcher customizado para busca por nome, CRM ou especialidade
        function matchCustom(params, data) {
            if ($.trim(params.term) === '') {
                return data;
            }

            const term = params.term.toLowerCase();
            const plantonista = plantonistas.find(p => p.id == data.id);

            if (!plantonista) return null;

            // Buscar em nome, CRM e especialidade
            if (plantonista.nome.toLowerCase().indexOf(term) > -1 ||
                (plantonista.crm && plantonista.crm.toLowerCase().indexOf(term) > -1) ||
                (plantonista.especialidade && plantonista.especialidade.toLowerCase().indexOf(term) > -1)) {
                return data;
            }

            return null;
        }

        // Selecionar plantonista
        function selecionarPlantonista(id) {
            const plantonista = plantonistas.find(p => p.id == id);
            if (!plantonista) return;

            plantonistaSelecionado = plantonista;

            // Mostrar botão de limpar
            $('#btnLimparSelecao').show();

            // Atualizar visualização dos slots
            atualizarSlotsDisponiveis();
        }

        // Limpar seleção
        function limparSelecao() {
            plantonistaSelecionado = null;
            $('#selectPlantonista').val(null).trigger('change');
            $('#btnLimparSelecao').hide();
            atualizarSlotsDisponiveis();
        }

        // Botão limpar seleção
        $('#btnLimparSelecao').on('click', limparSelecao);

        // Verificar conflito de horário entre turnos (ou janelas de tempo)
        function temConflito(turno1Inicio, turno1Fim, turno2Inicio, turno2Fim) {
            const t1i = timeToMinutes(turno1Inicio);
            const t1f = timeToMinutes(turno1Fim);
            const t2i = timeToMinutes(turno2Inicio);
            const t2f = timeToMinutes(turno2Fim);

            console.log('  📊 Conversão:', {
                turno1: `${turno1Inicio} (${t1i}min) - ${turno1Fim} (${t1f}min)`,
                turno2: `${turno2Inicio} (${t2i}min) - ${turno2Fim} (${t2f}min)`
            });

            // Tratar turnos que passam da meia-noite (ex: 19:00-07:00)
            const t1f_ajustado = t1f < t1i ? t1f + 1440 : t1f;
            const t2f_ajustado = t2f < t2i ? t2f + 1440 : t2f;

            // Verificar sobreposição REAL (não apenas adjacência)
            // Turnos consecutivos (ex: 07:00-13:00 e 13:00-19:00) NÃO conflitam
            // Turnos sobrepostos (ex: 07:00-13:00 e 09:00-15:00) SIM conflitam
            // Observação: também detecta conflito quando os intervalos são idênticos (mesmo turno/janela)
            const hasOverlap = (t1i < t2f_ajustado && t1f_ajustado > t2i);
            console.log('  🔢 Resultado:', hasOverlap ? 'CONFLITO' : 'SEM CONFLITO');

            return hasOverlap;
        }

        // Converter HH:MM para minutos (aceita também datetime completo)
        function timeToMinutes(time) {
            if (!time) return 0;

            // Se for datetime completo (2025-10-21 09:00:00), extrair apenas a hora
            if (time.includes(' ')) {
                time = time.split(' ')[1]; // Pega "09:00:00"
            }

            // Extrair apenas HH:MM (ignorar segundos se houver)
            const parts = time.split(':');
            const h = parseInt(parts[0]);
            const m = parseInt(parts[1]);

            return h * 60 + m;
        }

        // Atualizar visualização dos slots disponíveis
        function atualizarSlotsDisponiveis() {
            if (!plantonistaSelecionado) {
                // Resetar todos os slots: mostrar ocupados em azul e buracos em vermelho
                document.querySelectorAll('.badge-slot').forEach(slot => {
                    const key = `${slot.dataset.semana}-${slot.dataset.dia}-${slot.dataset.turno}-${slot.dataset.setor}-${slot.dataset.slot}`;
                    const pid = alocacoes[key];

                    slot.classList.remove('disponivel', 'indisponivel', 'ocupado-selecionado', 'buraco-disponivel', 'buraco-indisponivel');

                    if (pid) {
                        // Ocupado (azul)
                        slot.classList.remove('buraco');
                        slot.classList.add('ocupado');
                        const plantonista = plantonistas.find(p => p.id == pid);
                        slot.textContent = plantonista ? plantonista.nome.split(' ')[0] : 'Ocupado';
                    } else {
                        // Buraco (vermelho)
                        slot.classList.remove('ocupado');
                        slot.classList.add('buraco');
                        slot.textContent = `Buraco ${slot.dataset.slot}`;
                    }
                });
                return;
            }

            // Para cada slot, verificar disponibilidade
            document.querySelectorAll('.badge-slot').forEach(slot => {
                const semana = slot.dataset.semana;
                const dia = slot.dataset.dia;
                const turno = slot.dataset.turno;
                const setor = slot.dataset.setor;
                const slotNum = slot.dataset.slot;
                const turnoInicio = slot.dataset.turnoInicio;
                const turnoFim = slot.dataset.turnoFim;

                const key = `${semana}-${dia}-${turno}-${setor}-${slotNum}`;

                // Verificar se já está ocupado
                if (alocacoes[key]) {
                    slot.classList.remove('disponivel', 'indisponivel', 'buraco');
                    slot.classList.add('ocupado');
                    const plantonista = plantonistas.find(p => p.id == alocacoes[key]);
                    slot.textContent = plantonista ? plantonista.nome.split(' ')[0] : 'Ocupado';

                    // Se ocupado pelo plantonista selecionado, destacar em verde
                    if (Number(alocacoes[key]) === Number(plantonistaSelecionado.id)) {
                        slot.classList.add('ocupado-selecionado');
                    } else {
                        slot.classList.remove('ocupado-selecionado');
                    }
                    return;
                }

                // Verificar conflitos de horário no mesmo dia com OUTRAS alocações
                const temConflitoDia = Object.keys(alocacoes).some(k => {
                    const [s, d, t, st, slotN] = k.split('-');

                    // Ignorar se não é mesmo dia, mesma semana ou mesmo plantonista
                    if (s !== semana || d !== dia || Number(alocacoes[k]) !== Number(plantonistaSelecionado.id)) return false;

                    // Ignorar se é o EXATO mesmo slot (mesma semana, dia, turno, setor E número)
                    if (s === semana && d === dia && t === turno && st === setor && slotN === slotNum) return false;

                    // REGRA ATUALIZADA: NUNCA permitir duplicidade do mesmo plantonista em janelas que se sobrepõem,
                    // inclusive quando for o mesmo turno e mesmo setor (buracos diferentes). Ou seja, se os intervalos
                    // forem iguais (mesmo turno) ou tiverem qualquer sobreposição, deve BLOQUEAR.
                    // Abaixo verificamos a sobreposição de horário entre o slot atual e o já alocado.
                    const outroSlot = document.querySelector(`[data-semana="${s}"][data-dia="${d}"][data-turno="${t}"][data-setor="${st}"][data-slot="${slotN}"]`);
                    if (!outroSlot) {
                        console.warn('⚠️ Slot não encontrado:', s, d, t, st, slotN);
                        return false;
                    }

                    const outroInicio = outroSlot.dataset.turnoInicio;
                    const outroFim = outroSlot.dataset.turnoFim;

                    console.log('🔍 Verificando conflito:');
                    console.log('  Slot atual:', turno, setor, turnoInicio + '-' + turnoFim);
                    console.log('  Já alocado:', t, st, outroInicio + '-' + outroFim);

                    // BLOQUEIA se houver sobreposição de horário (inclui intervalos idênticos)
                    // ✅ PERMITE: Manhã 07-13h + Tarde 13-19h (consecutivos, sem sobreposição)
                    // ❌ BLOQUEIA: Manhã 07-13h + Manhã Suporte 09-15h (sobreposição 09-13h)
                    // ❌ BLOQUEIA: Manhã 07-13h + Manhã 07-13h (intervalos idênticos, buracos diferentes)
                    const hasConflict = temConflito(turnoInicio, turnoFim, outroInicio, outroFim);

                    if (hasConflict) {
                        console.log('❌ CONFLITO DETECTADO - Bloqueando');
                    } else {
                        console.log('✅ SEM CONFLITO - Permitindo');
                    }

                    return hasConflict;
                });

                if (temConflitoDia) {
                    // Conflito: manter buraco em vermelho, porém com indicação de bloqueio
                    slot.classList.remove('disponivel', 'ocupado', 'ocupado-selecionado', 'buraco-disponivel');
                    slot.classList.add('buraco', 'buraco-indisponivel');
                    slot.title = 'Conflito de horário com outra alocação';
                    return;
                }

                // Disponível: manter visual de buraco em vermelho com leve destaque
                slot.classList.remove('ocupado', 'ocupado-selecionado', 'buraco-indisponivel');
                slot.classList.add('buraco', 'buraco-disponivel');
                slot.title = 'Clique para alocar ' + plantonistaSelecionado.nome;
            });
        }

        // Alocar plantonista ao clicar no slot
        document.addEventListener('click', async function(e) {
            if (!e.target.classList.contains('badge-slot')) return;
            if (!plantonistaSelecionado) {
                alert('Selecione um plantonista primeiro no dropdown acima');
                return;
            }

            const slot = e.target;

            // Verificar se está disponível
            if (slot.classList.contains('indisponivel')) {
                alert('Este slot não está disponível para o plantonista selecionado');
                return;
            }

            if (slot.classList.contains('ocupado')) {
                // Desalocar
                const key = `${slot.dataset.semana}-${slot.dataset.dia}-${slot.dataset.turno}-${slot.dataset.setor}-${slot.dataset.slot}`;

                // Remover do banco
                const success = await salvarAlocacao(key, null);
                if (success) {
                    delete alocacoes[key];
                    slot.classList.remove('ocupado', 'ocupado-selecionado');
                    slot.classList.add('buraco');
                    slot.textContent = `Buraco ${slot.dataset.slot}`;
                    atualizarSlotsDisponiveis();
                }
                return;
            }

            // Alocar
            const key = `${slot.dataset.semana}-${slot.dataset.dia}-${slot.dataset.turno}-${slot.dataset.setor}-${slot.dataset.slot}`;

            // Salvar no banco
            const success = await salvarAlocacao(key, plantonistaSelecionado.id);
            if (success) {
                alocacoes[key] = plantonistaSelecionado.id;

                slot.classList.remove('disponivel', 'buraco', 'buraco-disponivel', 'buraco-indisponivel');
                slot.classList.add('ocupado', 'ocupado-selecionado');
                slot.textContent = plantonistaSelecionado.nome.split(' ')[0];
                slot.title = plantonistaSelecionado.nome;

                // Atualizar disponibilidade
                atualizarSlotsDisponiveis();
            }
        });

        // Função para salvar/remover alocação via API
        async function salvarAlocacao(key, plantonista_id) {
            const [semana, dia, turno_id, setor_id, slot] = key.split('-');

            const payload = {
                semana: parseInt(semana),
                dia: parseInt(dia),
                turno_id: parseInt(turno_id),
                setor_id: parseInt(setor_id),
                slot: parseInt(slot),
                plantonista_id: plantonista_id
            };

            try {
                const url = '{{ url("/api/escalas-padrao") }}/' + unidadeId + '/alocacoes';
                console.log('🚀 POST para:', url);
                console.log('📦 Payload:', payload);
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(payload)
                });

                console.log('📡 Status da resposta:', response.status);
                const responseText = await response.text();
                console.log('📄 Resposta recebida (primeiros 200 chars):', responseText.substring(0, 200));

                const result = JSON.parse(responseText);

                if (!response.ok || !result.success) {
                    alert(result.message || 'Erro ao salvar alocação');
                    return false;
                }

                return true;
            } catch (error) {
                console.error('Erro ao salvar alocação:', error);
                alert('Erro ao salvar alocação. Verifique a conexão.');
                return false;
            }
        }
    </script>
</body>

</html>