<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Plantonista - Sistema de Escala Médica</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-6 mx-auto">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">✏️ Editar Plantonista</h5>
                    </div>
                    <div class="card-body">
                        @if($errors->any())
                        <div class="alert alert-danger">
                            @foreach($errors->all() as $error)
                            <div>{{ $error }}</div>
                            @endforeach
                        </div>
                        @endif

                        <form action="{{ route('plantonistas.update', $plantonista) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <label for="nome" class="form-label">Nome Completo *</label>
                                <input type="text" class="form-control" id="nome" name="nome" value="{{ old('nome', $plantonista->nome) }}" required>
                            </div>

                            <div class="mb-3">
                                <label for="crm" class="form-label">CRM *</label>
                                <input type="text" class="form-control" id="crm" name="crm" value="{{ old('crm', $plantonista->crm) }}" required>
                                <small class="form-text text-muted">Formato: 12345-UF</small>
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Email *</label>
                                <input type="email" class="form-control" id="email" name="email" value="{{ old('email', $plantonista->email) }}" required>
                            </div>

                            <div class="mb-3">
                                <label for="telefone" class="form-label">Telefone *</label>
                                <input type="text" class="form-control" id="telefone" name="telefone" value="{{ old('telefone', $plantonista->telefone) }}" required>
                            </div>

                            <div class="mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="ativo" {{ old('status', $plantonista->status) === 'ativo' ? 'selected' : '' }}>Ativo</option>
                                    <option value="inativo" {{ old('status', $plantonista->status) === 'inativo' ? 'selected' : '' }}>Inativo</option>
                                </select>
                            </div>

                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <a href="{{ route('plantonistas.show', $plantonista) }}" class="btn btn-secondary">Cancelar</a>
                                <button type="submit" class="btn btn-primary">💾 Atualizar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>