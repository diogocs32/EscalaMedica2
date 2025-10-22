<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendário das Escalas Publicadas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/main.min.css" rel="stylesheet">
    <style>
        body {
            background: #f6f7fb;
        }

        .page-title {
            display: flex;
            align-items: center;
            gap: .75rem;
        }

        .card-shell {
            border: 0;
            border-radius: 14px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, .08);
        }

        #calendar {
            background: #fff;
            border-radius: 12px;
            padding: 8px;
        }

        .fc .fc-toolbar-title {
            font-size: 1.25rem;
            font-weight: 700;
        }

        .fc .fc-button-primary {
            background: #4c6ef5;
            border-color: #4c6ef5;
        }

        .legend .badge {
            font-size: .75rem;
        }

        /* Melhorar legibilidade dos eventos (quebra de linha e tamanhos) */
        .fc .fc-event-title,
        .fc .fc-event-time {
            white-space: normal;
        }

        .fc .fc-daygrid-event {
            padding: .15rem .25rem;
        }

        .fc .fc-timegrid-event {
            padding: .25rem .35rem;
        }

        .fc .fc-event .small {
            font-size: .78rem;
            line-height: 1.1;
        }

        .fc .fc-event .muted {
            opacity: .85;
        }
    </style>
</head>

<body>
    <div class="container py-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="page-title h3 mb-0"><i class="bi bi-calendar3 me-1"></i> Calendário das Escalas Publicadas</h1>
            <div class="d-flex gap-2">
                <a href="{{ route('alocacoes.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-card-list"></i> Lista</a>
                <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-house"></i> Dashboard</a>
            </div>
        </div>

        <div class="card card-shell mb-3">
            <div class="card-body">
                <form class="row g-2" id="filterForm">
                    <div class="col-md-4">
                        <label class="form-label mb-1">Unidade (opcional)</label>
                        <select name="unidade_id" id="unidade_id" class="form-select">
                            <option value="">Todas</option>
                            @foreach($unidades as $u)
                            <option value="{{ $u->id }}">{{ $u->nome }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label mb-1">Plantonista (opcional)</label>
                        <select name="plantonista_id" id="plantonista_id" class="form-select">
                            <option value="">Todos</option>
                            @foreach($plantonistas as $p)
                            <option value="{{ $p->id }}">{{ $p->nome }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button type="button" id="applyFilters" class="btn btn-primary w-100"><i class="bi bi-funnel me-1"></i> Aplicar filtros</button>
                    </div>
                </form>
            </div>
        </div>

        <div class="mb-2 legend">
            <span class="badge" style="background:#4dabf7">Manhã</span>
            <span class="badge" style="background:#51cf66">Tarde</span>
            <span class="badge" style="background:#845ef7">Noite/Corujão</span>
        </div>

        <div id="calendar"></div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.15/index.global.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const calendarEl = document.getElementById('calendar');
            const unidadeSel = document.getElementById('unidade_id');
            const plantonistaSel = document.getElementById('plantonista_id');
            const applyBtn = document.getElementById('applyFilters');

            const calendar = new FullCalendar.Calendar(calendarEl, {
                initialView: 'timeGridWeek',
                height: 'auto',
                locale: 'pt-br',
                timeZone: 'local',
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek'
                },
                navLinks: true,
                selectable: false,
                editable: false,
                expandRows: true,
                dayMaxEventRows: 4,
                moreLinkClick: 'popover',
                displayEventEnd: true,
                eventDisplay: 'block',
                eventTimeFormat: {
                    hour: '2-digit',
                    minute: '2-digit',
                    meridiem: false
                },
                slotMinTime: '06:00:00',
                slotMaxTime: '24:00:00',
                events: function(fetchInfo, successCallback, failureCallback) {
                    const params = new URLSearchParams({
                        start: fetchInfo.startStr,
                        end: fetchInfo.endStr
                    });
                    if (unidadeSel.value) params.append('unidade_id', unidadeSel.value);
                    if (plantonistaSel.value) params.append('plantonista_id', plantonistaSel.value);

                    fetch(`{{ route('api.escalas-publicadas.events') }}?` + params.toString())
                        .then(r => r.json())
                        .then(data => successCallback(data))
                        .catch(err => failureCallback(err));
                },
                eventContent: function(arg) {
                    // Construir conteúdo mais legível (3 linhas): Nome, Setor/Turno, Horário
                    const p = arg.event.extendedProps || {};
                    const nome = (arg.event.title || '').split(' • ')[0] || (p.plantonista || 'Vago');
                    const setorTurno = [p.setor, p.turno].filter(Boolean).join(' • ');
                    const horario = arg.timeText || '';
                    const html = `
                        <div class="fc-event-title fw-semibold">${nome}</div>
                        ${setorTurno ? `<div class="small">${setorTurno}</div>` : ''}
                        ${horario ? `<div class="small muted">${horario}</div>` : ''}
                    `;
                    return {
                        html
                    };
                },
                eventDidMount: function(info) {
                    // Tooltip nativo do browser
                    const p = info.event.extendedProps;
                    const unidade = p.unidade ? `\nUnidade: ${p.unidade}` : '';
                    const cidade = p.cidade ? `\nCidade: ${p.cidade}` : '';
                    const setor = p.setor ? `\nSetor: ${p.setor}` : '';
                    const turno = p.turno ? `\nTurno: ${p.turno}` : '';
                    info.el.title = info.event.title + unidade + cidade + setor + turno;
                },
                eventClick: function(info) {
                    // Preenche modal com detalhes
                    const p = info.event.extendedProps || {};
                    const modal = new bootstrap.Modal(document.getElementById('eventDetailModal'));
                    document.getElementById('evtTitle').innerText = info.event.title;
                    document.getElementById('evtHorario').innerText = info.timeText || '';
                    document.getElementById('evtUnidade').innerText = p.unidade || '-';
                    document.getElementById('evtCidade').innerText = p.cidade || '-';
                    document.getElementById('evtSetor').innerText = p.setor || '-';
                    document.getElementById('evtTurno').innerText = p.turno || '-';
                    modal.show();
                }
            });

            calendar.render();

            applyBtn.addEventListener('click', function() {
                calendar.refetchEvents();
            });
        });
    </script>

    <!-- Modal de detalhes do evento -->
    <div class="modal fade" id="eventDetailModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-info-circle me-1"></i> Detalhes do Plantão</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-2"><strong id="evtTitle">Título</strong></div>
                    <div class="row g-2 small">
                        <div class="col-6"><span class="text-muted">Horário:</span> <span id="evtHorario">-</span></div>
                        <div class="col-6"><span class="text-muted">Unidade:</span> <span id="evtUnidade">-</span></div>
                        <div class="col-6"><span class="text-muted">Cidade:</span> <span id="evtCidade">-</span></div>
                        <div class="col-6"><span class="text-muted">Setor:</span> <span id="evtSetor">-</span></div>
                        <div class="col-6"><span class="text-muted">Turno:</span> <span id="evtTurno">-</span></div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>
</body>

</html>