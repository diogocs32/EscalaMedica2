<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Turnos - {{ $unidade->nome }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container mt-4">
        <div class="row">
            <div class="col-md-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h1 class="h3">🕐 Editar Turnos</h1>
                        <p class="text-muted mb-0">{{ $unidade->nome }}</p>
                    </div>
                    <a href="{{ route('unidades.index') }}" class="btn btn-secondary">🔙 Voltar</a>
                </div>

                @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                @endif

                @if($errors->any())
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                    @endforeach
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
                @endif

                <div class="card mb-4">
                    <div class="card-header">
                        <h5 class="mb-0">Selecionar Turnos da Unidade</h5>
                    </div>
                    <div class="card-body">
                        <div class="alert alert-info">
                            <strong>ℹ️ Informação:</strong> Marque os turnos que deseja ativar para esta unidade. Após salvar, você poderá configurar a <strong>Escala Padrão</strong> (setores, turnos e quantidades por dia).
                        </div>

                        <form action="{{ route('unidades.updateTurnos', $unidade) }}" method="POST">
                            @csrf
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead class="table-light">
                                        <tr>
                                            <th width="50">
                                                <input type="checkbox" id="selectAll" class="form-check-input" title="Selecionar todos">
                                            </th>
                                            <th>Turno</th>
                                            <th>Horário</th>
                                            <th>Duração</th>
                                            <th>Período</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($turnos as $turno)
                                        <tr class="{{ in_array($turno->id, $turnosAssociados) ? 'table-success' : '' }}">
                                            <td>
                                                <input type="checkbox"
                                                    name="turnos[]"
                                                    value="{{ $turno->id }}"
                                                    class="form-check-input turno-checkbox"
                                                    {{ in_array($turno->id, $turnosAssociados) ? 'checked' : '' }}>
                                            </td>
                                            <td>
                                                <strong>{{ $turno->nome }}</strong>
                                                @if(in_array($turno->id, $turnosAssociados))
                                                <span class="badge bg-success ms-2">✓ Em uso</span>
                                                @endif
                                            </td>
                                            <td>
                                                <span class="badge bg-info">
                                                    {{ \Carbon\Carbon::parse($turno->hora_inicio)->format('H:i') }} -
                                                    {{ \Carbon\Carbon::parse($turno->hora_fim)->format('H:i') }}
                                                </span>
                                            </td>
                                            <td>{{ $turno->duracao_horas }}h</td>
                                            <td><span class="badge bg-secondary">{{ ucfirst($turno->periodo) }}</span></td>
                                            <td>
                                                <span class="badge bg-{{ $turno->status === 'ativo' ? 'success' : 'secondary' }}">
                                                    {{ ucfirst($turno->status) }}
                                                </span>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>

                            <div class="d-flex justify-content-between align-items-center mt-4">
                                <a href="{{ route('turnos.index') }}" class="btn btn-outline-secondary" target="_blank">
                                    ➕ Gerenciar Turnos Globais
                                </a>
                                <div>
                                    <button type="submit" class="btn btn-primary btn-lg">
                                        💾 Salvar Turnos
                                    </button>
                                    <a href="{{ route('escalas-padrao.index', $unidade) }}" class="btn btn-success btn-lg ms-2">
                                        ➡️ Configurar Escala Padrão
                                    </a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <script>
                    // Selecionar/Desmarcar todos
                    document.getElementById('selectAll').addEventListener('change', function() {
                        const checkboxes = document.querySelectorAll('.turno-checkbox');
                        checkboxes.forEach(cb => cb.checked = this.checked);
                    });

                    // Atualizar "Selecionar todos" baseado nos checkboxes individuais
                    document.querySelectorAll('.turno-checkbox').forEach(checkbox => {
                        checkbox.addEventListener('change', function() {
                            const allCheckboxes = document.querySelectorAll('.turno-checkbox');
                            const allChecked = Array.from(allCheckboxes).every(cb => cb.checked);
                            document.getElementById('selectAll').checked = allChecked;
                        });
                    });
                </script>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>