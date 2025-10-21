# Funcionalidades Deprecadas

A partir desta versão, consolidamos a configuração de escalas no novo fluxo de Escala Padrão (template rotativo de 5 semanas, sem datas), por unidade.

## Vagas (UI antiga)

- O antigo módulo de "Vagas" (configuração de Setor + Turno + Dia da semana + Quantidade) foi substituído pela Escala Padrão.
- Todas as rotas `vagas.*` permanecem ativas apenas como compatibilidade, mas agora redirecionam para `escalas-padrao.index` da unidade.
- Telas ligadas a Vagas foram removidas dos atalhos/links principais (ex.: página da Unidade).
- A página `Unidades > Editar Setores` foi substituída por uma mensagem que aponta para a Escala Padrão.
- A página `Unidades > Editar Turnos` agora direciona diretamente para a Escala Padrão após salvar.

## O que permanece

- O modelo/tabela `vagas` e suas relações seguem existentes para compatibilidade de dados e histórico.
- Outras áreas que apenas exibem contagens ou referências informativas a `vagas` continuam funcionais, mas não são o meio de configuração.

## Novo fluxo oficial

- Configuração completa via `Escala Padrão`: `GET /unidades/{unidade}/escala-padrao`.
- Estrutura: 5 semanas × 7 dias, por unidade, com configurações por dia (Turno + Setor + Quantidade).

## Observações

- Bookmarks/links antigos para `vagas.*` não quebram: serão redirecionados automaticamente com um aviso informativo.
- Recomenda-se atualizar documentação interna, atalhos e treinamentos para utilizar exclusivamente a Escala Padrão.
