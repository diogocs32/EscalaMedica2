<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Criar Escala Padrão - {{ $unidade->nome }}</title>
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
            max-width: 800px;
            margin: 0 auto;
        }

        .card {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        h1 {
            font-size: 24px;
            color: #2c3e50;
            margin-bottom: 10px;
        }

        .subtitle {
            color: #7f8c8d;
            margin-bottom: 30px;
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

        input:focus,
        textarea:focus {
            outline: none;
            border-color: #3498db;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
        }

        textarea {
            resize: vertical;
            min-height: 100px;
        }

        .help-text {
            font-size: 12px;
            color: #7f8c8d;
            margin-top: 5px;
        }

        .actions {
            display: flex;
            gap: 10px;
            margin-top: 30px;
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

        .alert {
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .alert-danger {
            background: #f8d7da;
            border-left: 4px solid #dc3545;
            color: #721c24;
        }

        .info-box {
            background: #e8f4f8;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid #3498db;
        }

        .info-box h3 {
            font-size: 14px;
            color: #2c3e50;
            margin-bottom: 8px;
        }

        .info-box p {
            font-size: 13px;
            color: #34495e;
            margin: 5px 0;
        }

        .info-box ul {
            margin-left: 20px;
            margin-top: 10px;
        }

        .info-box li {
            font-size: 13px;
            color: #34495e;
            margin: 3px 0;
        }
    </style>
</head>

<body>
    <div class="container">
        <div class="card">
            <h1>📅 Criar Escala Padrão</h1>
            <p class="subtitle">Unidade: <strong>{{ $unidade->nome }}</strong></p>

            <!-- Info Box -->
            <div class="info-box">
                <h3>ℹ O que é a Escala Padrão?</h3>
                <p>A escala padrão define um modelo de 5 semanas que se repete automaticamente. É útil para:</p>
                <ul>
                    <li>Criar um padrão de trabalho consistente</li>
                    <li>Facilitar o planejamento de longo prazo</li>
                    <li>Reduzir o trabalho manual de criação de escalas</li>
                </ul>
                <p style="margin-top: 10px;"><strong>Após criar, você poderá configurar os turnos e setores para cada dia da semana.</strong></p>
            </div>

            @if($errors->any())
            <div class="alert alert-danger">
                <div>
                    <strong>Erro:</strong>
                    <ul style="margin-top: 5px; margin-left: 20px;">
                        @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
            @endif

            <form action="{{ route('escalas-padrao.store', $unidade->id) }}" method="POST">
                @csrf

                <div class="form-group">
                    <label for="nome">Nome da Escala *</label>
                    <input
                        type="text"
                        id="nome"
                        name="nome"
                        value="{{ old('nome', 'Escala Padrão ' . $unidade->nome) }}"
                        required
                        placeholder="Ex: Escala Padrão 2025">
                    <p class="help-text">Nome identificador desta escala</p>
                </div>

                <div class="form-group">
                    <label for="descricao">Descrição (opcional)</label>
                    <textarea
                        id="descricao"
                        name="descricao"
                        placeholder="Ex: Escala padrão com foco em atendimento emergencial 24h">{{ old('descricao') }}</textarea>
                    <p class="help-text">Informações adicionais sobre esta escala</p>
                </div>

                <div class="form-group">
                    <label for="vigencia_inicio">Data de Início da Vigência *</label>
                    <input
                        type="date"
                        id="vigencia_inicio"
                        name="vigencia_inicio"
                        value="{{ old('vigencia_inicio', date('Y-m-d')) }}"
                        required>
                    <p class="help-text">A partir desta data, o sistema começará a contar as semanas (Semana 1, 2, 3, 4, 5...)</p>
                </div>

                <div class="actions">
                    <button type="submit" class="btn btn-primary">✓ Criar Escala Padrão</button>
                    <a href="{{ route('unidades.index') }}" class="btn btn-secondary">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
</body>

</html>