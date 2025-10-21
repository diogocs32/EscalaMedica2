<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalhes do Plantonista - Sistema de Escala Médica</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-8 mx-auto">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1 class="h3">👨‍⚕️ Detalhes do Plantonista</h1>
                    <a href="{{ route('plantonistas.edit', $plantonista) }}" class="btn btn-warning">✏️ Editar</a>
                </div>

                @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                @endif

                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Informações do Plantonista</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>ID:</strong> {{ $plantonista->id }}</p>
                                <p><strong>Nome:</strong> {{ $plantonista->nome }}</p>
                                <p><strong>CRM:</strong> {{ $plantonista->crm }}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Email:</strong> {{ $plantonista->email }}</p>
                                <p><strong>Telefone:</strong> {{ $plantonista->telefone }}</p>
                                <p>
                                    <strong>Status:</strong>
                                    <span class="badge bg-{{ $plantonista->status === 'ativo' ? 'success' : 'secondary' }}">
                                        {{ ucfirst($plantonista->status) }}
                                    </span>
                                </p>
                            </div>
                        </div>
                        <hr>
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Criado em:</strong> {{ $plantonista->created_at->format('d/m/Y H:i') }}</p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Atualizado em:</strong> {{ $plantonista->updated_at->format('d/m/Y H:i') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">Alocações do Plantonista</h5>
                    </div>
                    <div class="card-body">
                        @if($plantonista->alocacoes->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-sm">
                                <thead>
                                    <tr>
                                        <th>Data</th>
                                        <th>Unidade</th>
                                        <th>Setor</th>
                                        <th>Turno</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($plantonista->alocacoes as $alocacao)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($alocacao->data_plantao)->format('d/m/Y') }}</td>
                                        <td>{{ $alocacao->vaga->unidade->nome ?? 'N/A' }}</td>
                                        <td>{{ $alocacao->vaga->setor->nome ?? 'N/A' }}</td>
                                        <td>{{ $alocacao->vaga->turno->nome ?? 'N/A' }}</td>
                                        <td>
                                            <span class="badge bg-{{ $alocacao->status === 'confirmado' ? 'success' : 'warning' }}">
                                                {{ ucfirst($alocacao->status) }}
                                            </span>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @else
                        <div class="alert alert-info mb-0">
                            Nenhuma alocação encontrada para este plantonista.
                        </div>
                        @endif
                    </div>
                </div>

                <div class="mt-3">
                    <a href="{{ route('plantonistas.index') }}" class="btn btn-outline-secondary">⬅️ Voltar</a>
                    <form action="{{ route('plantonistas.destroy', $plantonista) }}" method="POST" class="d-inline" onsubmit="return confirm('Tem certeza que deseja excluir este plantonista?')">
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