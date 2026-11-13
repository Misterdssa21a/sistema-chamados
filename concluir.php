<?php
// Muda o status de um chamado para "Resolvido" e volta para a lista.
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    header("Location: lista.php");
    exit;
}

$codigo = trim($_POST["codigo"] ?? "");
$volta  = trim($_POST["volta"] ?? "");

if ($codigo !== "" && file_exists("chamados.csv")) {
    $arquivo = fopen("chamados.csv", "c+");
    flock($arquivo, LOCK_EX);

    $linhas = [];
    while (($linha = fgetcsv($arquivo)) !== false) {
        if (count($linha) >= 7 && $linha[0] === $codigo) {
            $linha[6] = "Resolvido";
        }
        $linhas[] = $linha;
    }

    // Reescreve o arquivo inteiro com o status atualizado
    ftruncate($arquivo, 0);
    rewind($arquivo);
    foreach ($linhas as $linha) {
        fputcsv($arquivo, $linha);
    }

    flock($arquivo, LOCK_UN);
    fclose($arquivo);
}

$destino = "lista.php";
if (in_array($volta, ["Baixa", "Média", "Alta"], true)) {
    $destino .= "?prioridade=" . urlencode($volta);
}
header("Location: " . $destino);
exit;