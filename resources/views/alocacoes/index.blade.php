<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Escalas Publicadas - Sistema de Escala Médica</title>
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

        .badge-soft {
            background: rgba(255, 255, 255, .25);
            color: #fff;
            border: 1px solid rgba(255, 255, 255, .35);
        }

        .stat-slab {
            background: #f8f9fb;
            border-radius: 12px;
            padding: .85rem;
        }

        .stat-slab .label {
            color: #6c757d;
            font-size: .85rem;
        }

        .stat-slab .value {
            font-size: 1.35rem;
            font-weight: 700;
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
            <h1 class="page-title h3 mb-0"><i class="bi bi-calendar-check me-1"></i> Escalas Publicadas</h1>
            <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Voltar</a>
        </div>

        <div class="card filter-card mb-4">
            <div class="card-body">
                <form class="row g-2" method="GET" action="{{ route('alocacoes.index') }}">
                    <div class="col-md-4">
                        <input type="text" class="form-control" placeholder="Buscar por cidade ou unidade" name="q" value="{{ request('q') }}">
                    </div>
                    <div class="col-md-3">
                        <input type="month" class="form-control" name="mes" value="{{ request('mes') }}">
                    </div>
                    <div class="col-md-2">
                        <select name="status" class="form-select">
                            <option value="">Status (todos)</option>
                            <option value="publicado" {{ request('status')==='publicado' ? 'selected' : '' }}>Publicado</option>
                            <option value="em_edicao" {{ request('status')==='em_edicao' ? 'selected' : '' }}>Em Edição</option>
                        </select>
                    </div>
                    <div class="col-md-3 text-md-end">
                        <button class="btn btn-primary"><i class="bi bi-funnel me-1"></i> Filtrar</button>
                        <a href="{{ route('alocacoes.index') }}" class="btn btn-light">Limpar</a>
                    </div>
                </form>
            </div>
        </div>


        @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        @endif

        @if(empty($escalasPublicadas))
        <div class="alert alert-info">Nenhuma escala publicada encontrada.</div>
        @else
        <div class="row g-4">
            @foreach($escalasPublicadas as $escala)
            <div class="col-lg-4 col-md-6">
                <div class="card schedule-card h-100">
                    <div class="card-header-gradient d-flex justify-content-between align-items-center">
                        <div>
                            <i class="bi bi-calendar-event me-1"></i>
                            {{ \Carbon\Carbon::parse($escala['mes'].'-01')->locale('pt_BR')->isoFormat('MMMM/YYYY') }}
                        </div>
                        <span class="badge badge-soft">{{ $escala['status'] === 'publicado' ? 'Publicado' : 'Em Edição' }}</span>
                    </div>
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-2 small-note">
                            <i class="bi bi-geo-alt me-1"></i>
                            {{ $escala['cidade'] }} - {{ $escala['estado'] }}
                        </div>
                        <h5 class="mb-3">{{ $escala['unidade'] }}</h5>

                        <div class="row text-center mb-3 g-2">
                            <div class="col-4">
                                <div class="stat-slab">
                                    <div class="label">Total de Slots</div>
                                    <div class="value">{{ number_format($escala['total_slots']) }}</div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="stat-slab">
                                    <div class="label">Preenchidos</div>
                                    <div class="value text-success">{{ number_format($escala['preenchidos']) }}</div>
                                </div>
                            </div>
                            <div class="col-4">
                                <div class="stat-slab">
                                    <div class="label">Buracos</div>
                                    <div class="value text-danger">{{ number_format($escala['buracos']) }}</div>
                                </div>
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <div class="small-note">Taxa Preenchimento</div>
                            <div><span class="badge bg-{{ $escala['taxa'] >= 80 ? 'success' : ($escala['taxa'] >= 40 ? 'warning' : 'danger') }}">{{ $escala['taxa'] }}%</span></div>
                        </div>
                    </div>
                    <div class="card-footer bg-transparent border-0 pb-3 px-3">
                        <div class="d-flex gap-2">
                            <a href="#" class="btn btn-sm btn-outline-primary w-100" onclick="alert('Funcionalidade em desenvolvimento'); return false;">
                                <i class="bi bi-eye"></i> Ver
                            </a>
                            <a href="#" class="btn btn-sm btn-outline-warning w-100" onclick="alert('Funcionalidade em desenvolvimento'); return false;">
                                <i class="bi bi-pencil"></i> Editar
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>