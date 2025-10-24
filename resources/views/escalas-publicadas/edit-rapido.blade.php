<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Edição Rápida - {{ $escalaPublicada->unidade->nome }} ({{ \Carbon\Carbon::create($escalaPublicada->ano, $escalaPublicada->mes, 1)->locale('pt_BR')->isoFormat('MMMM/YYYY') }})</title>
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

        .badge-slot {
            cursor: pointer;
            transition: all 0.2s;
            position: relative;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 10ch;
            white-space: nowrap;
            padding: .25rem .5rem;
            border-radius: 6px;
            font-size: .8rem;
            border: 1px solid;
            margin-bottom: .25rem;
        }

        .badge-slot:hover {
            transform: scale(1.05);
            box-shadow: 0 2px 8px rgba(0, 0, 0, .15);
        }

        /* Slot ocupado (plantonista alocado) */
        .badge-slot.ocupado {
            background: rgba(13, 110, 253, .12);
            color: #0d6efd;
            border-color: rgba(13, 110, 253, .35);
        }

        /* Slot ocupado pelo plantonista selecionado */
        .badge-slot.ocupado-selecionado {
            background: rgba(25, 135, 84, .12);
            color: #198754;
            border-color: rgba(25, 135, 84, .35);
        }

        /* Slot vago (buraco) */
        .badge-slot.buraco {
            background: rgba(220, 53, 69, .12);
            color: #dc3545;
            border-color: rgba(220, 53, 69, .35);
        }

        /* Slot vago disponível para o plantonista selecionado */
        .badge-slot.buraco-disponivel {
            animation: pulse 2s infinite;
            border-color: rgba(220, 53, 69, .55);
        }

        /* Slot vago indisponível (conflito de horário) */
        .badge-slot.buraco-indisponivel {
            cursor: not-allowed;
            opacity: 1;
            border-style: dashed;
            background: rgb(235 206 209 / 12%) !important;
            color: #fbc6c6 !important;
            border-color: rgba(220, 53, 69, .35);
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

        .search-plantonista {
            position: sticky;
            top: 0;
            background: white;
            z-index: 1000;
            padding-bottom: 1rem;
            border-bottom: 2px solid #e9ecef;
        }
    </style>
</head>

<body>
    <div class="container-fluid py-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="d-flex align-items-center gap-2">
                <h1 class="page-title h4 mb-0">
                    <i class="bi bi-lightning-charge-fill text-success me-1"></i>
                    Edição Rápida
                </h1>
                <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-house"></i></a>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ route('escalas-publicadas.edit', $escalaPublicada) }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-pencil"></i> Edição Normal
                </a>
                <a href="{{ route('alocacoes.index') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-card-list"></i> Lista
                </a>
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
                        <div><strong>Preenchidos:</strong> <span id="countPreenchidos">{{ $escalaPublicada->preenchidos }}</span> / {{ $escalaPublicada->total_slots }}</div>
                        <div class="text-muted">Buracos: <span class="text-danger" id="countBuracos">{{ $escalaPublicada->buracos }}</span> • Taxa: <span class="fw-semibold" id="countTaxa">{{ $escalaPublicada->taxa }}%</span></div>
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
                                        <span class="badge-slot {{ $slot->plantonista ? 'ocupado' : 'buraco' }}"
                                            data-slot-id="{{ $slot->id }}"
                                            data-data="{{ $row['data']->format('Y-m-d') }}"
                                            data-turno-id="{{ $turno->id }}"
                                            data-setor-id="{{ $setor->id }}"
                                            data-turno-inicio="{{ $turno->hora_inicio }}"
                                            data-turno-fim="{{ $turno->hora_fim }}"
                                            data-plantonista-id="{{ $slot->plantonista_id ?? '' }}">
                                            @if($slot->plantonista)
                                            {{ $slot->plantonista->nome }}
                                            @else
                                            <i class="bi bi-dash-circle"></i> Vago
                                            @endif
                                        </span>
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
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <!-- Metadados seguros para uso no JavaScript -->
    <input type="hidden" id="escalaId" value="{{ $escalaPublicada->id }}">
    <input type="hidden" id="totalSlots" value="{{ $escalaPublicada->total_slots }}">

    <script>
        // Estado global
        let plantonistaSelecionado = null;
        let plantonistas = [];
        let alocacoes = {}; // {slotId: plantonista_id}
        const escalaPublicadaId = parseInt(document.getElementById('escalaId').value, 10);
        const totalSlots = parseInt(document.getElementById('totalSlots').value, 10);

        // Carregar plantonistas e alocações ao iniciar
        $(document).ready(function() {
            console.log('Document ready - iniciando carregamento');
            console.log('jQuery version:', $.fn.jquery);
            console.log('Select2 disponível:', typeof $.fn.select2);
            console.log('Elemento #selectPlantonista existe:', $('#selectPlantonista').length);

            carregarPlantonistas();
            carregarAlocacoes();
        });

        // Função para carregar plantonistas
        async function carregarPlantonistas() {
            try {
                console.log('Carregando plantonistas...');
                const url = '{{ url("/api/plantonistas-ativos") }}';
                console.log('URL:', url);

                const response = await fetch(url);
                console.log('Response status:', response.status);

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                plantonistas = await response.json();
                console.log('Plantonistas carregados:', plantonistas.length, plantonistas);

                if (!plantonistas || plantonistas.length === 0) {
                    console.warn('Nenhum plantonista retornado da API');
                    alert('Nenhum plantonista ativo encontrado no sistema');
                    return;
                }

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

                console.log('Select2 inicializado com sucesso');
            } catch (error) {
                console.error('Erro ao carregar plantonistas:', error);
                alert('Erro ao carregar plantonistas: ' + error.message);
            }
        }

        // Função para carregar alocações existentes
        async function carregarAlocacoes() {
            // Mapear alocações já exibidas na página
            document.querySelectorAll('.badge-slot.ocupado').forEach(slot => {
                const slotId = slot.getAttribute('data-slot-id');
                const plantonista_id = slot.getAttribute('data-plantonista-id');
                if (slotId && plantonista_id) {
                    alocacoes[slotId] = parseInt(plantonista_id);
                }
            });
            atualizarSlotsDisponiveis();
        }

        // Matcher customizado para busca por nome, CRM ou especialidade
        function matchCustom(params, data) {
            if ($.trim(params.term) === '') {
                return data;
            }

            const term = params.term.toLowerCase();
            const plantonista = plantonistas.find(p => p.id == data.id);

            if (!plantonista) return null;

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
            $('#btnLimparSelecao').show();
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

        // Converter HH:MM para minutos (aceita também datetime completo)
        function timeToMinutes(time) {
            if (!time) return 0;
            if (time.includes(' ')) {
                time = time.split(' ')[1];
            }
            const parts = time.split(':');
            const h = parseInt(parts[0]);
            const m = parseInt(parts[1]);
            return h * 60 + m;
        }

        // Verificar conflito de horário entre turnos
        function temConflito(turno1Inicio, turno1Fim, turno2Inicio, turno2Fim) {
            const t1i = timeToMinutes(turno1Inicio);
            const t1f = timeToMinutes(turno1Fim);
            const t2i = timeToMinutes(turno2Inicio);
            const t2f = timeToMinutes(turno2Fim);

            const t1f_ajustado = t1f < t1i ? t1f + 1440 : t1f;
            const t2f_ajustado = t2f < t2i ? t2f + 1440 : t2f;

            return (t1i < t2f_ajustado && t1f_ajustado > t2i);
        }

        // Atualizar visualização dos slots disponíveis
        function atualizarSlotsDisponiveis() {
            if (!plantonistaSelecionado) {
                // Resetar todos os slots
                document.querySelectorAll('.badge-slot').forEach(slot => {
                    const slotId = slot.getAttribute('data-slot-id');
                    const pid = alocacoes[slotId];

                    slot.classList.remove('ocupado-selecionado', 'buraco-disponivel', 'buraco-indisponivel');

                    if (pid) {
                        slot.classList.add('ocupado');
                        slot.classList.remove('buraco');
                    } else {
                        slot.classList.add('buraco');
                        slot.classList.remove('ocupado');
                    }
                });
                return;
            }

            // Para cada slot, verificar disponibilidade
            document.querySelectorAll('.badge-slot').forEach(slot => {
                const slotId = slot.getAttribute('data-slot-id');
                const data = slot.getAttribute('data-data');
                const turnoInicio = slot.getAttribute('data-turno-inicio');
                const turnoFim = slot.getAttribute('data-turno-fim');

                // Verificar se já está ocupado
                if (alocacoes[slotId]) {
                    slot.classList.remove('buraco', 'buraco-disponivel', 'buraco-indisponivel');
                    slot.classList.add('ocupado');

                    // Se ocupado pelo plantonista selecionado, destacar
                    if (Number(alocacoes[slotId]) === Number(plantonistaSelecionado.id)) {
                        slot.classList.add('ocupado-selecionado');
                    } else {
                        slot.classList.remove('ocupado-selecionado');
                    }
                    return;
                }

                // Verificar conflitos de horário no mesmo dia
                const temConflitoDia = Array.from(document.querySelectorAll('.badge-slot')).some(s => {
                    const sData = s.getAttribute('data-data');
                    const sId = s.getAttribute('data-slot-id');
                    const sPid = alocacoes[sId];

                    // Ignorar se não é mesmo dia ou não é o plantonista selecionado
                    if (sData !== data || !sPid || Number(sPid) !== Number(plantonistaSelecionado.id)) return false;

                    // Ignorar se é o mesmo slot
                    if (sId === slotId) return false;

                    const sInicio = s.getAttribute('data-turno-inicio');
                    const sFim = s.getAttribute('data-turno-fim');

                    return temConflito(turnoInicio, turnoFim, sInicio, sFim);
                });

                if (temConflitoDia) {
                    slot.classList.remove('ocupado', 'ocupado-selecionado', 'buraco-disponivel');
                    slot.classList.add('buraco', 'buraco-indisponivel');
                    slot.title = 'Conflito de horário com outra alocação';
                    return;
                }

                // Disponível
                slot.classList.remove('ocupado', 'ocupado-selecionado', 'buraco-indisponivel');
                slot.classList.add('buraco', 'buraco-disponivel');
                slot.title = 'Clique para alocar ' + plantonistaSelecionado.nome;
            });
        }

        // Alocar/desalocar plantonista ao clicar no slot
        document.addEventListener('click', async function(e) {
            const slot = e.target.closest('.badge-slot');
            if (!slot) return;

            const slotId = slot.getAttribute('data-slot-id');

            // Se está ocupado e clicou, desalocar
            if (slot.classList.contains('ocupado')) {
                if (confirm('Deseja remover este plantonista?')) {
                    const success = await salvarAlocacao(slotId, null);
                    if (success) {
                        delete alocacoes[slotId];
                        slot.setAttribute('data-plantonista-id', '');
                        slot.classList.remove('ocupado', 'ocupado-selecionado');
                        slot.classList.add('buraco');
                        slot.innerHTML = '<i class="bi bi-dash-circle"></i> Vago';
                        atualizarSlotsDisponiveis();
                        atualizarContadores();
                    }
                }
                return;
            }

            // Se está indisponível, não fazer nada
            if (slot.classList.contains('buraco-indisponivel')) {
                alert('Este slot não está disponível para o plantonista selecionado devido a conflito de horário');
                return;
            }

            // Se não tem plantonista selecionado
            if (!plantonistaSelecionado) {
                alert('Selecione um plantonista primeiro no dropdown acima');
                return;
            }

            // Alocar
            const success = await salvarAlocacao(slotId, plantonistaSelecionado.id);
            if (success) {
                alocacoes[slotId] = plantonistaSelecionado.id;
                slot.setAttribute('data-plantonista-id', plantonistaSelecionado.id);
                slot.classList.remove('buraco', 'buraco-disponivel', 'buraco-indisponivel');
                slot.classList.add('ocupado', 'ocupado-selecionado');
                slot.textContent = plantonistaSelecionado.nome;
                slot.title = plantonistaSelecionado.nome;
                atualizarSlotsDisponiveis();
                atualizarContadores();
            }
        });

        // Função para salvar/remover alocação via formulário (usa a rota PUT existente)
        async function salvarAlocacao(slotId, plantonista_id) {
            try {
                const url = `{{ url('/escalas-publicadas/alocacoes') }}/${slotId}`;
                const formData = new FormData();
                formData.append('_method', 'PUT');
                formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
                if (plantonista_id) {
                    formData.append('plantonista_id', plantonista_id);
                } else {
                    formData.append('plantonista_id', '');
                }

                const response = await fetch(url, {
                    method: 'POST',
                    body: formData
                });

                if (!response.ok) {
                    const text = await response.text();
                    console.error('Erro na resposta:', text);
                    alert('Erro ao salvar alocação. Verifique o console.');
                    return false;
                }

                return true;
            } catch (error) {
                console.error('Erro ao salvar alocação:', error);
                alert('Erro ao salvar alocação. Verifique a conexão.');
                return false;
            }
        }

        // Atualizar contadores de preenchidos/buracos/taxa
        function atualizarContadores() {
            const preenchidos = Object.keys(alocacoes).length;
            const buracos = totalSlots - preenchidos;
            const taxa = totalSlots > 0 ? Math.round((preenchidos / totalSlots) * 100) : 0;

            document.getElementById('countPreenchidos').textContent = preenchidos;
            document.getElementById('countBuracos').textContent = buracos;
            document.getElementById('countTaxa').textContent = taxa + '%';
        }
    </script>
</body>

</html>