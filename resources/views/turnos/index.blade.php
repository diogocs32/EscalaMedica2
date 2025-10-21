<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Turnos - Sistema de Escala Médica</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="h3">🕐 Gestão de Turnos</h1>
                    <a href="{{ route('turnos.create') }}" class="btn btn-primary">➕ Novo Turno</a>
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
                        <h5 class="mb-0">Lista de Turnos</h5>
                    </div>
                    <div class="card-body">
                        @if($turnos->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Nome</th>
                                        <th>Horário</th>
                                        <th>Duração</th>
                                        <th>Período</th>
                                        <th>Status</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($turnos as $turno)
                                    <tr>
                                        <td>{{ $turno->id }}</td>
                                        <td><strong>{{ $turno->nome }}</strong></td>
                                        <td>
                                            {{ date('H:i', strtotime($turno->hora_inicio)) }} -
                                            {{ date('H:i', strtotime($turno->hora_fim)) }}
                                        </td>
                                        <td>{{ $turno->duracao_horas }}h</td>
                                        <td>
                                            <span class="badge bg-{{ $turno->periodo === 'diurno' ? 'warning' : ($turno->periodo === 'noturno' ? 'dark' : 'info') }}">
                                                {{ ucfirst($turno->periodo) }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $turno->status === 'ativo' ? 'success' : 'secondary' }}">
                                                {{ ucfirst($turno->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('turnos.show', $turno) }}" class="btn btn-sm btn-outline-info">👁️</a>
                                                <a href="{{ route('turnos.edit', $turno) }}" class="btn btn-sm btn-outline-warning">✏️</a>
                                                <form action="{{ route('turnos.destroy', $turno) }}" method="POST" class="d-inline" onsubmit="return confirm('Tem certeza que deseja excluir este turno?')">
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

                        {{ $turnos->links() }}
                        @else
                        <div class="text-center py-4">
                            <p class="text-muted">Nenhum turno cadastrado.</p>
                            <a href="{{ route('turnos.create') }}" class="btn btn-primary">Cadastrar Primeiro Turno</a>
                        </div>
                        @endif
                    </div>
                </div>

                <div class="mt-3">
                    <a href="{{ url('/') }}" class="btn btn-secondary">🏠 Voltar ao Início</a>
                    <a href="{{ route('setores.index') }}" class="btn btn-outline-primary">📋 Setores</a>
                    <a href="{{ route('alocacoes.index') }}" class="btn btn-outline-success">📅 Alocações</a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>