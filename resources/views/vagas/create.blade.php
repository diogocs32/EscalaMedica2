<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nova Configuração de Vaga - {{ $unidade->nome }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h1 class="h3">➕ Nova Configuração de Vaga</h1>
                        <p class="text-muted mb-0">
                            <strong>{{ $unidade->nome }}</strong> - {{ $unidade->cidade->nome }}/{{ $unidade->cidade->uf }}
                        </p>
                    </div>
                    <a href="{{ route('vagas.index', $unidade) }}" class="btn btn-secondary">🔙 Voltar</a>
                </div>

                @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>❌ Erro ao criar configuração:</strong>
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
                        <small class="text-muted">Defina qual setor opera em qual turno e quantos médicos são necessários</small>
                    </div>
                    <div class="card-body">
                        <form action="{{ route('vagas.store', $unidade) }}" method="POST">
                            @csrf

                            <div class="mb-3">
                                <label for="turno_id" class="form-label">
                                    <strong>Turno</strong> <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('turno_id') is-invalid @enderror"
                                    id="turno_id"
                                    name="turno_id"
                                    required>
                                    <option value="">Selecione o turno...</option>
                                    @foreach($turnos as $turno)
                                    <option value="{{ $turno->id }}"
                                        {{ old('turno_id') == $turno->id ? 'selected' : '' }}
                                        data-setor-ids="{{ $turnos->map(fn($t) => $t->id == $turno->id ? 'turno_' . $turno->id : '')->filter()->implode(',') }}">
                                        {{ $turno->nome }}
                                        ({{ \Carbon\Carbon::parse($turno->hora_inicio)->format('H:i') }} -
                                        {{ \Carbon\Carbon::parse($turno->hora_fim)->format('H:i') }})
                                        - {{ $turno->duracao_horas }}h
                                    </option>
                                    @endforeach
                                </select>
                                @error('turno_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Horário de funcionamento</small>
                            </div>

                            <div class="mb-3">
                                <label for="setor_id" class="form-label">
                                    <strong>Setor</strong> <span class="text-danger">*</span>
                                </label>
                                <select class="form-select @error('setor_id') is-invalid @enderror"
                                    id="setor_id"
                                    name="setor_id"
                                    required>
                                    <option value="">Selecione o setor...</option>
                                    @foreach($setores as $setor)
                                    <option value="{{ $setor->id }}"
                                        {{ old('setor_id') == $setor->id ? 'selected' : '' }}>
                                        {{ $setor->nome }}
                                        @if($setor->descricao)
                                        - {{ Str::limit($setor->descricao, 50) }}
                                        @endif
                                    </option>
                                    @endforeach
                                </select>
                                @error('setor_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Área de atendimento</small>
                            </div>

                            <div class="mb-3">
                                <label for="quantidade_necessaria" class="form-label">
                                    <strong>Quantidade de Médicos Necessária</strong> <span class="text-danger">*</span>
                                </label>
                                <input type="number"
                                    class="form-control @error('quantidade_necessaria') is-invalid @enderror"
                                    id="quantidade_necessaria"
                                    name="quantidade_necessaria"
                                    value="{{ old('quantidade_necessaria', 1) }}"
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
                                    maxlength="500">{{ old('observacoes') }}</textarea>
                                @error('observacoes')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Informações adicionais sobre esta configuração (opcional)</small>
                            </div>

                            <div class="alert alert-info">
                                <strong>ℹ️ Exemplo:</strong><br>
                                <strong>Turno:</strong> Manhã (07:00 - 13:00)<br>
                                <strong>Setor:</strong> Teleconsulta<br>
                                <strong>Quantidade:</strong> 2 médicos<br>
                                <br>
                                Isso significa que na <strong>Telemedicina</strong>, no turno da <strong>Manhã</strong>,
                                o setor de <strong>Teleconsulta</strong> precisa de <strong>2 médicos</strong> alocados.
                            </div>

                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">
                                    ✅ Salvar Configuração
                                </button>
                                <a href="{{ route('vagas.index', $unidade) }}" class="btn btn-secondary">
                                    ❌ Cancelar
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

                @if(count($vagasExistentes) > 0)
                <div class="alert alert-warning mt-3">
                    <strong>⚠️ Atenção:</strong> Algumas combinações de Setor + Turno já estão configuradas e não podem ser duplicadas.
                </div>
                @endif
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Prevenir duplicatas no lado do cliente
        const vagasExistentes = @json($vagasExistentes);

        document.addEventListener('DOMContentLoaded', function() {
            const setorSelect = document.getElementById('setor_id');
            const turnoSelect = document.getElementById('turno_id');

            function verificarDuplicata() {
                const setorId = setorSelect.value;
                const turnoId = turnoSelect.value;

                if (setorId && turnoId) {
                    const chave = setorId + '_' + turnoId;
                    if (vagasExistentes.includes(chave)) {
                        alert('⚠️ Esta combinação de Setor + Turno já está configurada!');
                        setorSelect.value = '';
                        turnoSelect.value = '';
                    }
                }
            }

            setorSelect.addEventListener('change', verificarDuplicata);
            turnoSelect.addEventListener('change', verificarDuplicata);
        });
    </script>
</body>

</html>