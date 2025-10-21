<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalhes do Setor - Sistema de Escala Médica</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-8 mx-auto">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="h3">📋 {{ $setor->nome }}</h1>
                    <div>
                        <a href="{{ route('setores.edit', $setor) }}" class="btn btn-warning">✏️ Editar</a>
                        <a href="{{ route('setores.index') }}" class="btn btn-secondary">🔙 Voltar</a>
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
                        <h5 class="mb-0">Informações do Setor</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="text-muted">Nome do Setor</h6>
                                <p class="fw-bold fs-5">{{ $setor->nome }}</p>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-muted">Status</h6>
                                <span class="badge bg-{{ $setor->status === 'ativo' ? 'success' : 'secondary' }} fs-6">
                                    {{ ucfirst($setor->status) }}
                                </span>
                            </div>
                        </div>

                        <hr>

                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="text-muted">Unidade</h6>
                                <p class="fw-bold">{{ $setor->unidade->nome ?? 'N/A' }}</p>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-muted">Cidade</h6>
                                <p class="fw-bold">{{ $setor->unidade->cidade->nome ?? 'N/A' }} - {{ $setor->unidade->cidade->uf ?? '' }}</p>
                            </div>
                        </div>

                        @if($setor->descricao)
                        <hr>
                        <div class="row">
                            <div class="col-12">
                                <h6 class="text-muted">Descrição</h6>
                                <p>{{ $setor->descricao }}</p>
                            </div>
                        </div>
                        @endif

                        <hr>

                        <div class="row">
                            <div class="col-md-6">
                                <h6 class="text-muted">Vagas Disponíveis</h6>
                                <p class="fw-bold">{{ $setor->vagas->count() ?? 0 }}</p>
                            </div>
                            <div class="col-md-6">
                                <h6 class="text-muted">Vagas Ativas</h6>
                                <p class="fw-bold">{{ $setor->vagas->where('status', 'ativo')->count() ?? 0 }}</p>
                            </div>
                        </div>

                        <hr>

                        <div class="row">
                            <div class="col-md-6">
                                <small class="text-muted">
                                    <strong>Criado em:</strong> {{ $setor->created_at->format('d/m/Y H:i') }}
                                </small>
                            </div>
                            <div class="col-md-6 text-end">
                                <small class="text-muted">
                                    <strong>Última atualização:</strong> {{ $setor->updated_at->format('d/m/Y H:i') }}
                                </small>
                            </div>
                        </div>
                    </div>
                </div>

                @if($setor->vagas->count() > 0)
                <div class="card mt-4">
                    <div class="card-header">
                        <h5 class="mb-0">Vagas do Setor</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Turno</th>
                                        <th>Horário</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($setor->vagas as $vaga)
                                    <tr>
                                        <td>{{ $vaga->id }}</td>
                                        <td>{{ $vaga->turno->nome ?? 'N/A' }}</td>
                                        <td>
                                            @if($vaga->turno)
                                            {{ date('H:i', strtotime($vaga->turno->hora_inicio)) }} -
                                            {{ date('H:i', strtotime($vaga->turno->hora_fim)) }}
                                            @endif
                                        </td>
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
                    <a href="{{ route('setores.index') }}" class="btn btn-secondary">📋 Todos os Setores</a>
                    <a href="{{ route('setores.edit', $setor) }}" class="btn btn-warning">✏️ Editar</a>
                    <form action="{{ route('setores.destroy', $setor) }}" method="POST" class="d-inline" onsubmit="return confirm('Tem certeza que deseja excluir este setor?')">
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