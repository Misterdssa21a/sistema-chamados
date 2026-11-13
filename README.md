# Sistema de Chamados Técnicos (Help Desk)

Sistema web simples para os professores registrarem problemas nos equipamentos do laboratório de informática da escola. Feito em **HTML, CSS e PHP**, guardando os dados em um arquivo **CSV**.

Atividade prática da disciplina **Desenvolvimento de Aplicação para Internet** (Análise e Desenvolvimento de Sistemas, UNINASSAU Teresina), com o professor Msc. Juaceli Junior.

## Funcionalidades

- Formulário para abrir chamado (solicitante, equipamento, prioridade e descrição)
- Validação dos dados no servidor (campos obrigatórios e descrição com no mínimo 10 caracteres)
- Proteção contra XSS com `htmlspecialchars` ao exibir os dados
- Gravação dos chamados em `chamados.csv`, com código único (`uniqid`), data e hora
- Lista de chamados em tabela, com cor por prioridade (Alta, Média e Baixa)

### Desafios extras

- Filtro por prioridade na lista (`$_GET["prioridade"]`)
- Botão **Concluir**, que muda o status do chamado para "Resolvido"
- Contador de chamados por equipamento
- Versionamento com Git

## Estrutura de arquivos

```
chamados/
├── index.html    # formulário de abertura de chamado
├── salvar.php    # valida e grava o chamado no CSV
├── lista.php     # lista os chamados, com filtro e contador
├── concluir.php  # marca um chamado como resolvido
├── estilo.css    # aparência
└── .gitignore    # ignora o chamados.csv (dados de teste)
```

O arquivo `chamados.csv` é criado automaticamente quando o primeiro chamado é salvo.

## Como executar

1. Instale o [XAMPP](https://www.apachefriends.org/) e ligue o **Apache**.
2. Copie os arquivos do projeto para `C:\xampp\htdocs\chamados\`.
3. Abra no navegador: `http://localhost/chamados/index.html`

O sistema precisa rodar pelo servidor (`localhost`). Abrir o `index.html` com duplo clique não executa o PHP.

## Como funciona

```
formulário (index.html) → salvar.php → chamados.csv → lista.php
```

1. O formulário envia os dados por POST para o `salvar.php`.
2. O `salvar.php` limpa (`trim`), valida e grava uma linha no `chamados.csv`.
3. O `lista.php` lê o CSV linha a linha (`fgetcsv`) e mostra a tabela.
4. O botão **Concluir** envia o código do chamado por POST ao `concluir.php`, que atualiza o status no CSV.

## Roteiro de teste

- Enviar o formulário completo grava o chamado e mostra o código
- Enviar descrição curta (ex.: "ruim") mostra o erro de validação
- Digitar `<b>teste</b>` na descrição aparece como texto na lista, sem negrito
- A lista mostra os chamados com a cor da prioridade
- O filtro, o botão Concluir e o contador por equipamento funcionam

## Próximos passos

Trocar o armazenamento em CSV por um banco de dados MySQL.

## Autor

Victor Lucas Batista e Silva
