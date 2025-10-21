<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Turno - Sistema de Escala Médica</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-6 mx-auto">
                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">✏️ Editar Turno: {{ $turno->nome }}</h5>
                    </div>
                    <div class="card-body">
                        @if($errors->any())
                        <div class="alert alert-danger">
                            @foreach($errors->all() as $error)
                            <div>{{ $error }}</div>
                            @endforeach
                        </div>
                        @endif

                        <form action="{{ route('turnos.update', $turno) }}" method="POST">
                            @csrf
                            @method('PUT')

                            <div class="mb-3">
                                <label for="nome" class="form-label">Nome *</label>
                                <input type="text" class="form-control" id="nome" name="nome" value="{{ old('nome') ?? $turno->nome }}" placeholder="Ex: Manhã, Tarde, Noite, Corujão" required>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="hora_inicio" class="form-label">Hora Início *</label>
                                    <input type="time" class="form-control" id="hora_inicio" name="hora_inicio" value="{{ old('hora_inicio') ?? date('H:i', strtotime($turno->hora_inicio)) }}" required>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="hora_fim" class="form-label">Hora Fim *</label>
                                    <input type="time" class="form-control" id="hora_fim" name="hora_fim" value="{{ old('hora_fim') ?? date('H:i', strtotime($turno->hora_fim)) }}" required>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="periodo" class="form-label">Período</label>
                                    <select class="form-select" id="periodo" name="periodo">
                                        <option value="diurno" {{ (old('periodo') ?? $turno->periodo) === 'diurno' ? 'selected' : '' }}>Diurno</option>
                                        <option value="noturno" {{ (old('periodo') ?? $turno->periodo) === 'noturno' ? 'selected' : '' }}>Noturno</option>
                                        <option value="misto" {{ (old('periodo') ?? $turno->periodo) === 'misto' ? 'selected' : '' }}>Misto</option>
                                    </select>
                                </div>

                                <div class="col-md-6 mb-3">
                                    <label for="status" class="form-label">Status</label>
                                    <select class="form-select" id="status" name="status">
                                        <option value="ativo" {{ (old('status') ?? $turno->status) === 'ativo' ? 'selected' : '' }}>Ativo</option>
                                        <option value="inativo" {{ (old('status') ?? $turno->status) === 'inativo' ? 'selected' : '' }}>Inativo</option>
                                    </select>
                                </div>
                            </div>

                            <div class="alert alert-info">
                                <small><strong>ℹ️ Nota:</strong> A duração será recalculada automaticamente. Para turnos noturnos que atravessam a meia-noite, use horário fim menor que início.</small>
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="{{ route('turnos.show', $turno) }}" class="btn btn-secondary">🔙 Cancelar</a>
                                <button type="submit" class="btn btn-primary">💾 Atualizar Turno</button>
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