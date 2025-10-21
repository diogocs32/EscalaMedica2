<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalhes da Cidade - Sistema de Escala Médica</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-8 mx-auto">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="h3">🏙️ Detalhes da Cidade</h1>
                    <a href="{{ route('cidades.edit', $cidade) }}" class="btn btn-warning">✏️ Editar</a>
                </div>

                @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                @endif

                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Informações da Cidade</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>ID:</strong> {{ $cidade->id }}</p>
                                <p><strong>Nome:</strong> {{ $cidade->nome }}</p>
                                <p><strong>UF:</strong> {{ $cidade->estado }}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Total de Unidades:</strong> {{ $cidade->unidades->count() }}</p>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Criado em:</strong> {{ $cidade->created_at->format('d/m/Y H:i') }}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Atualizado em:</strong> {{ $cidade->updated_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Unidades na Cidade</h5>
                    </div>
                    <div class="card-body">
                        @if($cidade->unidades->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Nome</th>
                                        <th>Endereço</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($cidade->unidades as $unidade)
                                    <tr>
                                        <td><strong>{{ $unidade->nome }}</strong></td>
                                        <td>{{ $unidade->endereco }}</td>
                                        <td>
                                            <span class="badge bg-{{ $unidade->status === 'ativo' ? 'success' : 'secondary' }}">
                                                {{ ucfirst($unidade->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @else
                        <div class="alert alert-info mb-0">
                            Nenhuma unidade cadastrada para esta cidade.
                        </div>
                        @endif
                    </div>
                </div>

                <div class="mt-3">
                    <a href="{{ route('cidades.index') }}" class="btn btn-outline-secondary">⬅️ Voltar</a>
                    <form action="{{ route('cidades.destroy', $cidade) }}" method="POST" class="d-inline" onsubmit="return confirm('Tem certeza que deseja excluir esta cidade?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger">🗑️ Excluir</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>