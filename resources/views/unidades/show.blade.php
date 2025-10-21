<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalhes da Unidade - Sistema de Escala Médica</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-8 mx-auto">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="h3">🏥 Detalhes da Unidade</h1>
                    <div>
                        <a href="{{ route('escalas-padrao.index', $unidade) }}" class="btn btn-primary">
                            📅 Configurar Escala Padrão
                        </a>
                        <a href="{{ route('unidades.edit', $unidade) }}" class="btn btn-warning">✏️ Editar</a>
                        <a href="{{ route('unidades.index') }}" class="btn btn-secondary">🔙 Voltar</a>
                    </div>
                </div>

                @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                @endif

                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Informações da Unidade</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>ID:</strong> {{ $unidade->id }}</p>
                                <p><strong>Nome:</strong> {{ $unidade->nome }}</p>
                                <p><strong>Cidade:</strong> {{ $unidade->cidade->nome ?? 'N/A' }}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Endereço:</strong> {{ $unidade->endereco }}</p>
                                <p>
                                    <strong>Status:</strong>
                                    <span class="badge bg-{{ $unidade->status === 'ativo' ? 'success' : 'secondary' }}">
                                        {{ ucfirst($unidade->status) }}
                                    </span>
                                </p>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Criado em:</strong> {{ $unidade->created_at->format('d/m/Y H:i') }}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Atualizado em:</strong> {{ $unidade->updated_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="mb-0">Escala Padrão</h5>
                        <a href="{{ route('escalas-padrao.index', $unidade) }}" class="btn btn-sm btn-outline-primary">Abrir Escala Padrão</a>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info mb-0">
                            A configuração diária de setores, turnos e quantidades agora é feita pela <strong>Escala Padrão</strong> (modelo de 5 semanas sem datas).
                        </div>
                    </div>
                </div>

                <div class="mt-3">
                    <a href="{{ route('unidades.index') }}" class="btn btn-outline-secondary">⬅️ Voltar</a>
                    <form action="{{ route('unidades.destroy', $unidade) }}" method="POST" class="d-inline" onsubmit="return confirm('Tem certeza que deseja excluir esta unidade?')">
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