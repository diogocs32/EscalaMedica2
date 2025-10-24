<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Padrões de Escala - Resumo Geral</title>
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

        .filter-card {
            border: 0;
            border-radius: 14px;
            box-shadow: 0 6px 28px rgba(0, 0, 0, .06);
        }

        .schedule-card {
            border: 0;
            border-radius: 14px;
            overflow: hidden;
            box-shadow: 0 8px 32px rgba(0, 0, 0, .08);
            transition: transform .25s ease;
        }

        .schedule-card:hover {
            transform: translateY(-4px);
        }

        .card-header-gradient {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #fff;
            padding: .85rem 1rem;
            font-weight: 600;
        }

        .metric {
            text-align: center;
        }

        .metric .label {
            color: #6c757d;
            font-size: .85rem;
        }

        .metric .value {
            font-size: 1.35rem;
            font-weight: 700;
        }

        .badge-soft {
            background: rgba(255, 255, 255, .25);
            color: #fff;
            border: 1px solid rgba(255, 255, 255, .35);
        }

        .btn-soft {
            border-radius: 10px;
        }

        .stat-slab {
            background: #f8f9fb;
            border-radius: 12px;
            padding: .85rem;
        }

        .small-note {
            color: #6c757d;
            font-size: .85rem;
        }
    </style>
</head>

<body>
    <div class="container py-4">
        {{-- Alerta de Confirmação de Substituição --}}
        @if(session('warning') && session('confirm_substituir'))
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle-fill me-2"></i>
            <strong>Atenção!</strong> {{ session('warning') }}
            <div class="mt-3">
                <form method="POST" action="{{ route('escalas-padrao.publicar', session('unidade_id')) }}" style="display: inline;">
                    @csrf
                    <input type="hidden" name="periodo" value="{{ session('periodo') }}">
                    <input type="hidden" name="substituir" value="1">
                    <button type="submit" class="btn btn-warning btn-sm">
                        <i class="bi bi-arrow-repeat"></i> Sim, substituir
                    </button>
                </form>
                <button type="button" class="btn btn-secondary btn-sm ms-2" data-bs-dismiss="alert">
                    Cancelar
                </button>
            </div>
        </div>
        @endif

        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="bi bi-x-circle-fill me-2"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="d-flex align-items-center gap-2">
                <h1 class="page-title h3 mb-0"><i class="bi bi-diagram-3 me-1"></i> Padrões de Escala</h1>
                <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-house"></i></a>
            </div>
        </div>

        <div class="card filter-card mb-4">
            <div class="card-body">
                <form class="row g-2" method="GET" action="{{ route('schedule-patterns') }}">
                    <div class="col-md-6">
                        <input type="text" class="form-control" placeholder="Buscar por nome da unidade" name="q" value="{{ $filtros['q'] ?? '' }}">
                    </div>
                    <div class="col-md-3">
                        <select name="status" class="form-select">
                            <option value="">Status (todos)</option>
                            <option value="ativo" {{ ($filtros['status'] ?? '')==='ativo' ? 'selected' : '' }}>Ativo</option>
                            <option value="inativo" {{ ($filtros['status'] ?? '')==='inativo' ? 'selected' : '' }}>Inativo</option>
                        </select>
                    </div>
                    <div class="col-md-3 text-md-end">
                        <button class="btn btn-primary"><i class="bi bi-funnel me-1"></i> Filtrar</button>
                        <a href="{{ route('schedule-patterns') }}" class="btn btn-light">Limpar</a>
                    </div>
                </form>
            </div>
        </div>

        @if(empty($cards))
        <div class="alert alert-info">Nenhuma unidade encontrada.</div>
        @endif

        <div class="row g-4">
            @foreach($cards as $item)
            <div class="col-lg-4 col-md-6">
                <div class="card schedule-card h-100">
                    <div class="card-header-gradient d-flex justify-content-between align-items-center">
                        <div>
                            <i class="bi bi-pin-angle me-1"></i>
                            Padrão {{ $item['unidade']->nome }}
                        </div>
                        <span class="badge badge-soft">{{ $item['status'] === 'inexistente' ? 'Sem escala' : ucfirst($item['status']) }}</span>
                    </div>
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2 small-note">
                            <i class="bi bi-geo-alt me-1"></i>
                            {{ $item['unidade']->cidade->nome ?? 'Cidade N/D' }}
                            @if(isset($item['unidade']->cidade->estado))
                            - {{ $item['unidade']->cidade->estado }}
                            @endif
                        </div>

                        <div class="row text-center mb-3 g-2">
                            <div class="col-4">
                                <div class="stat-slab">
                                    <div class="label">Total de Slots</div>
                                    <div class="value">{{ number_format($item['total_slots']) }}</div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="stat-slab">
                                    <div class="label">Preenchidos</div>
                                    <div class="value text-success">{{ number_format($item['preenchidos']) }}</div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="stat-slab">
                                    <div class="label">Buracos</div>
                                    <div class="value text-danger">{{ number_format($item['buracos']) }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div class="small-note">Taxa Preenchimento</div>
                            <div><span class="badge bg-{{ $item['taxa'] >= 80 ? 'success' : ($item['taxa'] >= 40 ? 'warning' : 'danger') }}">{{ $item['taxa'] }}%</span></div>
                        </div>
                        <div class="small-note">Slots Configurados: <strong>{{ number_format($item['slots_configurados']) }}</strong> <span class="text-muted">(linhas: {{ number_format($item['configs_linhas']) }})</span></div>
                    </div>
                    <div class="card-footer bg-transparent border-0 pb-3 px-3">
                        <div class="d-flex gap-2">
                            @if($item['escala'])
                            <a href="{{ route('schedule-patterns.schedule', $item['unidade']) }}" class="btn btn-sm btn-outline-primary w-100">
                                <i class="bi bi-pencil-square"></i> Editar
                            </a>
                            @else
                            <a href="{{ route('escalas-padrao.create', $item['unidade']) }}" class="btn btn-sm btn-primary w-100">
                                <i class="bi bi-plus-circle"></i> Criar Escala
                            </a>
                            @endif
                            @if($item['escala'])
                            <button class="btn btn-sm btn-success w-100" onclick="publicarEscala({{ $item['unidade']->id }})">
                                <i class="bi bi-calendar-check"></i> Publicar
                            </button>
                            @endif
                            <a href="{{ route('escalas-padrao.index', $item['unidade']) }}" class="btn btn-sm btn-outline-secondary w-100">
                                <i class="bi bi-hospital"></i> Unidade
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Modal de Publicação -->
    <div class="modal fade" id="modalPublicar" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-calendar-check"></i> Publicar Escala</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p class="text-muted mb-3">Selecione o período para gerar a escala com base no padrão de 5 semanas:</p>

                    <form id="formPublicar" method="POST">
                        @csrf
                        <input type="hidden" id="unidade_id_publicar" name="unidade_id">

                        <div class="mb-3">
                            <label for="ano_publicar" class="form-label">Ano</label>
                            <select class="form-select" id="ano_publicar" name="ano" required>
                                <option value="">Selecione o ano</option>
                                @php
                                $anoAtual = date('Y');
                                for ($ano = $anoAtual; $ano <= $anoAtual + 2; $ano++) {
                                    echo "<option value='$ano'>$ano</option>" ;
                                    }
                                    @endphp
                                    </select>
                        </div>

                        <div class="mb-3">
                            <label for="mes_publicar" class="form-label">Mês</label>
                            <select class="form-select" id="mes_publicar" name="mes" required>
                                <option value="">Selecione o mês</option>
                                <option value="01">Janeiro</option>
                                <option value="02">Fevereiro</option>
                                <option value="03">Março</option>
                                <option value="04">Abril</option>
                                <option value="05">Maio</option>
                                <option value="06">Junho</option>
                                <option value="07">Julho</option>
                                <option value="08">Agosto</option>
                                <option value="09">Setembro</option>
                                <option value="10">Outubro</option>
                                <option value="11">Novembro</option>
                                <option value="12">Dezembro</option>
                            </select>
                        </div>

                        <div class="alert alert-info">
                            <small>
                                <strong>Como funciona:</strong><br>
                                • O sistema vai mapear cada semana do mês escolhido para uma das 5 semanas do padrão<br>
                                • Cada dia receberá as configurações (turnos/setores) do dia da semana correspondente<br>
                                • Exemplo: 02/10/2025 (quarta-feira) = configurações da quarta da semana 1
                            </small>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-success" onclick="confirmarPublicacao()">
                        <i class="bi bi-check-lg"></i> Publicar Escala
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let modalPublicar;

        document.addEventListener('DOMContentLoaded', function() {
            modalPublicar = new bootstrap.Modal(document.getElementById('modalPublicar'));
        });

        function publicarEscala(unidadeId) {
            document.getElementById('unidade_id_publicar').value = unidadeId;
            document.getElementById('formPublicar').reset();
            document.getElementById('unidade_id_publicar').value = unidadeId;

            // Pré-selecionar ano atual
            document.getElementById('ano_publicar').value = new Date().getFullYear();

            modalPublicar.show();
        }

        function confirmarPublicacao() {
            const ano = document.getElementById('ano_publicar').value;
            const mes = document.getElementById('mes_publicar').value;
            const unidadeId = document.getElementById('unidade_id_publicar').value;

            if (!ano || !mes) {
                alert('Por favor, selecione o ano e o mês.');
                return;
            }

            // Construir período e URL de ação
            const periodo = `${ano}-${mes}`;
            const form = document.getElementById('formPublicar');
            form.action = `/EscalaMedica2/public/escalas-padrao/${unidadeId}/publicar?periodo=${periodo}`;

            // Submeter formulário via POST
            form.submit();
        }
    </script>
</body>

</html>