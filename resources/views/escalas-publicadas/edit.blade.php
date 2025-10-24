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
            <a href="{{ route('alocacoes.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-card-list"></i> Lista</a>
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
                <div class="p-3 border-bottom bg-light">
                    <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#modalAtribuicaoRapida">
                        <i class="bi bi-lightning-charge me-1"></i>
                        Atribuição Rápida
                    </button>
                    <small class="text-muted ms-2">Clique para alocar plantonistas rapidamente</small>
                </div>
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
                            <span class="badge bg-success-subtle text-success border">
                                <i class="bi bi-check-circle"></i> Disponível
                            </span>
                            <span class="badge bg-secondary-subtle text-secondary border ms-2">
                                <i class="bi bi-x-circle"></i> Conflito de horário
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
                        <span id="plantonistaSelecionadoInfo"></span>
                    </div>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let plantonistaSelecionado = null;
        const escalaPublicadaId = {
            {
                $escalaPublicada - > id
            }
        };

        // Carregar plantonistas quando abrir o modal
        document.getElementById('modalAtribuicaoRapida').addEventListener('shown.bs.modal', async function() {
            await carregarPlantonistas();
        });

        async function carregarPlantonistas() {
            const lista = document.getElementById('listaPlantonistas');
            try {
                console.log('Carregando plantonistas...');
                const url = '{{ url("/api/plantonistas-ativos") }}';
                console.log('URL:', url);

                const resp = await fetch(url);
                console.log('Response status:', resp.status);

                if (!resp.ok) {
                    throw new Error(`HTTP error! status: ${resp.status}`);
                }

                const plantonistas = await resp.json();
                console.log('Plantonistas carregados:', plantonistas.length);

                if (!plantonistas || plantonistas.length === 0) {
                    lista.innerHTML = '<div class="col-12 text-center text-muted py-3">Nenhum plantonista ativo encontrado</div>';
                    return;
                }

                lista.innerHTML = plantonistas.map(p => `
                    <div class="col-md-6">
                        <div class="card card-plantonista h-100" data-id="${p.id}" data-nome="${p.nome}" onclick="selecionarPlantonista(${p.id}, '${p.nome.replace(/'/g, "\\'")}')">
                            <div class="card-body p-2">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-person-circle fs-4 text-primary me-2"></i>
                                    <div class="flex-grow-1">
                                        <div class="fw-semibold">${p.nome}</div>
                                        <small class="text-muted">${p.crm || ''} ${p.especialidade ? '- ' + p.especialidade : ''}</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `).join('');

                // Adicionar estilos aos cards
                document.querySelectorAll('.card-plantonista').forEach(card => {
                    card.style.cursor = 'pointer';
                    card.style.transition = 'all 0.2s';
                    card.addEventListener('mouseenter', () => {
                        card.style.borderColor = '#0d6efd';
                        card.style.backgroundColor = '#f8f9fa';
                    });
                    card.addEventListener('mouseleave', () => {
                        if (!card.classList.contains('selected')) {
                            card.style.borderColor = '#dee2e6';
                            card.style.backgroundColor = 'white';
                        }
                    });
                });
            } catch (e) {
                console.error('Erro ao carregar plantonistas:', e);
                lista.innerHTML = '<div class="col-12 text-danger text-center py-3">Erro ao carregar plantonistas: ' + e.message + '</div>';
            }
        }

        function selecionarPlantonista(id, nome) {
            plantonistaSelecionado = {
                id,
                nome
            };

            // Atualizar visual
            document.querySelectorAll('.card-plantonista').forEach(card => {
                card.classList.remove('selected');
                card.style.borderColor = '#dee2e6';
                card.style.backgroundColor = 'white';
            });

            const cardSelecionado = document.querySelector(`.card-plantonista[data-id="${id}"]`);
            cardSelecionado.classList.add('selected');
            cardSelecionado.style.borderColor = '#198754';
            cardSelecionado.style.backgroundColor = '#d1e7dd';

            document.getElementById('plantonistaSelecionadoInfo').innerHTML = `
                <i class="bi bi-person-check text-success"></i>
                Selecionado: <strong>${nome}</strong>. Clique em um slot vago abaixo para atribuir.
            `;

            // Ativar cliques nos badges vagos
            ativarAssignmentRapido();
        }

        function ativarAssignmentRapido() {
            document.querySelectorAll('.slot-badge.vago').forEach(badge => {
                badge.style.cursor = 'pointer';
                badge.style.border = '2px dashed #198754';

                badge.onclick = async function(e) {
                    e.stopPropagation();

                    if (!plantonistaSelecionado) {
                        alert('Selecione um plantonista primeiro no modal de Atribuição Rápida');
                        return;
                    }

                    // Pegar o ID do modal associado ao badge
                    const modalId = badge.getAttribute('data-bs-target').replace('#', '');
                    const modal = document.getElementById(modalId);
                    const form = modal.querySelector('form');
                    const select = form.querySelector('select[name="plantonista_id"]');

                    // Definir o plantonista
                    select.value = plantonistaSelecionado.id;

                    // Submeter automaticamente
                    form.submit();
                };
            });
        }

        // Busca de plantonistas
        document.getElementById('searchPlantonista')?.addEventListener('input', function(e) {
            const termo = e.target.value.toLowerCase();
            document.querySelectorAll('.card-plantonista').forEach(card => {
                const nome = card.getAttribute('data-nome').toLowerCase();
                const text = card.textContent.toLowerCase();
                card.closest('.col-md-6').style.display = (nome.includes(termo) || text.includes(termo)) ? '' : 'none';
            });
        });
    </script>
</body>

</html>