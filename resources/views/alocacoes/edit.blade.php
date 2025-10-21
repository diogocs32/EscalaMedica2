<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Alocação - Sistema de Escala Médica</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-8 mx-auto">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">✏️ Editar Alocação #{{ $alocacao->id }}</h5>
                    </div>
                    <div class="card-body">
                        @if($errors->any())
                        <div class="alert alert-danger">
                            @foreach($errors->all() as $error)
                            <div>{{ $error }}</div>
                            @endforeach
                        </div>
                        @endif

                        <form action="{{ route('alocacoes.update', $alocacao) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="plantonista_id" class="form-label">Plantonista *</label>
                                    <select class="form-select" id="plantonista_id" name="plantonista_id" required>
                                        <option value="">Selecione um plantonista</option>
                                        @foreach($plantonistas as $plantonista)
                                        <option value="{{ $plantonista->id }}" {{ (old('plantonista_id') ?? $alocacao->plantonista_id) == $plantonista->id ? 'selected' : '' }}>
                                            {{ $plantonista->nome }} - {{ $plantonista->especialidade }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="vaga_id" class="form-label">Vaga *</label>
                                    <select class="form-select" id="vaga_id" name="vaga_id" required>
                                        <option value="">Selecione uma vaga</option>
                                        @foreach($vagas as $vaga)
                                        <option value="{{ $vaga->id }}" {{ (old('vaga_id') ?? $alocacao->vaga_id) == $vaga->id ? 'selected' : '' }}>
                                            {{ $vaga->unidade->nome }} / {{ $vaga->setor->nome }} - {{ $vaga->turno->nome }}
                                        </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="data_plantao" class="form-label">Data do Plantão *</label>
                                    <input type="date" class="form-control" id="data_plantao" name="data_plantao" value="{{ old('data_plantao') ?? $alocacao->data_plantao }}" required>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="status" class="form-label">Status</label>
                                    <select class="form-select" id="status" name="status">
                                        <option value="pendente" {{ (old('status') ?? $alocacao->status) === 'pendente' ? 'selected' : '' }}>Pendente</option>
                                        <option value="confirmada" {{ (old('status') ?? $alocacao->status) === 'confirmada' ? 'selected' : '' }}>Confirmada</option>
                                        <option value="cancelada" {{ (old('status') ?? $alocacao->status) === 'cancelada' ? 'selected' : '' }}>Cancelada</option>
                                    </select>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="observacoes" class="form-label">Observações</label>
                                <textarea class="form-control" id="observacoes" name="observacoes" rows="3" placeholder="Observações sobre a alocação...">{{ old('observacoes') ?? $alocacao->observacoes }}</textarea>
                            </div>

                            <div class="alert alert-info">
                                <small><strong>ℹ️ Nota:</strong> Os horários de início e fim serão recalculados automaticamente se você alterar o turno ou data.</small>
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="{{ route('alocacoes.show', $alocacao) }}" class="btn btn-secondary">🔙 Cancelar</a>
                                <button type="submit" class="btn btn-primary">💾 Atualizar Alocação</button>
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