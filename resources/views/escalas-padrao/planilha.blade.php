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
                                    <span class="badge badge-soft badge-slot mb-1"
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

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        // Estado global
        let plantonistaSelecionado = null;
        let plantonistas = [];
        let alocacoes = {}; // {semana-dia-turno-setor-slot: plantonista_id}
        const unidadeId = parseInt('{{ $unidade->id }}');

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
                            slot.classList.remove('disponivel', 'indisponivel');
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
                // Resetar todos os slots
                document.querySelectorAll('.badge-slot').forEach(slot => {
                    slot.classList.remove('disponivel', 'indisponivel', 'ocupado');
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
                    slot.classList.remove('disponivel', 'indisponivel');
                    slot.classList.add('ocupado');
                    const plantonista = plantonistas.find(p => p.id == alocacoes[key]);
                    slot.textContent = plantonista ? plantonista.nome.split(' ')[0] : 'Ocupado';
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
                    slot.classList.remove('disponivel', 'ocupado');
                    slot.classList.add('indisponivel');
                    slot.title = 'Conflito de horário com outra alocação';
                    return;
                }

                // Disponível
                slot.classList.remove('indisponivel', 'ocupado');
                slot.classList.add('disponivel');
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
                    slot.classList.remove('ocupado');
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

                slot.classList.remove('disponivel');
                slot.classList.add('ocupado');
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