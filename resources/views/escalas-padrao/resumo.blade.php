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
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h1 class="page-title h3 mb-0"><i class="bi bi-diagram-3 me-1"></i> Padrões de Escala</h1>
            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Voltar</a>
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
                                <i class="bi bi-eye"></i> Ver
                            </a>
                            @else
                            <a href="{{ route('escalas-padrao.create', $item['unidade']) }}" class="btn btn-sm btn-primary w-100">
                                <i class="bi bi-plus-circle"></i> Criar Escala
                            </a>
                            @endif
                            <button class="btn btn-sm btn-outline-success w-100" disabled title="Em breve">
                                <i class="bi bi-lightning-charge"></i> Atribuição Rápida
                            </button>
                            <a href="{{ route('unidades.show', $item['unidade']) }}" class="btn btn-sm btn-outline-secondary w-100">
                                <i class="bi bi-hospital"></i> Unidade
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>