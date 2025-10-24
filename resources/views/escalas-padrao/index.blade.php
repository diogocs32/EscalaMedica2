<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Escala Padrão - {{ $unidade->nome }}</title>
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
            max-width: 1400px;
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

        .header p {
            color: #7f8c8d;
            margin: 5px 0;
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

        .alert-info {
            background: #d1ecf1;
            border-left: 4px solid #17a2b8;
            color: #0c5460;
        }

        .alert-warning {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            color: #856404;
        }

        .semana-section {
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
            overflow: hidden;
        }

        .semana-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px 25px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .semana-header h2 {
            font-size: 20px;
            font-weight: 600;
            margin: 0;
        }

        .semana-header .badge-atual {
            background: rgba(255, 255, 255, 0.3);
            color: white;
            padding: 5px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }

        .semana-content {
            padding: 20px;
        }

        .dias-grid {
            display: grid;
            grid-template-columns: repeat(7, 1fr);
            gap: 15px;
        }

        @media (max-width: 1200px) {
            .dias-grid {
                grid-template-columns: repeat(4, 1fr);
            }
        }

        @media (max-width: 768px) {
            .dias-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 480px) {
            .dias-grid {
                grid-template-columns: 1fr;
            }
        }

        .dia-card {
            background: white;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            overflow: hidden;
            transition: all 0.3s;
            min-height: 200px;
        }

        .dia-card:hover {
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            transform: translateY(-2px);
        }

        .dia-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 12px;
            text-align: center;
            font-weight: 600;
            font-size: 14px;
            text-transform: capitalize;
        }

        .dia-body {
            padding: 12px;
        }

        .config-item {
            background: #f8f9fa;
            padding: 10px;
            margin-bottom: 8px;
            border-radius: 6px;
            border-left: 3px solid #3498db;
            font-size: 13px;
        }

        .config-item:last-child {
            margin-bottom: 0;
        }

        .config-turno {
            font-weight: 600;
            color: #2c3e50;
            display: block;
            margin-bottom: 3px;
        }

        .config-setor {
            color: #7f8c8d;
            font-size: 12px;
        }

        .config-qty {
            background: #3498db;
            color: white;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 11px;
            font-weight: 600;
            float: right;
        }

        .empty-day {
            text-align: center;
            padding: 20px;
            color: #95a5a6;
            font-size: 13px;
        }

        .btn-add-config {
            width: 100%;
            padding: 10px;
            background: white;
            border: 2px dashed #bdc3c7;
            border-radius: 6px;
            color: #7f8c8d;
            font-size: 13px;
            cursor: pointer;
            transition: all 0.3s;
            text-decoration: none;
            display: block;
            text-align: center;
            margin-top: 10px;
        }

        .btn-add-config:hover {
            border-color: #3498db;
            color: #3498db;
            background: #f8f9fa;
        }

        .actions {
            display: flex;
            gap: 10px;
            margin-top: 15px;
        }

        .btn {
            padding: 10px 20px;
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

        .info-box {
            background: white;
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
            color: #7f8c8d;
            margin: 0;
        }
    </style>
</head>

<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 15px;">
                <h1 style="margin: 0;">📅 Escala Padrão de 5 Semanas</h1>
                <div style="display: flex; gap: 10px;">
                    <a href="{{ route('schedule-patterns') }}" class="btn btn-primary">📋 Ir para Escala Padrão</a>
                    <a href="{{ url('/unidades') }}" class="btn btn-secondary">← Voltar para Unidades</a>
                </div>
            </div>
            <p><strong>Unidade:</strong> {{ $unidade->nome }}</p>
            <p><strong>Cidade:</strong> {{ $unidade->cidade->nome ?? 'N/A' }}</p>
            <p><strong>Vigência:</strong> Desde {{ \Carbon\Carbon::parse($escala->vigencia_inicio)->format('d/m/Y') }}</p>
        </div>

        <!-- Alerts -->
        @if(session('success'))
        <div class="alert alert-success">
            ✓ {{ session('success') }}
        </div>
        @endif

        @if(session('info'))
        <div class="alert alert-info">
            ℹ {{ session('info') }}
        </div>
        @endif

        @if(session('warning'))
        <div class="alert alert-warning">
            ⚠ {{ session('warning') }}
        </div>
        @endif

        <!-- Info Box -->
        <div class="info-box">
            <h3>ℹ Como funciona?</h3>
            <p>Esta escala se repete automaticamente a cada 5 semanas. Semana atual: <strong>{{ $semanaAtual }}</strong>. Configure turnos e setores para cada dia da semana.</p>
        </div>

        <!-- Semanas (uma abaixo da outra) -->
        @for($s = 1; $s <= 5; $s++)
            @php
            $semana=$escala->semanas->firstWhere('numero_semana', $s);
            @endphp
            <div class="semana-section">
                <div class="semana-header">
                    <div>
                        <h2>Semana {{ $s }}</h2>
                        @if($semana && $semana->nome)
                        <small style="opacity: 0.9; font-size: 13px;">{{ $semana->nome }}</small>
                        @endif
                    </div>
                    @if($s === $semanaAtual)
                    <span class="badge-atual">SEMANA ATUAL</span>
                    @endif
                </div>

                <div class="semana-content">
                    @if($semana)
                    <div class="dias-grid">
                        @foreach($dias as $dia)
                        @php
                        $diaTemplate = $semana->dias->firstWhere('dia_semana', $dia);
                        $configs = $diaTemplate ? $diaTemplate->configuracoes->sortBy(function($config) {
                        return $config->turno->hora_inicio;
                        }) : collect();
                        @endphp
                        <div class="dia-card">
                            <div class="dia-header">
                                {{ ucfirst($dia) }}
                            </div>
                            <div class="dia-body">
                                @if($configs->isEmpty())
                                <div class="empty-day">
                                    Sem configurações
                                </div>
                                @else
                                @foreach($configs as $config)
                                <div class="config-item">
                                    <span class="config-qty">{{ $config->quantidade_necessaria }}</span>
                                    <span class="config-turno">{{ $config->turno->nome }}</span>
                                    <span class="config-setor">{{ $config->setor->nome }}</span>
                                </div>
                                @endforeach
                                @endif
                                <a href="{{ route('escalas-padrao.edit-dia', [$unidade->id, $s, $dia]) }}" class="btn-add-config">
                                    + Configurar
                                </a>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <p style="text-align: center; padding: 40px; color: #95a5a6;">Semana não encontrada.</p>
                    @endif
                </div>
            </div>
            @endfor

            <!-- Actions -->
            <div class="actions">
                <a href="{{ url('/unidades') }}" class="btn btn-secondary">← Voltar para Unidades</a>
            </div>
    </div>
</body>

</html>