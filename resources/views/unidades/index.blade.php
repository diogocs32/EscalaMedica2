<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Unidades - Sistema de Escala Médica</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div class="d-flex align-items-center gap-2">
                        <h1 class="h3 mb-0">🏥 Gestão de Unidades</h1>
                        <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-house"></i></a>
                    </div>
                    <a href="{{ route('unidades.create') }}" class="btn btn-primary">➕ Nova Unidade</a>
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
                        <h5 class="mb-0">Lista de Unidades</h5>
                    </div>
                    <div class="card-body">
                        @if($unidades->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Nome</th>
                                        <th>Cidade</th>
                                        <th>Endereço</th>
                                        <th>Status</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($unidades as $unidade)
                                    <tr>
                                        <td>{{ $unidade->id }}</td>
                                        <td><strong>{{ $unidade->nome }}</strong></td>
                                        <td>{{ $unidade->cidade->nome ?? 'N/A' }}</td>
                                        <td>{{ Str::limit($unidade->endereco, 40) }}</td>
                                        <td>
                                            <span class="badge bg-{{ $unidade->status === 'ativo' ? 'success' : 'secondary' }}">
                                                {{ ucfirst($unidade->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('unidades.show', $unidade) }}" class="btn btn-sm btn-outline-info" title="Ver detalhes">👁️</a>
                                                <a href="{{ route('unidades.edit', $unidade) }}" class="btn btn-sm btn-outline-warning" title="Editar">✏️</a>
                                                <form action="{{ route('unidades.destroy', $unidade) }}" method="POST" class="d-inline" onsubmit="return confirm('Tem certeza que deseja excluir esta unidade?')">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Excluir">🗑️</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-3">
                            {{ $unidades->links() }}
                        </div>
                        @else
                        <div class="alert alert-info">
                            Nenhuma unidade cadastrada ainda. <a href="{{ route('unidades.create') }}">Cadastre a primeira!</a>
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