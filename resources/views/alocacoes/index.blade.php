<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alocações - Sistema de Escala Médica</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="h3">📅 Gestão de Alocações</h1>
                    <a href="{{ route('alocacoes.create') }}" class="btn btn-primary">➕ Nova Alocação</a>
                </div>

                @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                @endif

                @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                    @endforeach
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                @endif

                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Lista de Alocações</h5>
                    </div>
                    <div class="card-body">
                        @if($alocacoes->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Plantonista</th>
                                        <th>Data Plantão</th>
                                        <th>Horário</th>
                                        <th>Unidade/Setor</th>
                                        <th>Turno</th>
                                        <th>Status</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($alocacoes as $alocacao)
                                    <tr>
                                        <td>{{ $alocacao->id }}</td>
                                        <td><strong>{{ $alocacao->plantonista->nome ?? 'N/A' }}</strong></td>
                                        <td>{{ date('d/m/Y', strtotime($alocacao->data_plantao)) }}</td>
                                        <td>
                                            @if($alocacao->data_hora_inicio && $alocacao->data_hora_fim)
                                            {{ date('H:i', strtotime($alocacao->data_hora_inicio)) }} -
                                            {{ date('H:i', strtotime($alocacao->data_hora_fim)) }}
                                            @else
                                            <span class="text-muted">Não calculado</span>
                                            @endif
                                        </td>
                                        <td>
                                            {{ $alocacao->vaga->unidade->nome ?? 'N/A' }} /
                                            {{ $alocacao->vaga->setor->nome ?? 'N/A' }}
                                        </td>
                                        <td>
                                            <span class="badge bg-info">
                                                {{ $alocacao->vaga->turno->nome ?? 'N/A' }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $alocacao->status === 'confirmada' ? 'success' : ($alocacao->status === 'pendente' ? 'warning' : 'secondary') }}">
                                                {{ ucfirst($alocacao->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('alocacoes.show', $alocacao) }}" class="btn btn-sm btn-outline-info">👁️</a>
                                                <a href="{{ route('alocacoes.edit', $alocacao) }}" class="btn btn-sm btn-outline-warning">✏️</a>
                                                <form action="{{ route('alocacoes.destroy', $alocacao) }}" method="POST" class="d-inline" onsubmit="return confirm('Tem certeza que deseja excluir esta alocação?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">🗑️</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        {{ $alocacoes->links() }}
                        @else
                        <div class="text-center py-4">
                            <p class="text-muted">Nenhuma alocação cadastrada.</p>
                            <a href="{{ route('alocacoes.create') }}" class="btn btn-primary">Cadastrar Primeira Alocação</a>
                        </div>
                        @endif
                    </div>
                </div>

                <div class="mt-3">
                    <a href="{{ url('/') }}" class="btn btn-secondary">🏠 Voltar ao Início</a>
                    <a href="{{ route('setores.index') }}" class="btn btn-outline-primary">📋 Setores</a>
                    <a href="{{ route('turnos.index') }}" class="btn btn-outline-info">🕐 Turnos</a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>