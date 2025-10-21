<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Configuração - {{ $unidade->nome }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h1 class="h3">✏️ Editar Configuração de Vaga</h1>
                        <p class="text-muted mb-0">
                            <strong>{{ $unidade->nome }}</strong> - {{ $unidade->cidade->nome }}/{{ $unidade->cidade->uf }}
                        </p>
                    </div>
                    <a href="{{ route('vagas.show', [$unidade, $vaga]) }}" class="btn btn-secondary">🔙 Voltar</a>
                </div>

                @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>❌ Erro ao atualizar configuração:</strong>
                    <ul class="mb-0 mt-2">
                        @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                @endif

                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">📝 Informações da Configuração</h5>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('vagas.update', [$unidade, $vaga]) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="alert alert-info">
                                <strong>ℹ️ Configuração Atual:</strong><br>
                                <strong>Turno:</strong> {{ $vaga->turno->nome }}
                                ({{ \Carbon\Carbon::parse($vaga->turno->hora_inicio)->format('H:i') }} -
                                {{ \Carbon\Carbon::parse($vaga->turno->hora_fim)->format('H:i') }})<br>
                                <strong>Setor:</strong> {{ $vaga->setor->nome }}<br>
                                <br>
                                <small class="text-muted">⚠️ Turno e Setor não podem ser alterados. Para mudar, crie uma nova configuração.</small>
                            </div>

                            <div class="mb-3">
                                <label for="quantidade_necessaria" class="form-label">
                                    <strong>Quantidade de Médicos Necessária</strong> <span class="text-danger">*</span>
                                </label>
                                <input type="number"
                                    class="form-control @error('quantidade_necessaria') is-invalid @enderror"
                                    id="quantidade_necessaria"
                                    name="quantidade_necessaria"
                                    value="{{ old('quantidade_necessaria', $vaga->quantidade_necessaria) }}"
                                    min="1"
                                    max="50"
                                    required>
                                @error('quantidade_necessaria')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Quantos médicos são necessários para este setor/turno</small>
                            </div>

                            <div class="mb-3">
                                <label for="observacoes" class="form-label">
                                    <strong>Observações</strong>
                                </label>
                                <textarea class="form-control @error('observacoes') is-invalid @enderror"
                                    id="observacoes"
                                    name="observacoes"
                                    rows="3"
                                    maxlength="500">{{ old('observacoes', $vaga->observacoes) }}</textarea>
                                @error('observacoes')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Informações adicionais sobre esta configuração (opcional)</small>
                            </div>

                            <div class="mb-3">
                                <label for="status" class="form-label">
                                    <strong>Status</strong> <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('status') is-invalid @enderror"
                                    id="status"
                                    name="status"
                                    required>
                                    <option value="ativo" {{ old('status', $vaga->status) === 'ativo' ? 'selected' : '' }}>
                                        ✓ Ativo
                                    </option>
                                    <option value="inativo" {{ old('status', $vaga->status) === 'inativo' ? 'selected' : '' }}>
                                        ✗ Inativo
                                    </option>
                                </select>
                                @error('status')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Configurações inativas não permitem novas alocações</small>
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">
                                    ✅ Atualizar Configuração
                                </button>
                                <a href="{{ route('vagas.show', [$unidade, $vaga]) }}" class="btn btn-secondary">
                                    ❌ Cancelar
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

                @if($vaga->alocacoes->count() > 0)
                <div class="alert alert-warning mt-3">
                    <strong>⚠️ Atenção:</strong> Esta configuração possui {{ $vaga->alocacoes->count() }} alocação(ões) vinculada(s).
                    Alterar a quantidade ou desativar pode afetar escalas existentes.
                </div>
                @endif
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>