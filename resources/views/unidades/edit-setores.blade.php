<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Setores - {{ $unidade->nome }}</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<div class="card">
    <div class="card-header bg-light">
        <h5 class="mb-0">⚠️ Tela substituída</h5>
    </div>
    <div class="card-body">
        <div class="alert alert-info">
            A gestão de setores/turnos/dias e quantidades foi substituída pela <strong>Escala Padrão</strong> (template de 5 semanas, sem datas).
        </div>
        <a href="{{ route('escalas-padrao.index', $unidade) }}" class="btn btn-primary btn-lg">
            � Abrir Escala Padrão da Unidade
        </a>
        <a href="{{ route('unidades.index') }}" class="btn btn-secondary btn-lg ms-2">🔙 Voltar</a>
    </div>
</div>
{{ ucfirst($vaga->status) }}
</span>
</td>
<td>
    <form action="{{ route('unidades.removeSetor', [$unidade, $vaga->setor_id, $vaga->turno_id]) }}"
        method="POST"
        class="d-inline"
        onsubmit="return confirm('Tem certeza que deseja remover esta configuração?')">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-sm btn-danger" title="Remover">🗑️</button>
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
        {{ $unidade->vagas->count() }} configuração(ões) ativa(s)
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