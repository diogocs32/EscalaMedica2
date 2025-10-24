<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Editar Escala Publicada - {{ $escalaPublicada->unidade->nome }} ({{ \Carbon\Carbon::create($escalaPublicada->ano, $escalaPublicada->mes, 1)->locale('pt_BR')->isoFormat('MMMM/YYYY') }})</title>
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

        .table-fixed {
            font-size: .9rem;
        }

        .table-fixed thead th {
            position: sticky;
            top: 0;
            background: #fff;
            z-index: 3;
            border-bottom: 2px solid #dee2e6;
            font-weight: 600;
        }

        .turno-header {
            background: #f1f3f8;
            text-align: center;
            font-weight: 700;
            border-bottom: 1px solid #dee2e6;
        }

        .setor-subheader {
            background: #fafbfe;
            text-align: center;
            font-size: .85rem;
        }

        .slot-cell {
            padding: .3rem;
            vertical-align: middle;
        }

        .slot-badge {
            display: inline-block;
            padding: .25rem .5rem;
            border-radius: 6px;
            font-size: .8rem;
            cursor: pointer;
            transition: all 0.2s;
            border: 1px solid;
        }

        .slot-badge.preenchido {
            background: rgba(25, 135, 84, .12);
            color: #198754;
            border-color: rgba(25, 135, 84, .35);
        }

        .slot-badge.vago {
            background: rgba(220, 53, 69, .12);
            color: #dc3545;
            border-color: rgba(220, 53, 69, .35);
        }

        .slot-badge:hover {
            transform: scale(1.05);
            box-shadow: 0 2px 8px rgba(0, 0, 0, .15);
        }
    </style>
    <script>
        function submitUpdate(formId) {
            const form = document.getElementById(formId);
            if (form) form.submit();
        }

        function clearAssignment(formId) {
            const form = document.getElementById(formId);
            if (!form) return;
            const select = form.querySelector('select[name="plantonista_id"]');
            if (select) select.value = '';
            form.submit();
        }
    </script>
</head>

<body>
    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="d-flex align-items-center gap-2">
                <h1 class="page-title h4 mb-0"><i class="bi bi-calendar4-week me-1"></i> Editar Escala Publicada</h1>
                <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-house"></i></a>
            </div>
            <div>
                <!-- Botões em telas grandes -->
                <div class="d-none d-md-flex gap-2">
                    <a href="{{ route('escalas-publicadas.edit-rapido', $escalaPublicada) }}" class="btn btn-sm btn-success">
                        <i class="bi bi-lightning-charge"></i> Atribuição Rápida
                    </a>
                    <a href="{{ route('escalas-publicadas.calendar') }}?mes={{ $escalaPublicada->ano }}-{{ str_pad($escalaPublicada->mes, 2, '0', STR_PAD_LEFT) }}" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-calendar3"></i> Ver Calendário
                    </a>
                    <a href="{{ route('alocacoes.index') }}" class="btn btn-sm btn-outline-secondary">
                        <i class="bi bi-card-list"></i> Lista
                    </a>
                </div>
                <!-- Dropdown em telas pequenas -->
                <div class="d-md-none">
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-sm btn-outline-secondary dropdown-toggle" data-bs-toggle="dropdown">
                            <i class="bi bi-three-dots-vertical"></i> Menu
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="{{ route('escalas-publicadas.edit-rapido', $escalaPublicada) }}"><i class="bi bi-lightning-charge text-success me-2"></i>Atribuição Rápida</a></li>
                            <li><a class="dropdown-item" href="{{ route('escalas-publicadas.calendar') }}?mes={{ $escalaPublicada->ano }}-{{ str_pad($escalaPublicada->mes, 2, '0', STR_PAD_LEFT) }}"><i class="bi bi-calendar3 me-2"></i>Ver Calendário</a></li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>
                            <li><a class="dropdown-item" href="{{ route('alocacoes.index') }}"><i class="bi bi-card-list me-2"></i>Lista</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="card card-slab mb-4">
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <div><strong>Unidade:</strong> {{ $escalaPublicada->unidade->nome }}</div>
                        <div class="text-muted">{{ $escalaPublicada->unidade->cidade->nome ?? 'N/A' }} - {{ $escalaPublicada->unidade->cidade->estado ?? '' }}</div>
                    </div>
                    <div class="col-md-4">
                        <div><strong>Referência:</strong> {{ \Carbon\Carbon::create($escalaPublicada->ano, $escalaPublicada->mes, 1)->locale('pt_BR')->isoFormat('MMMM/YYYY') }}</div>
                        <div class="text-muted">Status: <span class="badge bg-{{ $escalaPublicada->status === 'publicado' ? 'success' : 'warning' }}">{{ $escalaPublicada->status }}</span></div>
                    </div>
                    <div class="col-md-4">
                        <div><strong>Preenchidos:</strong> {{ $escalaPublicada->preenchidos }} / {{ $escalaPublicada->total_slots }}</div>
                        <div class="text-muted">Buracos: <span class="text-danger">{{ $escalaPublicada->buracos }}</span> • Taxa: <span class="fw-semibold">{{ $escalaPublicada->taxa }}%</span></div>
                    </div>
                </div>
            </div>
        </div>

        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif
        @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ $errors->first() }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        <div class="card card-slab">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-bordered table-hover table-fixed mb-0">
                        <thead>
                            <tr>
                                <th rowspan="2" style="width: 80px; vertical-align: middle;">Week</th>
                                <th rowspan="2" style="width: 60px; vertical-align: middle;">Day</th>
                                <th rowspan="2" style="width: 120px; vertical-align: middle;">Dia da Semana</th>
                                @foreach($turnosAtivos as $turno)
                                @php $colspan = isset($setoresAtivosPorTurno[$turno->id]) ? count($setoresAtivosPorTurno[$turno->id]) : 0; @endphp
                                <th colspan="{{ $colspan }}" class="turno-header">
                                    {{ ucfirst($turno->nome) }}
                                    <br>
                                    <small style="font-weight: 400; font-size: 0.75rem;">
                                        {{ \Carbon\Carbon::parse($turno->hora_inicio)->format('H:i') }} - {{ \Carbon\Carbon::parse($turno->hora_fim)->format('H:i') }}
                                    </small>
                                </th>
                                @endforeach
                            </tr>
                            <tr>
                                @foreach($turnosAtivos as $turno)
                                @foreach(($setoresAtivosPorTurno[$turno->id] ?? []) as $setor)
                                <th class="setor-subheader">{{ ucfirst($setor->nome) }}</th>
                                @endforeach
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($rows as $row)
                            <tr>
                                <td class="text-center fw-semibold">Semana {{ $row['semana'] }}</td>
                                <td class="text-center">{{ $row['dia'] }}</td>
                                <td>{{ ucfirst($row['weekday']) }}</td>
                                @foreach($turnosAtivos as $turno)
                                @foreach(($setoresAtivosPorTurno[$turno->id] ?? []) as $setor)
                                @php
                                $slots = $row['slots'][$turno->id][$setor->id] ?? [];
                                @endphp
                                <td class="slot-cell">
                                    @if(count($slots) > 0)
                                    <div class="d-flex flex-column gap-1">
                                        @foreach($slots as $slot)
                                        <span class="slot-badge {{ $slot->plantonista ? 'preenchido' : 'vago' }}"
                                            data-bs-toggle="modal"
                                            data-bs-target="#modal-{{ $slot->id }}">
                                            @if($slot->plantonista)
                                            {{ $slot->plantonista->nome }}
                                            @else
                                            <i class="bi bi-dash-circle"></i> Vago
                                            @endif
                                        </span>

                                        <!-- Modal para edição -->
                                        <div class="modal fade" id="modal-{{ $slot->id }}" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Editar Slot</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <form id="form-{{ $slot->id }}" action="{{ route('escalas-publicadas.alocacoes.update', $slot) }}" method="POST">
                                                        @csrf
                                                        @method('PUT')
                                                        <div class="modal-body">
                                                            <div class="mb-3">
                                                                <label class="form-label">Data</label>
                                                                <input type="text" class="form-control" value="{{ $row['data']->format('d/m/Y') }}" disabled>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label">Turno / Setor</label>
                                                                <input type="text" class="form-control" value="{{ $turno->nome }} • {{ $setor->nome }}" disabled>
                                                            </div>
                                                            <div class="mb-3">
                                                                <label class="form-label">Plantonista</label>
                                                                @php
                                                                $diaKey = $row['data']->format('Y-m-d');
                                                                // Extrair apenas a parte da hora, mesmo que venha com data
                                                                $horaInicioAtual = $turno->hora_inicio ? \Carbon\Carbon::parse($turno->hora_inicio)->format('H:i:s') : '00:00:00';
                                                                $horaFimAtual = $turno->hora_fim ? \Carbon\Carbon::parse($turno->hora_fim)->format('H:i:s') : '00:00:00';
                                                                // Janela do turno atual
                                                                $inicioNovo = \Carbon\Carbon::parse($diaKey.' '.$horaInicioAtual);
                                                                $fimNovo = \Carbon\Carbon::parse($diaKey.' '.$horaFimAtual);
                                                                if ($fimNovo->lessThanOrEqualTo($inicioNovo)) { $fimNovo->addDay(); }
                                                                @endphp
                                                                <select name="plantonista_id" class="form-select">
                                                                    <option value="">— Selecionar —</option>
                                                                    @foreach($plantonistas as $p)
                                                                    @php
                                                                    $disabled = false;
                                                                    if (!empty($ocupacaoPorDia[$diaKey][$p->id])) {
                                                                    foreach ($ocupacaoPorDia[$diaKey][$p->id] as $ocup) {
                                                                    // Normalizar para HH:MM:SS caso venha com data
                                                                    $iniHora = $ocup['inicio'] ? \Carbon\Carbon::parse($ocup['inicio'])->format('H:i:s') : '00:00:00';
                                                                    $fimHora = $ocup['fim'] ? \Carbon\Carbon::parse($ocup['fim'])->format('H:i:s') : '00:00:00';
                                                                    $ini = \Carbon\Carbon::parse($diaKey.' '.$iniHora);
                                                                    $fim = \Carbon\Carbon::parse($diaKey.' '.$fimHora);
                                                                    if ($fim->lessThanOrEqualTo($ini)) { $fim->addDay(); }
                                                                    // Sobreposição: (inicioNovo < fim) && (ini < fimNovo)
                                                                        if ($inicioNovo->lt($fim) && $ini->lt($fimNovo)) { $disabled = true; break; }
                                                                        }
                                                                        }
                                                                        @endphp
                                                                        <option value="{{ $p->id }}" {{ $slot->plantonista_id == $p->id ? 'selected' : '' }} {{ $disabled && $slot->plantonista_id != $p->id ? 'disabled' : '' }}>
                                                                            {{ $p->nome }} {{ $disabled && $slot->plantonista_id != $p->id ? '(conflito)' : '' }}
                                                                        </option>
                                                                        @endforeach
                                                                </select>
                                                                <small class="text-muted">Opções marcadas como (conflito) estão desativadas por sobreposição de horário neste dia.</small>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                            @if($slot->plantonista_id)
                                                            <button type="button" class="btn btn-outline-danger" onclick="clearAssignment('form-{{ $slot->id }}')">Limpar</button>
                                                            @endif
                                                            <button type="submit" class="btn btn-primary">Salvar</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                    @else
                                    <span class="text-muted text-center d-block">—</span>
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
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>