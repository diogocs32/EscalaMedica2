<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configurar {{ ucfirst($diaTemplate->dia_semana) }} - Semana {{ $semanaTemplate->numero_semana }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            background: #f5f7fa;
            color: #2c3e50;
            padding: 20px;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
        }

        .header {
            background: white;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .header h1 {
            font-size: 24px;
            color: #2c3e50;
            margin-bottom: 10px;
        }

        .breadcrumb {
            color: #7f8c8d;
            font-size: 13px;
            margin-bottom: 5px;
        }

        .breadcrumb a {
            color: #3498db;
            text-decoration: none;
        }

        .breadcrumb a:hover {
            text-decoration: underline;
        }

        .alert {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .alert-success {
            background: #d4edda;
            border-left: 4px solid #28a745;
            color: #155724;
        }

        .alert-danger {
            background: #f8d7da;
            border-left: 4px solid #dc3545;
            color: #721c24;
        }

        .grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        @media (max-width: 768px) {
            .grid {
                grid-template-columns: 1fr;
            }
        }

        .card {
            background: white;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .card h2 {
            font-size: 18px;
            color: #2c3e50;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #e9ecef;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            font-weight: 600;
            margin-bottom: 8px;
            color: #2c3e50;
            font-size: 14px;
        }

        select,
        input,
        textarea {
            width: 100%;
            padding: 12px;
            border: 1px solid #dfe6e9;
            border-radius: 6px;
            font-size: 14px;
            font-family: inherit;
            transition: all 0.3s;
        }

        select:focus,
        input:focus,
        textarea:focus {
            outline: none;
            border-color: #3498db;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
        }

        textarea {
            resize: vertical;
            min-height: 80px;
        }

        .config-list {
            max-height: 600px;
            overflow-y: auto;
        }

        .config-item {
            background: #f8f9fa;
            padding: 15px;
            margin-bottom: 12px;
            border-radius: 8px;
            border-left: 4px solid #3498db;
            position: relative;
        }

        .config-item:last-child {
            margin-bottom: 0;
        }

        .config-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 10px;
        }

        .config-title {
            flex: 1;
        }

        .config-turno {
            font-weight: 600;
            color: #2c3e50;
            font-size: 15px;
            display: block;
            margin-bottom: 3px;
        }

        .config-setor {
            color: #7f8c8d;
            font-size: 13px;
        }

        .config-qty {
            background: #3498db;
            color: white;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
            white-space: nowrap;
        }

        .config-obs {
            font-size: 12px;
            color: #7f8c8d;
            margin-top: 8px;
            font-style: italic;
        }

        .config-actions {
            display: flex;
            gap: 8px;
            margin-top: 10px;
        }

        .config-actions button {
            padding: 6px 12px;
            font-size: 12px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .btn-edit {
            background: #3498db;
            color: white;
        }

        .btn-edit:hover {
            background: #2980b9;
        }

        .btn-delete {
            background: #e74c3c;
            color: white;
        }

        .btn-delete:hover {
            background: #c0392b;
        }

        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #95a5a6;
        }

        .empty-state svg {
            width: 64px;
            height: 64px;
            margin-bottom: 15px;
            opacity: 0.3;
        }

        .btn {
            padding: 12px 24px;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            text-decoration: none;
            transition: all 0.3s;
            display: inline-block;
        }

        .btn-primary {
            background: #3498db;
            color: white;
        }

        .btn-primary:hover {
            background: #2980b9;
        }

        .btn-secondary {
            background: #95a5a6;
            color: white;
        }

        .btn-secondary:hover {
            background: #7f8c8d;
        }

        .btn-success {
            background: #27ae60;
            color: white;
        }

        .btn-success:hover {
            background: #229954;
        }

        .btn-block {
            width: 100%;
            text-align: center;
        }

        .actions {
            display: flex;
            gap: 10px;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 2px solid #e9ecef;
        }

        .help-text {
            font-size: 12px;
            color: #7f8c8d;
            margin-top: 5px;
        }

        /* Modal para copiar */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }

        .modal.active {
            display: flex;
        }

        .modal-content {
            background: white;
            padding: 30px;
            border-radius: 8px;
            max-width: 500px;
            width: 90%;
            max-height: 80vh;
            overflow-y: auto;
        }

        .modal-header {
            margin-bottom: 20px;
        }

        .modal-header h3 {
            font-size: 18px;
            color: #2c3e50;
        }

        .checkbox-group {
            margin-bottom: 15px;
        }

        .checkbox-label {
            display: flex;
            align-items: center;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 6px;
            margin-bottom: 8px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .checkbox-label:hover {
            background: #e9ecef;
        }

        .checkbox-label input {
            width: auto;
            margin-right: 10px;
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="breadcrumb">
                <a href="{{ route('escalas-padrao.index', $unidade->id) }}">← Voltar para Escala Padrão</a>
            </div>
            <h1>📝 Configurar {{ ucfirst($diaTemplate->dia_semana) }}</h1>
            <p style="color: #7f8c8d; margin-top: 5px;">
                Semana {{ $semanaTemplate->numero_semana }} • {{ $unidade->nome }}
            </p>
        </div>

        <!-- Alerts -->
        @if(session('success'))
        <div class="alert alert-success">
            ✓ {{ session('success') }}
        </div>
        @endif

        @if($errors->any())
        <div class="alert alert-danger">
            ❌ {{ $errors->first() }}
        </div>
        @endif

        <div class="grid">
            <!-- Formulário de Adição -->
            <div class="card">
                <h2>➕ Adicionar Configuração</h2>

                <form action="{{ route('escalas-padrao.store-configuracao', [$unidade->id, $semanaTemplate->numero_semana, $diaTemplate->dia_semana]) }}" method="POST">
                    @csrf

                    <div class="form-group">
                        <label for="turno_id">Turno *</label>
                        <select id="turno_id" name="turno_id" required>
                            <option value="">Selecione um turno</option>
                            @foreach($turnos as $turno)
                            <option value="{{ $turno->id }}" {{ old('turno_id') == $turno->id ? 'selected' : '' }}>
                                {{ $turno->nome }} ({{ $turno->hora_inicio }} - {{ $turno->hora_fim }})
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="setor_id">Setor *</label>
                        <select id="setor_id" name="setor_id" required>
                            <option value="">Selecione um setor</option>
                            @foreach($setores as $setor)
                            <option value="{{ $setor->id }}" {{ old('setor_id') == $setor->id ? 'selected' : '' }}>
                                {{ $setor->nome }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="quantidade_necessaria">Quantidade de Médicos *</label>
                        <input
                            type="number"
                            id="quantidade_necessaria"
                            name="quantidade_necessaria"
                            min="1"
                            max="50"
                            value="{{ old('quantidade_necessaria', 1) }}"
                            required>
                        <p class="help-text">Quantos médicos são necessários para esta combinação?</p>
                    </div>

                    <div class="form-group">
                        <label for="observacoes">Observações (opcional)</label>
                        <textarea
                            id="observacoes"
                            name="observacoes"
                            placeholder="Ex: Priorizar especialistas em emergência">{{ old('observacoes') }}</textarea>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block">
                        ✓ Adicionar Configuração
                    </button>
                </form>

                <!-- Botão para copiar configurações -->
                @if($diaTemplate->configuracoes->isNotEmpty())
                <button type="button" class="btn btn-success btn-block" onclick="openCopyModal()" style="margin-top: 15px;">
                    📋 Copiar para outros dias
                </button>
                @endif
            </div>

            <!-- Lista de Configurações -->
            <div class="card">
                <h2>📋 Configurações Atuais</h2>

                <div class="config-list">
                    @if($diaTemplate->configuracoes->isEmpty())
                    <div class="empty-state">
                        <svg fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a2 2 0 01-2 2H6a2 2 0 01-2-2V4zm3 1h6v4H7V5zm6 6H7v2h6v-2z" clip-rule="evenodd" />
                        </svg>
                        <p>Nenhuma configuração ainda.</p>
                        <p style="font-size: 12px; margin-top: 5px;">Adicione a primeira combinação de Turno + Setor</p>
                    </div>
                    @else
                    @foreach($diaTemplate->configuracoes as $config)
                    <div class="config-item">
                        <div class="config-header">
                            <div class="config-title">
                                <span class="config-turno">{{ $config->turno->nome }}</span>
                                <span class="config-setor">{{ $config->setor->nome }}</span>
                            </div>
                            <span class="config-qty">{{ $config->quantidade_necessaria }} médico(s)</span>
                        </div>

                        @if($config->observacoes)
                        <div class="config-obs">
                            💬 {{ $config->observacoes }}
                        </div>
                        @endif

                        <div class="config-actions">
                            <form action="{{ route('escalas-padrao.destroy-configuracao', [$unidade->id, $config->id]) }}" method="POST" style="display: inline;" onsubmit="return confirm('Confirma a exclusão desta configuração?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn-delete">🗑️ Remover</button>
                            </form>
                        </div>
                    </div>
                    @endforeach
                    @endif
                </div>
            </div>
        </div>

        <!-- Actions -->
        <div class="actions">
            <a href="{{ route('escalas-padrao.index', $unidade->id) }}" class="btn btn-secondary">← Voltar para Escala</a>
        </div>
    </div>

    <!-- Modal de Copiar -->
    <div class="modal" id="copyModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3>📋 Copiar Configurações</h3>
                <p style="color: #7f8c8d; font-size: 13px; margin-top: 5px;">
                    Copiar as {{ $diaTemplate->configuracoes->count() }} configuração(ões) deste dia para:
                </p>
            </div>

            <form action="{{ route('escalas-padrao.copiar-dia', [$unidade->id, $semanaTemplate->numero_semana, $diaTemplate->dia_semana]) }}" method="POST">
                @csrf

                <div class="form-group">
                    <label>Semana de Destino</label>
                    <select name="semana_destino" required style="margin-bottom: 15px;">
                        @for($s = 1; $s <= 5; $s++)
                            <option value="{{ $s }}" {{ $s == $semanaTemplate->numero_semana ? 'selected' : '' }}>
                            Semana {{ $s }}
                            </option>
                            @endfor
                    </select>
                </div>

                <div class="checkbox-group">
                    <label style="font-weight: 600; margin-bottom: 10px; display: block;">Dias de Destino:</label>
                    @foreach(['segunda', 'terca', 'quarta', 'quinta', 'sexta', 'sabado', 'domingo'] as $dia)
                    <label class="checkbox-label">
                        <input
                            type="checkbox"
                            name="dias_destino[]"
                            value="{{ $dia }}"
                            {{ $dia == $diaTemplate->dia_semana ? 'disabled' : '' }}>
                        {{ ucfirst($dia) }}
                        {{ $dia == $diaTemplate->dia_semana ? '(origem)' : '' }}
                    </label>
                    @endforeach
                </div>

                <div class="checkbox-group" style="margin-top: 15px;">
                    <label class="checkbox-label">
                        <input type="checkbox" name="sobrescrever" value="1">
                        Sobrescrever configurações existentes
                    </label>
                </div>

                <div style="display: flex; gap: 10px; margin-top: 20px;">
                    <button type="submit" class="btn btn-primary" style="flex: 1;">✓ Copiar</button>
                    <button type="button" class="btn btn-secondary" onclick="closeCopyModal()" style="flex: 1;">Cancelar</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openCopyModal() {
            document.getElementById('copyModal').classList.add('active');
        }

        function closeCopyModal() {
            document.getElementById('copyModal').classList.remove('active');
        }

        // Fechar modal ao clicar fora
        document.getElementById('copyModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeCopyModal();
            }
        });
    </script>
</body>

</html>