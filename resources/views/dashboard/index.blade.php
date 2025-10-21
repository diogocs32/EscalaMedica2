<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Sistema de Escalas Médicas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .sidebar {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }

        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
            border-radius: 8px;
            margin: 2px 0;
            transition: all 0.3s;
        }

        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            transform: translateX(5px);
        }

        .stats-card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
        }

        .stats-card:hover {
            transform: translateY(-5px);
        }

        .stats-card .card-body {
            padding: 2rem;
        }

        .stats-number {
            font-size: 3rem;
            font-weight: bold;
            margin: 0;
        }

        .navbar-custom {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }

        .quick-access-card {
            border: none;
            border-radius: 12px;
            background: #f8f9fa;
            transition: all 0.3s;
            cursor: pointer;
            height: 100%;
        }

        .quick-access-card:hover {
            background: white;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
            transform: translateY(-2px);
        }

        .content-card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
        }

        .empty-state {
            color: #6c757d;
            text-align: center;
            padding: 3rem 1rem;
        }

        .empty-state i {
            font-size: 4rem;
            margin-bottom: 1rem;
            opacity: 0.5;
        }
    </style>
</head>

<body class="bg-light">
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold" href="{{ route('dashboard') }}">
                <i class="bi bi-calendar-heart me-2"></i>Sistema de Escalas Médicas
            </a>
            <div class="navbar-nav ms-auto">
                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle text-white" href="#" role="button" data-bs-toggle="dropdown">
                        <i class="bi bi-person-circle me-1"></i>João Silva
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#"><i class="bi bi-person me-2"></i>Perfil</a></li>
                        <li><a class="dropdown-item" href="#"><i class="bi bi-gear me-2"></i>Configurações</a></li>
                        <li>
                            <hr class="dropdown-divider">
                        </li>
                        <li><a class="dropdown-item" href="#"><i class="bi bi-box-arrow-right me-2"></i>Sair</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <div class="col-md-3 col-lg-2 sidebar p-3">
                <nav class="nav flex-column">
                    <a class="nav-link active" href="{{ route('dashboard') }}">
                        <i class="bi bi-speedometer2 me-2"></i>Dashboard
                    </a>
                    <a class="nav-link" href="{{ route('alocacoes.index') }}">
                        <i class="bi bi-calendar-check me-2"></i>Escalas
                    </a>
                    <a class="nav-link" href="#">
                        <i class="bi bi-calendar-range me-2"></i>Padrões de Escala
                    </a>
                    <a class="nav-link" href="#">
                        <i class="bi bi-geo-alt me-2"></i>Cidades
                    </a>
                    <a class="nav-link" href="#">
                        <i class="bi bi-building me-2"></i>Unidades
                    </a>
                    <a class="nav-link" href="{{ route('setores.index') }}">
                        <i class="bi bi-diagram-3 me-2"></i>Setores
                    </a>
                    <a class="nav-link" href="{{ route('turnos.index') }}">
                        <i class="bi bi-clock me-2"></i>Turnos
                    </a>
                    <a class="nav-link" href="#">
                        <i class="bi bi-people me-2"></i>Plantonistas
                    </a>
                    <a class="nav-link" href="#">
                        <i class="bi bi-people-fill me-2"></i>Grupos de Acesso
                    </a>
                    <a class="nav-link" href="#">
                        <i class="bi bi-bar-chart me-2"></i>Relatórios
                    </a>
                    <a class="nav-link" href="#">
                        <i class="bi bi-shop me-2"></i>Marketplace
                    </a>
                </nav>
            </div>

            <!-- Main Content -->
            <div class="col-md-9 col-lg-10 p-4">
                <!-- Header -->
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h1 class="h3 mb-0"><i class="bi bi-speedometer2 me-2"></i>Dashboard</h1>
                        <p class="text-muted mb-0">Bem-vindo, João Silva!</p>
                    </div>
                    <div class="text-muted">
                        <i class="bi bi-calendar3 me-1"></i>{{ date('d/m/Y') }}
                    </div>
                </div>

                <!-- Stats Cards -->
                <div class="row mb-4">
                    <div class="col-md-3 mb-3">
                        <div class="card stats-card text-white" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                            <div class="card-body text-center">
                                <i class="bi bi-star-fill mb-3" style="font-size: 2rem;"></i>
                                <h5 class="card-title">Score Atual</h5>
                                <p class="stats-number">{{ $stats['score_atual'] }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card stats-card text-white" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                            <div class="card-body text-center">
                                <i class="bi bi-graph-up mb-3" style="font-size: 2rem;"></i>
                                <h5 class="card-title">Score 3 Meses</h5>
                                <p class="stats-number">{{ $stats['score_3_meses'] }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card stats-card text-white" style="background: linear-gradient(135deg, #fa709a 0%, #fee140 100%);">
                            <div class="card-body text-center">
                                <i class="bi bi-calendar-event mb-3" style="font-size: 2rem;"></i>
                                <h5 class="card-title">Próximos Plantões</h5>
                                <p class="stats-number">{{ $stats['proximos_plantoes'] }}</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3 mb-3">
                        <div class="card stats-card text-white" style="background: linear-gradient(135deg, #ffecd2 0%, #fcb69f 100%); color: #333 !important;">
                            <div class="card-body text-center">
                                <i class="bi bi-shop mb-3" style="font-size: 2rem;"></i>
                                <h5 class="card-title">Ofertas Disponíveis</h5>
                                <p class="stats-number">{{ $stats['ofertas_disponiveis'] }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Access -->
                <div class="row mb-4">
                    <div class="col-12">
                        <h5 class="mb-3"><i class="bi bi-lightning-charge me-2"></i>Acesso Rápido</h5>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="card quick-access-card" onclick="window.location='{{ route('alocacoes.index') }}'">
                            <div class="card-body text-center py-4">
                                <i class="bi bi-calendar-check text-primary mb-3" style="font-size: 3rem;"></i>
                                <h6 class="card-title">Ver Escalas</h6>
                                <p class="card-text text-muted">Escalas gerais</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="card quick-access-card" onclick="window.location='#'">
                            <div class="card-body text-center py-4">
                                <i class="bi bi-arrow-left-right text-warning mb-3" style="font-size: 3rem;"></i>
                                <h6 class="card-title">Marketplace</h6>
                                <p class="card-text text-muted">Trocar plantões</p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="card quick-access-card" onclick="window.location='#'">
                            <div class="card-body text-center py-4">
                                <i class="bi bi-bar-chart text-info mb-3" style="font-size: 3rem;"></i>
                                <h6 class="card-title">Relatórios</h6>
                                <p class="card-text text-muted">Estatísticas</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Bottom Section -->
                <div class="row">
                    <!-- Meus Próximos Plantões -->
                    <div class="col-md-6 mb-4">
                        <div class="card content-card">
                            <div class="card-header bg-transparent border-0 pb-0">
                                <h6 class="mb-0"><i class="bi bi-calendar-event me-2"></i>Meus Próximos Plantões</h6>
                            </div>
                            <div class="card-body">
                                @if($meusProximosPlantoes->count() > 0)
                                @foreach($meusProximosPlantoes as $plantao)
                                <div class="d-flex align-items-center p-3 mb-2 bg-light rounded">
                                    <div class="me-3">
                                        <i class="bi bi-calendar-event text-primary" style="font-size: 1.5rem;"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">{{ $plantao->vaga->setor->nome ?? 'N/A' }}</h6>
                                        <p class="mb-0 text-muted small">
                                            {{ date('d/m/Y', strtotime($plantao->data_plantao)) }} -
                                            {{ $plantao->vaga->turno->nome ?? 'N/A' }}
                                        </p>
                                    </div>
                                    <span class="badge bg-{{ $plantao->status === 'confirmada' ? 'success' : 'warning' }}">
                                        {{ ucfirst($plantao->status) }}
                                    </span>
                                </div>
                                @endforeach
                                @else
                                <div class="empty-state">
                                    <i class="bi bi-calendar-x"></i>
                                    <h6>Nenhum plantão agendado para os próximos 30 dias</h6>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Marketplace - Ofertas Disponíveis -->
                    <div class="col-md-6 mb-4">
                        <div class="card content-card">
                            <div class="card-header bg-transparent border-0 pb-0">
                                <h6 class="mb-0"><i class="bi bi-shop me-2"></i>Marketplace - Ofertas Disponíveis</h6>
                            </div>
                            <div class="card-body">
                                @if($ofertasDisponiveis->count() > 0)
                                @foreach($ofertasDisponiveis as $oferta)
                                <div class="d-flex align-items-center p-3 mb-2 bg-light rounded">
                                    <div class="me-3">
                                        <i class="bi bi-shop text-warning" style="font-size: 1.5rem;"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-1">{{ $oferta->setor->nome ?? 'N/A' }}</h6>
                                        <p class="mb-0 text-muted small">
                                            {{ $oferta->unidade->nome ?? 'N/A' }} -
                                            {{ $oferta->turno->nome ?? 'N/A' }}
                                        </p>
                                    </div>
                                    <button class="btn btn-sm btn-outline-primary">
                                        <i class="bi bi-plus-circle me-1"></i>Candidatar
                                    </button>
                                </div>
                                @endforeach
                                @else
                                <div class="empty-state">
                                    <i class="bi bi-shop"></i>
                                    <h6>Nenhuma oferta disponível no momento</h6>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>