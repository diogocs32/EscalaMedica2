<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Novo Plantonista - Sistema de Escala Médica</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-6 mx-auto">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">📝 Novo Plantonista</h5>
                    </div>
                    <div class="card-body">
                        @if($errors->any())
                        <div class="alert alert-danger">
                            @foreach($errors->all() as $error)
                            <div>{{ $error }}</div>
                            @endforeach
                        </div>
                        @endif

                        <form action="{{ route('plantonistas.store') }}" method="POST">
                            @csrf

                            <div class="mb-3">
                                <label for="nome" class="form-label">Nome Completo *</label>
                                <input type="text" class="form-control" id="nome" name="nome" value="{{ old('nome') }}" placeholder="Ex: Dr. João Silva" required>
                            </div>

                            <div class="mb-3">
                                <label for="crm" class="form-label">CRM *</label>
                                <input type="text" class="form-control" id="crm" name="crm" value="{{ old('crm') }}" placeholder="Ex: 12345-SP" required>
                                <small class="form-text text-muted">Formato: 12345-UF</small>
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Email *</label>
                                <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}" placeholder="Ex: joao@email.com" required>
                            </div>

                            <div class="mb-3">
                                <label for="telefone" class="form-label">Telefone *</label>
                                <input type="text" class="form-control" id="telefone" name="telefone" value="{{ old('telefone') }}" placeholder="Ex: (11) 99999-9999" required>
                            </div>

                            <div class="mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="ativo" {{ old('status') === 'ativo' ? 'selected' : '' }}>Ativo</option>
                                    <option value="inativo" {{ old('status') === 'inativo' ? 'selected' : '' }}>Inativo</option>
                                </select>
                            </div>

                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <a href="{{ route('plantonistas.index') }}" class="btn btn-secondary">Cancelar</a>
                                <button type="submit" class="btn btn-primary">💾 Salvar</button>
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