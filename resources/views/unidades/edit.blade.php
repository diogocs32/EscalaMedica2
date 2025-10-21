<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Unidade - Sistema de Escala Médica</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-6 mx-auto">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">✏️ Editar Unidade</h5>
                    </div>
                    <div class="card-body">
                        @if($errors->any())
                        <div class="alert alert-danger">
                            @foreach($errors->all() as $error)
                            <div>{{ $error }}</div>
                            @endforeach
                        </div>
                        @endif

                        <form action="{{ route('unidades.update', $unidade) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <label for="nome" class="form-label">Nome da Unidade *</label>
                                <input type="text" class="form-control" id="nome" name="nome" value="{{ old('nome', $unidade->nome) }}" required>
                            </div>

                            <div class="mb-3">
                                <label for="cidade_id" class="form-label">Cidade *</label>
                                <select class="form-select" id="cidade_id" name="cidade_id" required>
                                    <option value="">Selecione uma cidade</option>
                                    @foreach($cidades as $cidade)
                                    <option value="{{ $cidade->id }}" {{ old('cidade_id', $unidade->cidade_id) == $cidade->id ? 'selected' : '' }}>
                                        {{ $cidade->nome }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="endereco" class="form-label">Endereço *</label>
                                <textarea class="form-control" id="endereco" name="endereco" rows="2" required>{{ old('endereco', $unidade->endereco) }}</textarea>
                            </div>

                            <div class="mb-3">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" id="status" name="status">
                                    <option value="ativo" {{ old('status', $unidade->status) === 'ativo' ? 'selected' : '' }}>Ativo</option>
                                    <option value="inativo" {{ old('status', $unidade->status) === 'inativo' ? 'selected' : '' }}>Inativo</option>
                                </select>
                            </div>

                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <a href="{{ route('unidades.show', $unidade) }}" class="btn btn-secondary">Cancelar</a>
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