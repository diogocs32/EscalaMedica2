<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Novo Setor - Sistema de Escala Médica</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-6 mx-auto">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">📝 Novo Setor</h5>
                    </div>
                    <div class="card-body">
                        @if($errors->any())
                        <div class="alert alert-danger">
                            @foreach($errors->all() as $error)
                            <div>{{ $error }}</div>
                            @endforeach
                        </div>
                        @endif

                        <form action="{{ route('setores.store') }}" method="POST">
                            @csrf

                            <div class="mb-3">
                                <label for="nome" class="form-label">Nome *</label>
                                <input type="text" class="form-control" id="nome" name="nome" value="{{ old('nome') }}" placeholder="Ex: UTI, Emergência, Cardiologia" required>
                            </div>

                            <div class="mb-3">
                                <label for="unidade_id" class="form-label">Unidade *</label>
                                <select class="form-select" id="unidade_id" name="unidade_id" required>
                                    <option value="">Selecione uma unidade</option>
                                    @foreach($unidades as $unidade)
                                    <option value="{{ $unidade->id }}" {{ old('unidade_id') == $unidade->id ? 'selected' : '' }}>
                                        {{ $unidade->nome }} - {{ $unidade->cidade->nome }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="descricao" class="form-label">Descrição</label>
                                <textarea class="form-control" id="descricao" name="descricao" rows="3" placeholder="Descrição do setor...">{{ old('descricao') }}</textarea>
                            </div>

                            <div class="mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="ativo" {{ old('status') === 'ativo' ? 'selected' : '' }}>Ativo</option>
                                    <option value="inativo" {{ old('status') === 'inativo' ? 'selected' : '' }}>Inativo</option>
                                </select>
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="{{ route('setores.index') }}" class="btn btn-secondary">🔙 Cancelar</a>
                                <button type="submit" class="btn btn-primary">💾 Salvar Setor</button>
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