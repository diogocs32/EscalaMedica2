<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Cidade - Sistema de Escala Médica</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-6 mx-auto">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">✏️ Editar Cidade</h5>
                    </div>
                    <div class="card-body">
                        @if($errors->any())
                        <div class="alert alert-danger">
                            @foreach($errors->all() as $error)
                            <div>{{ $error }}</div>
                            @endforeach
                        </div>
                        @endif

                        <form action="{{ route('cidades.update', $cidade) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <label for="nome" class="form-label">Nome da Cidade *</label>
                                <input type="text" class="form-control" id="nome" name="nome" value="{{ old('nome', $cidade->nome) }}" required>
                            </div>

                            <div class="mb-3">
                                <label for="estado" class="form-label">Estado (UF) *</nlabel>
                                    <select id="estado" name="estado" class="form-select" required>
                                        @php
                                        $ufs = ['AC','AL','AP','AM','BA','CE','DF','ES','GO','MA','MT','MS','MG','PA','PB','PR','PE','PI','RJ','RN','RS','RO','RR','SC','SP','SE','TO'];
                                        $selectedUf = old('estado', $cidade->estado ?? '');
                                        @endphp
                                        @foreach($ufs as $uf)
                                        <option value="{{ $uf }}" {{ $selectedUf === $uf ? 'selected' : '' }}>{{ $uf }}</option>
                                        @endforeach
                                    </select>
                            </div>

                            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                                <a href="{{ route('cidades.show', $cidade) }}" class="btn btn-secondary">Cancelar</a>
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