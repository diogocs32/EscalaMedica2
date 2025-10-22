<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Setores - Sistema de Escala Médica</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div class="d-flex align-items-center gap-2">
                        <h1 class="h3 mb-0">🏢 Gestão de Setores</h1>
                        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-house"></i></a>
                    </div>
                    <a href="{{ route('setores.create') }}" class="btn btn-primary">➕ Novo Setor</a>
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
                        <h5 class="mb-0">Lista de Setores</h5>
                    </div>
                    <div class="card-body">
                        @if($setores->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Nome</th>
                                        <th>Descrição</th>
                                        <th>Vagas Ativas</th>
                                        <th>Status</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($setores as $setor)
                                    <tr>
                                        <td>{{ $setor->id }}</td>
                                        <td><strong>{{ $setor->nome }}</strong></td>
                                        <td>{{ Str::limit($setor->descricao, 50) }}</td>
                                        <td>{{ $setor->vagas->count() }}</td>
                                        <td>
                                            <span class="badge bg-{{ $setor->status === 'ativo' ? 'success' : 'secondary' }}">
                                                {{ ucfirst($setor->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('setores.show', $setor) }}" class="btn btn-sm btn-outline-info">👁️</a>
                                                <a href="{{ route('setores.edit', $setor) }}" class="btn btn-sm btn-outline-warning">✏️</a>
                                                <form action="{{ route('setores.destroy', $setor) }}" method="POST" class="d-inline" onsubmit="return confirm('Tem certeza que deseja excluir este setor?')">
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

                        {{ $setores->links() }}
                        @else
                        <div class="text-center py-4">
                            <p class="text-muted">Nenhum setor cadastrado.</p>
                            <a href="{{ route('setores.create') }}" class="btn btn-primary">Cadastrar Primeiro Setor</a>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>