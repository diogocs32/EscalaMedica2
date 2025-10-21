<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Escalas Médicas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .hero-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            min-height: 100vh;
            display: flex;
            align-items: center;
        }

        .feature-card {
            border: none;
            border-radius: 16px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease;
            height: 100%;
        }

        .feature-card:hover {
            transform: translateY(-5px);
        }

        .feature-icon {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 2rem;
            margin: 0 auto 1rem;
        }

        .btn-custom {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 25px;
            padding: 12px 30px;
            color: white;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
            color: white;
        }
    </style>
</head>

<body>
    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h1 class="display-4 fw-bold mb-4">
                        <i class="bi bi-calendar-heart me-3"></i>
                        Sistema de Escalas Médicas
                    </h1>
                    <p class="lead mb-4">
                        Gerencie escalas médicas de forma inteligente e eficiente.
                        Automatize horários, previna conflitos e otimize a gestão de plantões.
                    </p>
                    <div class="d-flex gap-3">
                        <a href="{{ route('dashboard') }}" class="btn btn-custom btn-lg">
                            <i class="bi bi-speedometer2 me-2"></i>Acessar Dashboard
                        </a>
                        <a href="{{ route('alocacoes.index') }}" class="btn btn-outline-light btn-lg">
                            <i class="bi bi-calendar-check me-2"></i>Ver Escalas
                        </a>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="text-center">
                        <i class="bi bi-hospital" style="font-size: 15rem; opacity: 0.1;"></i>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-5 bg-light">
        <div class="container">
            <div class="row mb-5">
                <div class="col-12 text-center">
                    <h2 class="display-5 fw-bold mb-3">Funcionalidades Principais</h2>
                    <p class="text-muted">Sistema completo para gestão de escalas médicas</p>
                </div>
            </div>

            <div class="row g-4">
                <div class="col-md-4">
                    <div class="card feature-card">
                        <div class="card-body text-center p-4">
                            <div class="feature-icon">
                                <i class="bi bi-calendar-check"></i>
                            </div>
                            <h5 class="card-title">Gestão de Escalas</h5>
                            <p class="card-text text-muted">
                                Crie e gerencie escalas médicas com facilidade.
                                Sistema intuitivo para alocação de plantonistas.
                            </p>
                            <a href="{{ route('alocacoes.index') }}" class="btn btn-custom">
                                Gerenciar Escalas
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card feature-card">
                        <div class="card-body text-center p-4">
                            <div class="feature-icon">
                                <i class="bi bi-shield-check"></i>
                            </div>
                            <h5 class="card-title">Prevenção de Conflitos</h5>
                            <p class="card-text text-muted">
                                Validação automática para evitar sobreposição de horários
                                e garantir escalas consistentes.
                            </p>
                            <a href="{{ route('turnos.index') }}" class="btn btn-custom">
                                Ver Turnos
                            </a>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card feature-card">
                        <div class="card-body text-center p-4">
                            <div class="feature-icon">
                                <i class="bi bi-clock-history"></i>
                            </div>
                            <h5 class="card-title">Cálculo Automático</h5>
                            <p class="card-text text-muted">
                                Horários calculados automaticamente baseados nos turnos.
                                Suporte para plantões noturnos.
                            </p>
                            <a href="{{ route('setores.index') }}" class="btn btn-custom">
                                Ver Setores
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-5">
        <div class="container">
            <div class="row text-center">
                <div class="col-md-3">
                    <div class="mb-3">
                        <i class="bi bi-people-fill text-primary" style="font-size: 3rem;"></i>
                    </div>
                    <h3 class="fw-bold">5</h3>
                    <p class="text-muted">Plantonistas Cadastrados</p>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                        <i class="bi bi-building text-success" style="font-size: 3rem;"></i>
                    </div>
                    <h3 class="fw-bold">3</h3>
                    <p class="text-muted">Unidades Médicas</p>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                        <i class="bi bi-diagram-3 text-warning" style="font-size: 3rem;"></i>
                    </div>
                    <h3 class="fw-bold">7</h3>
                    <p class="text-muted">Setores Ativos</p>
                </div>
                <div class="col-md-3">
                    <div class="mb-3">
                        <i class="bi bi-calendar-event text-info" style="font-size: 3rem;"></i>
                    </div>
                    <h3 class="fw-bold">12</h3>
                    <p class="text-muted">Plantões Agendados</p>
                </div>
            </div>
        </div>
    </section>

    <section class="py-5 bg-primary text-white">
        <div class="container text-center">
            <h2 class="display-6 fw-bold mb-3">Pronto para começar?</h2>
            <p class="lead mb-4">Acesse o sistema e comece a gerenciar suas escalas médicas agora mesmo.</p>
            <a href="{{ route('dashboard') }}" class="btn btn-light btn-lg">
                <i class="bi bi-speedometer2 me-2"></i>Acessar Sistema
            </a>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>