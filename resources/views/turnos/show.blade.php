<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalhes do Turno - Sistema de Escala Médica</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-8 mx-auto">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="h3">🕐 {{ $turno->nome }}</h1>
                    <div>
                        <a href="{{ route('turnos.edit', $turno) }}" class="btn btn-warning">✏️ Editar</a>
                        <a href="{{ route('turnos.index') }}" class="btn btn-secondary">🔙 Voltar</a>
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
                        <h5 class="mb-0">Informações do Turno</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="text-muted">Nome do Turno</h6>
                                <p class="fw-bold fs-5">{{ $turno->nome }}</p>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-muted">Status</h6>
                                <span class="badge bg-{{ $turno->status === 'ativo' ? 'success' : 'secondary' }} fs-6">
                                    {{ ucfirst($turno->status) }}
                                </span>
                            </div>
                        </div>

                        <hr>

                        <div class="row">
                            <div class="col-md-3">
                                <h6 class="text-muted">Horário de Início</h6>
                                <p class="fw-bold">{{ date('H:i', strtotime($turno->hora_inicio)) }}</p>
                            </div>
                            <div class="col-md-3">
                                <h6 class="text-muted">Horário de Fim</h6>
                                <p class="fw-bold">{{ date('H:i', strtotime($turno->hora_fim)) }}</p>
                            </div>
                            <div class="col-md-3">
                                <h6 class="text-muted">Duração</h6>
                                <p class="fw-bold">{{ $turno->duracao_horas }} horas</p>
                            </div>
                            <div class="col-md-3">
                                <h6 class="text-muted">Período</h6>
                                <span class="badge bg-{{ $turno->periodo === 'diurno' ? 'warning' : ($turno->periodo === 'noturno' ? 'dark' : 'info') }}">
                                    {{ ucfirst($turno->periodo) }}
                                </span>
                            </div>
                        </div>

                        <hr>

                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="text-muted">Vagas Disponíveis</h6>
                                <p class="fw-bold">{{ $turno->vagas->count() ?? 0 }}</p>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-muted">Vagas Ativas</h6>
                                <p class="fw-bold">{{ $turno->vagas->where('status', 'ativo')->count() ?? 0 }}</p>
                            </div>
                        </div>

                        <hr>

                        <div class="row">
                            <div class="col-md-6">
                                <small class="text-muted">
                                    <strong>Criado em:</strong> {{ $turno->created_at->format('d/m/Y H:i') }}
                                </small>
                            </div>
                            <div class="col-md-6 text-end">
                                <small class="text-muted">
                                    <strong>Última atualização:</strong> {{ $turno->updated_at->format('d/m/Y H:i') }}
                                </small>
                            </div>
                        </div>
                    </div>
                </div>

                @if($turno->vagas->count() > 0)
                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="mb-0">Vagas do Turno</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Unidade</th>
                                        <th>Setor</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($turno->vagas as $vaga)
                                    <tr>
                                        <td>{{ $vaga->id }}</td>
                                        <td>{{ $vaga->unidade->nome ?? 'N/A' }}</td>
                                        <td>{{ $vaga->setor->nome ?? 'N/A' }}</td>
                                        <td>
                                            <span class="badge bg-{{ $vaga->status === 'ativo' ? 'success' : 'secondary' }}">
                                                {{ ucfirst($vaga->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                @endif

                <div class="mt-3">
                    <a href="{{ route('turnos.index') }}" class="btn btn-secondary">🕐 Todos os Turnos</a>
                    <a href="{{ route('turnos.edit', $turno) }}" class="btn btn-warning">✏️ Editar</a>
                    <form action="{{ route('turnos.destroy', $turno) }}" method="POST" class="d-inline" onsubmit="return confirm('Tem certeza que deseja excluir este turno?')">
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