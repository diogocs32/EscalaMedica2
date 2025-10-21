<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalhes da Configuração - {{ $unidade->nome }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-10 mx-auto">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="h3">👁️ Detalhes da Configuração</h1>
                    <div>
                        <a href="{{ route('vagas.edit', [$unidade, $vaga]) }}" class="btn btn-warning">✏️ Editar</a>
                        <a href="{{ route('vagas.index', $unidade) }}" class="btn btn-secondary">🔙 Voltar</a>
                    </div>
                </div>

                @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                @endif

                <div class="card mb-3">
                    <div class="card-header">
                        <h5 class="mb-0">🏥 Unidade</h5>
                    </div>
                    <div class="card-body">
                        <h4>{{ $unidade->nome }}</h4>
                        <p class="text-muted mb-0">
                            📍 {{ $unidade->cidade->nome }}/{{ $unidade->cidade->uf }}
                        </p>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <div class="card mb-3">
                            <div class="card-header">
                                <h5 class="mb-0">🕐 Turno</h5>
                            </div>
                            <div class="card-body">
                                <h4>{{ $vaga->turno->nome }}</h4>
                                <p class="mb-2">
                                    <span class="badge bg-info">
                                        {{ \Carbon\Carbon::parse($vaga->turno->hora_inicio)->format('H:i') }} -
                                        {{ \Carbon\Carbon::parse($vaga->turno->hora_fim)->format('H:i') }}
                                    </span>
                                </p>
                                <p class="mb-1"><strong>Período:</strong> {{ $vaga->turno->periodo }}</p>
                                <p class="mb-0"><strong>Duração:</strong> {{ $vaga->turno->duracao_horas }} horas</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card mb-3">
                            <div class="card-header">
                                <h5 class="mb-0">🏥 Setor</h5>
                            </div>
                            <div class="card-body">
                                <h4>{{ $vaga->setor->nome }}</h4>
                                @if($vaga->setor->descricao)
                                <p class="text-muted mb-0">{{ $vaga->setor->descricao }}</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card mb-3">
                    <div class="card-header">
                        <h5 class="mb-0">📊 Configuração</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4">
                                <h6 class="text-muted">Médicos Necessários</h6>
                                <p class="fs-4 mb-0">
                                    <span class="badge bg-primary">
                                        {{ $vaga->quantidade_necessaria }}
                                        {{ $vaga->quantidade_necessaria == 1 ? 'médico' : 'médicos' }}
                                    </span>
                                </p>
                            </div>
                            <div class="col-md-4">
                                <h6 class="text-muted">Status</h6>
                                <p class="fs-5 mb-0">
                                    @if($vaga->status === 'ativo')
                                    <span class="badge bg-success">✓ Ativo</span>
                                    @else
                                    <span class="badge bg-secondary">✗ Inativo</span>
                                    @endif
                                </p>
                            </div>
                            <div class="col-md-4">
                                <h6 class="text-muted">Alocações</h6>
                                <p class="fs-5 mb-0">
                                    <span class="badge bg-info">
                                        {{ $vaga->alocacoes->count() }} alocação(ões)
                                    </span>
                                </p>
                            </div>
                        </div>

                        @if($vaga->observacoes)
                        <hr>
                        <h6 class="text-muted">Observações</h6>
                        <p class="mb-0">{{ $vaga->observacoes }}</p>
                        @endif
                    </div>
                </div>

                @if($vaga->alocacoes->isNotEmpty())
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">👨‍⚕️ Alocações Nesta Vaga</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Plantonista</th>
                                        <th>Data do Plantão</th>
                                        <th>Horário</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($vaga->alocacoes->sortByDesc('data_plantao') as $alocacao)
                                    <tr>
                                        <td>
                                            <strong>{{ $alocacao->plantonista->nome }}</strong>
                                            <br>
                                            <small class="text-muted">CRM: {{ $alocacao->plantonista->crm }}</small>
                                        </td>
                                        <td>{{ \Carbon\Carbon::parse($alocacao->data_plantao)->format('d/m/Y') }}</td>
                                        <td>
                                            <small>
                                                {{ \Carbon\Carbon::parse($alocacao->data_hora_inicio)->format('d/m/Y H:i') }}<br>
                                                {{ \Carbon\Carbon::parse($alocacao->data_hora_fim)->format('d/m/Y H:i') }}
                                            </small>
                                        </td>
                                        <td>
                                            @if($alocacao->status === 'confirmado')
                                            <span class="badge bg-success">✓ Confirmado</span>
                                            @elseif($alocacao->status === 'pendente')
                                            <span class="badge bg-warning">⏳ Pendente</span>
                                            @else
                                            <span class="badge bg-danger">✗ Cancelado</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>