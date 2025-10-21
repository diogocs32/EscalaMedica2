<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Plantonistas - Sistema de Escala Médica</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="h3">👨‍⚕️ Gestão de Plantonistas</h1>
                    <a href="{{ route('plantonistas.create') }}" class="btn btn-primary">➕ Novo Plantonista</a>
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
                        <h5 class="mb-0">Lista de Plantonistas</h5>
                    </div>
                    <div class="card-body">
                        @if($plantonistas->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Nome</th>
                                        <th>CRM</th>
                                        <th>Email</th>
                                        <th>Telefone</th>
                                        <th>Status</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($plantonistas as $plantonista)
                                    <tr>
                                        <td>{{ $plantonista->id }}</td>
                                        <td><strong>{{ $plantonista->nome }}</strong></td>
                                        <td>{{ $plantonista->crm }}</td>
                                        <td>{{ $plantonista->email }}</td>
                                        <td>{{ $plantonista->telefone }}</td>
                                        <td>
                                            <span class="badge bg-{{ $plantonista->status === 'ativo' ? 'success' : 'secondary' }}">
                                                {{ ucfirst($plantonista->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('plantonistas.show', $plantonista) }}" class="btn btn-sm btn-outline-info">👁️</a>
                                                <a href="{{ route('plantonistas.edit', $plantonista) }}" class="btn btn-sm btn-outline-warning">✏️</a>
                                                <form action="{{ route('plantonistas.destroy', $plantonista) }}" method="POST" class="d-inline" onsubmit="return confirm('Tem certeza que deseja excluir este plantonista?')">
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

                        <div class="mt-3">
                            {{ $plantonistas->links() }}
                        </div>
                        @else
                        <div class="alert alert-info">
                            Nenhum plantonista cadastrado ainda. <a href="{{ route('plantonistas.create') }}">Cadastre o primeiro!</a>
                        </div>
                        @endif
                    </div>
                </div>

                <div class="mt-3">
                    <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary">⬅️ Voltar ao Dashboard</a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>