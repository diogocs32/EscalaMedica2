<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalhes da Alocação - Sistema de Escala Médica</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-8 mx-auto">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="h3">📅 Detalhes da Alocação #{{ $alocacao->id }}</h1>
                    <div>
                        <a href="{{ route('alocacoes.edit', $alocacao) }}" class="btn btn-warning">✏️ Editar</a>
                        <a href="{{ route('alocacoes.index') }}" class="btn btn-secondary">🔙 Voltar</a>
                    </div>
                </div>

                @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                @endif

                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Informações da Alocação</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="text-muted">Plantonista</h6>
                                <p class="fw-bold">{{ $alocacao->plantonista->nome ?? 'N/A' }}</p>
                                <small class="text-muted">{{ $alocacao->plantonista->especialidade ?? '' }}</small>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-muted">Status</h6>
                                <span class="badge bg-{{ $alocacao->status === 'confirmada' ? 'success' : ($alocacao->status === 'pendente' ? 'warning' : 'secondary') }} fs-6">
                                    {{ ucfirst($alocacao->status) }}
                                </span>
                            </div>
                        </div>

                        <hr>

                        <div class="row">
                            <div class="col-md-4">
                                <h6 class="text-muted">Data do Plantão</h6>
                                <p class="fw-bold">{{ date('d/m/Y', strtotime($alocacao->data_plantao)) }}</p>
                                <small class="text-muted">{{ date('l', strtotime($alocacao->data_plantao)) }}</small>
                            </div>
                            <div class="col-md-4">
                                <h6 class="text-muted">Horário de Início</h6>
                                <p class="fw-bold">
                                    @if($alocacao->data_hora_inicio)
                                    {{ date('d/m/Y H:i', strtotime($alocacao->data_hora_inicio)) }}
                                    @else
                                    <span class="text-muted">Não calculado</span>
                                    @endif
                                </p>
                            </div>
                            <div class="col-md-4">
                                <h6 class="text-muted">Horário de Fim</h6>
                                <p class="fw-bold">
                                    @if($alocacao->data_hora_fim)
                                    {{ date('d/m/Y H:i', strtotime($alocacao->data_hora_fim)) }}
                                    @else
                                    <span class="text-muted">Não calculado</span>
                                    @endif
                                </p>
                            </div>
                        </div>

                        <hr>

                        <div class="row">
                            <div class="col-md-3">
                                <h6 class="text-muted">Unidade</h6>
                                <p class="fw-bold">{{ $alocacao->vaga->unidade->nome ?? 'N/A' }}</p>
                            </div>
                            <div class="col-md-3">
                                <h6 class="text-muted">Setor</h6>
                                <p class="fw-bold">{{ $alocacao->vaga->setor->nome ?? 'N/A' }}</p>
                            </div>
                            <div class="col-md-3">
                                <h6 class="text-muted">Turno</h6>
                                <p class="fw-bold">{{ $alocacao->vaga->turno->nome ?? 'N/A' }}</p>
                                <small class="text-muted">
                                    @if($alocacao->vaga->turno)
                                    {{ date('H:i', strtotime($alocacao->vaga->turno->hora_inicio)) }} -
                                    {{ date('H:i', strtotime($alocacao->vaga->turno->hora_fim)) }}
                                    @endif
                                </small>
                            </div>
                            <div class="col-md-3">
                                <h6 class="text-muted">Duração</h6>
                                <p class="fw-bold">{{ $alocacao->vaga->turno->duracao_horas ?? 'N/A' }}h</p>
                            </div>
                        </div>

                        @if($alocacao->observacoes)
                        <hr>
                        <div class="row">
                            <div class="col-12">
                                <h6 class="text-muted">Observações</h6>
                                <p>{{ $alocacao->observacoes }}</p>
                            </div>
                        </div>
                        @endif

                        <hr>

                        <div class="row">
                            <div class="col-md-6">
                                <small class="text-muted">
                                    <strong>Criado em:</strong> {{ $alocacao->created_at->format('d/m/Y H:i') }}
                                </small>
                            </div>
                            <div class="col-md-6 text-end">
                                <small class="text-muted">
                                    <strong>Última atualização:</strong> {{ $alocacao->updated_at->format('d/m/Y H:i') }}
                                </small>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mt-3">
                    <a href="{{ route('alocacoes.index') }}" class="btn btn-secondary">📅 Todas as Alocações</a>
                    <a href="{{ route('alocacoes.edit', $alocacao) }}" class="btn btn-warning">✏️ Editar</a>
                    <form action="{{ route('alocacoes.destroy', $alocacao) }}" method="POST" class="d-inline" onsubmit="return confirm('Tem certeza que deseja excluir esta alocação?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger">🗑️ Excluir</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>