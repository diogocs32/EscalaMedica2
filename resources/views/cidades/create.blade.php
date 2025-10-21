<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nova Cidade - Sistema de Escala Médica</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-6 mx-auto">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">📝 Nova Cidade</h5>
                    </div>
                    <div class="card-body">
                        @if($errors->any())
                        <div class="alert alert-danger">
                            @foreach($errors->all() as $error)
                            <div>{{ $error }}</div>
                            @endforeach
                        </div>
                        @endif

                        <form action="{{ route('cidades.store') }}" method="POST">
                            @csrf

                            <div class="mb-3">
                                <label for="nome" class="form-label">Nome da Cidade *</label>
                                <input type="text" class="form-control" id="nome" name="nome" value="{{ old('nome') }}" placeholder="Ex: São Paulo, Rio de Janeiro" required>
                                <small class="form-text text-muted">Digite o nome completo da cidade</small>
                            </div>

                            <div class="mb-3">
                                <label for="estado" class="form-label">Estado (UF) *</label>
                                <select id="estado" name="estado" class="form-select" required>
                                    <option value="" disabled {{ old('estado') ? '' : 'selected' }}>Selecione a UF</option>
                                    @php
                                    $ufs = ['AC','AL','AP','AM','BA','CE','DF','ES','GO','MA','MT','MS','MG','PA','PB','PR','PE','PI','RJ','RN','RS','RO','RR','SC','SP','SE','TO'];
                                    @endphp
                                    @foreach($ufs as $uf)
                                    <option value="{{ $uf }}" {{ old('estado') === $uf ? 'selected' : '' }}>{{ $uf }}</option>
                                    @endforeach
                                </select>
                                <small class="form-text text-muted">Escolha a sigla do estado (UF)</small>
                            </div>

                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <a href="{{ route('cidades.index') }}" class="btn btn-secondary">Cancelar</a>
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