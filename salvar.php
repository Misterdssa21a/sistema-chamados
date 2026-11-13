<?php
header("Content-Type: text/html; charset=UTF-8");

// 1. Receber e limpar os dados
$solicitante = trim($_POST["solicitante"] ?? "");
$equipamento = trim($_POST["equipamento"] ?? "");
$prioridade  = trim($_POST["prioridade"] ?? "");
$descricao   = trim($_POST["descricao"] ?? "");

// 2. Validar (no servidor, pois o "required" do HTML pode ser burlado)
$erros = [];

if ($solicitante === "") {
    $erros[] = "Informe seu nome.";
}
if (!in_array($equipamento, ["Computador", "Projetor", "Internet", "Impressora"], true)) {
    $erros[] = "Escolha o equipamento.";
}
if (!in_array($prioridade, ["Baixa", "Média", "Alta"], true)) {
    $erros[] = "Escolha uma prioridade válida.";
}
if (mb_strlen($descricao, "UTF-8") < 10) {
    $erros[] = "Descreva o problema com pelo menos 10 caracteres.";
}

function cabecalho(string $titulo): void {
    echo "<!DOCTYPE html><html lang='pt-br'><head><meta charset='UTF-8'>";
    echo "<meta name='viewport' content='width=device-width, initial-scale=1'>";
    echo "<title>" . htmlspecialchars($titulo) . "</title>";
    echo "<link rel='stylesheet' href='estilo.css'></head><body>";
    echo "<header class='topo'><div class='topo-interno'>";
    echo "<span class='logo'>🛠️ Help Desk do Laboratório</span><nav>";
    echo "<a href='index.html'>Abrir chamado</a><a href='lista.php'>Ver chamados</a>";
    echo "</nav></div></header><main>";
}

if (!empty($erros)) {
    cabecalho("Erro");
    echo "<div class='aviso aviso-erro'><h2>Não foi possível enviar</h2><ul>";
    foreach ($erros as $erro) {
        echo "<li>" . htmlspecialchars($erro) . "</li>";
    }
    echo "</ul></div>";
    echo "<p><a class='botao' href='index.html'>Voltar e corrigir</a></p>";
    echo "</main></body></html>";
    exit;
}

// 3. Montar o registro (mesma ordem de colunas da versão 1)
$registro = [
    uniqid(),            // 0 código do chamado
    date("d/m/Y H:i"),   // 1 data e hora
    $solicitante,        // 2
    $equipamento,        // 3
    $prioridade,         // 4
    $descricao,          // 5
    "Aberto"             // 6 status inicial
];

// 4. Gravar no CSV
$arquivo = fopen("chamados.csv", "a");
flock($arquivo, LOCK_EX);
fputcsv($arquivo, $registro);
flock($arquivo, LOCK_UN);
fclose($arquivo);

// 5. Confirmar
cabecalho("Chamado registrado");
echo "<div class='aviso aviso-ok'><h2>Chamado registrado com sucesso!</h2>";
echo "<p>Código: <strong>" . htmlspecialchars($registro[0]) . "</strong></p></div>";
echo "<p><a class='botao' href='lista.php'>Ver chamados</a> ";
echo "<a class='botao botao-claro' href='index.html'>Abrir outro</a></p>";
echo "</main></body></html>";
?>