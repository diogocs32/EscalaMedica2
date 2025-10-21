<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuração de Vagas - {{ $unidade->nome }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-4">
        <form method="GET" action="{{ route('vagas.index', $unidade) }}" class="mb-3">
            <div class="row align-items-center">
                <div class="col-auto">
                    <label for="dia_semana" class="form-label mb-0"><strong>Dia da Semana:</strong></label>
                </div>
                <div class="col-auto">
                    <select name="dia" id="dia_semana" class="form-select" onchange="this.form.submit()">
                        @foreach($dias as $dia)
                        <option value="{{ $dia }}" {{ $diaSelecionado == $dia ? 'selected' : '' }}>{{ ucfirst($dia) }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-outline-primary">Filtrar</button>
                </div>
            </div>
        </form>
        <form method="POST" action="{{ route('vagas.clone', $unidade) }}" class="mb-3 d-flex align-items-center gap-2">
            @csrf
            <input type="hidden" name="origem" value="{{ $diaSelecionado }}">
            <label for="destino" class="form-label mb-0"><strong>Clonar para:</strong></label>
            <select name="destino" id="destino" class="form-select w-auto">
                @foreach($dias as $dia)
                @if($dia != $diaSelecionado)
                <option value="{{ $dia }}">{{ ucfirst($dia) }}</option>
                @endif
                @endforeach
            </select>
            <button type="submit" class="btn btn-outline-success">Clonar Configurações</button>
        </form>
        <div class="row">
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h1 class="h3">⚙️ Configuração de Vagas</h1>
                        <p class="text-muted mb-0">
                            <strong>{{ $unidade->nome }}</strong> - {{ $unidade->cidade->nome }}/{{ $unidade->cidade->uf }}
                        </p>
                    </div>
                    <div>
                        <a href="{{ route('vagas.create', $unidade) }}" class="btn btn-primary">
                            ➕ Adicionar Configuração
                        </a>
                        <a href="{{ route('unidades.show', $unidade) }}" class="btn btn-secondary">
                            🔙 Voltar para Unidade
                        </a>
                    </div>
                </div>

                @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                @endif

                @if(session('error') || $errors->has('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') ?? $errors->first('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                @endif

                <div class="card">
                    <div class="card-header">
                        <h5 class="mb-0">📋 Configurações Atuais</h5>
                        <small class="text-muted">Define quais setores operam em quais turnos e quantos médicos são necessários</small>
                    </div>
                    <div class="card-body">
                        @if($vagasDia->isEmpty())
                        <div class="alert alert-info mb-0">
                            <strong>ℹ️ Nenhuma configuração definida para este dia</strong><br>
                            Clique em "Adicionar Configuração" para definir quais setores operam em quais turnos nesta unidade neste dia.
                        </div>
                        @else
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead class="table-light">
                                    <tr>
                                        <th>Turno</th>
                                        <th>Horário</th>
                                        <th>Setor</th>
                                        <th>Médicos Necessários</th>
                                        <th>Status</th>
                                        <th class="text-end">Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($vagasDia->sortBy(function($vaga) {
                                    return $vaga->turno->hora_inicio . '_' . $vaga->setor->nome;
                                    }) as $vaga)
                                    <tr>
                                        <td>
                                            <strong>{{ $vaga->turno->nome }}</strong>
                                            <br>
                                            <small class="text-muted">{{ $vaga->turno->periodo }}</small>
                                        </td>
                                        <td>
                                            <span class="badge bg-info">
                                                {{ \Carbon\Carbon::parse($vaga->turno->hora_inicio)->format('H:i') }} -
                                                {{ \Carbon\Carbon::parse($vaga->turno->hora_fim)->format('H:i') }}
                                            </span>
                                            <br>
                                            <small class="text-muted">{{ $vaga->turno->duracao_horas }}h</small>
                                        </td>
                                        <td>
                                            <strong>{{ $vaga->setor->nome }}</strong>
                                            @if($vaga->setor->descricao)
                                            <br>
                                            <small class="text-muted">{{ Str::limit($vaga->setor->descricao, 50) }}</small>
                                            @endif
                                        </td>
                                        <td>
                                            <span class="badge bg-primary fs-6">
                                                {{ $vaga->quantidade_necessaria }}
                                                {{ $vaga->quantidade_necessaria == 1 ? 'médico' : 'médicos' }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($vaga->status === 'ativo')
                                            <span class="badge bg-success">✓ Ativo</span>
                                            @else
                                            <span class="badge bg-secondary">✗ Inativo</span>
                                            @endif
                                        </td>
                                        <td class="text-end">
                                            <a href="{{ route('vagas.show', [$unidade, $vaga]) }}"
                                                class="btn btn-sm btn-info"
                                                title="Ver detalhes">
                                                👁️
                                            </a>
                                            <a href="{{ route('vagas.edit', [$unidade, $vaga]) }}"
                                                class="btn btn-sm btn-warning"
                                                title="Editar">
                                                ✏️
                                            </a>
                                            <form action="{{ route('vagas.destroy', [$unidade, $vaga]) }}"
                                                method="POST"
                                                class="d-inline"
                                                onsubmit="return confirm('Tem certeza que deseja excluir esta configuração?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit"
                                                    class="btn btn-sm btn-danger"
                                                    title="Excluir">
                                                    🗑️
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="mt-3">
                            <div class="alert alert-light">
                                <strong>📊 Resumo:</strong>
                                {{ $vagasDia->count() }} configuração(ões) definida(s) |
                                {{ $vagasDia->sum('quantidade_necessaria') }} médicos necessários neste dia
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>