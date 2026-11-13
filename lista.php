<?php
header("Content-Type: text/html; charset=UTF-8");

// ------------------------------------------------------------
// 1. LER OS CHAMADOS DO CSV (fgetcsv lê uma linha por vez)
// ------------------------------------------------------------
$chamados = [];
if (file_exists("chamados.csv")) {
    $arquivo = fopen("chamados.csv", "r");
    while (($linha = fgetcsv($arquivo)) !== false) {
        if (count($linha) >= 7) {      // ignora linhas quebradas
            $chamados[] = $linha;
        }
    }
    fclose($arquivo);
}

// ------------------------------------------------------------
// 2. DESAFIO 3: CONTADOR POR EQUIPAMENTO
// Conta sobre TODOS os chamados (antes do filtro).
// Posição 3 de cada linha = equipamento.
// ------------------------------------------------------------
$porEquipamento = [];
foreach ($chamados as $c) {
    $porEquipamento[$c[3]] = ($porEquipamento[$c[3]] ?? 0) + 1;
}
arsort($porEquipamento);   // do maior para o menor

// ------------------------------------------------------------
// 3. DESAFIO 1: FILTRO POR PRIORIDADE ($_GET["prioridade"])
// Posição 4 de cada linha = prioridade.
// ------------------------------------------------------------
$permitidas = ["Baixa", "Média", "Alta"];
$filtro = $_GET["prioridade"] ?? "";
if (!in_array($filtro, $permitidas, true)) {
    $filtro = "";   // valor inválido na URL: ignora o filtro
}

$exibidos = $chamados;
if ($filtro !== "") {
    $exibidos = array_values(array_filter($chamados, fn($c) => $c[4] === $filtro));
}
$exibidos = array_reverse($exibidos);   // mais recentes primeiro

// Resumo: posição 6 de cada linha = status
$abertos = count(array_filter($chamados, fn($c) => $c[6] === "Aberto"));
$resolvidos = count($chamados) - $abertos;

// Atalho para escapar texto (evita XSS)
function e($texto): string {
    return htmlspecialchars((string)$texto, ENT_QUOTES, "UTF-8");
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Chamados</title>
    <link rel="stylesheet" href="estilo.css">
</head>
<body>
    <header class="topo">
        <div class="topo-interno">
            <span class="logo">🛠️ Help Desk do Laboratório</span>
            <nav>
                <a href="index.html">Abrir chamado</a>
                <a href="lista.php" class="ativo">Ver chamados</a>
            </nav>
        </div>
    </header>

    <main class="largo">
        <h1>Chamados Registrados (<?= count($chamados) ?>)</h1>

        <!-- Resumo -->
        <div class="resumo">
            <div class="cartao"><span class="numero"><?= count($chamados) ?></span><span>Total</span></div>
            <div class="cartao cartao-aberto"><span class="numero"><?= $abertos ?></span><span>Abertos</span></div>
            <div class="cartao cartao-resolvido"><span class="numero"><?= $resolvidos ?></span><span>Resolvidos</span></div>
        </div>

        <!-- DESAFIO 3: contador por equipamento -->
        <?php if (!empty($porEquipamento)): ?>
            <h2>Chamados por equipamento</h2>
            <ul class="etiquetas">
                <?php foreach ($porEquipamento as $equip => $qtd): ?>
                    <li><?= e($equip) ?>: <strong><?= $qtd ?></strong></li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>

        <!-- DESAFIO 1: filtro por prioridade (método GET) -->
        <form method="GET" action="lista.php" class="filtro">
            <label for="prioridade">Filtrar por prioridade</label>
            <select id="prioridade" name="prioridade" onchange="this.form.submit()">
                <option value="">Todas</option>
                <?php foreach ($permitidas as $p): ?>
                    <option <?= $filtro === $p ? "selected" : "" ?>><?= e($p) ?></option>
                <?php endforeach; ?>
            </select>
            <noscript><button type="submit">Filtrar</button></noscript>
            <?php if ($filtro !== ""): ?>
                <a href="lista.php">Limpar filtro</a>
            <?php endif; ?>
        </form>

        <!-- Tabela de chamados -->
        <?php if (empty($exibidos)): ?>
            <p class="vazio">Nenhum chamado encontrado.</p>
        <?php else: ?>
            <div class="tabela-rolagem">
            <table>
                <tr>
                    <th>Código</th><th>Data</th><th>Solicitante</th>
                    <th>Equipamento</th><th>Prioridade</th><th>Descrição</th>
                    <th>Status</th><th>Ação</th>
                </tr>
                <?php foreach ($exibidos as $c): ?>
                    <!-- a classe (ex.: prioridade-alta) define a cor da linha no CSS -->
                    <tr class="prioridade-<?= e(strtolower($c[4])) ?>">
                        <td><?= e($c[0]) ?></td>
                        <td><?= e($c[1]) ?></td>
                        <td><?= e($c[2]) ?></td>
                        <td><?= e($c[3]) ?></td>
                        <td><span class="selo selo-<?= e(strtolower($c[4])) ?>"><?= e($c[4]) ?></span></td>
                        <td><?= e($c[5]) ?></td>
                        <td><span class="status status-<?= e(strtolower($c[6])) ?>"><?= e($c[6]) ?></span></td>
                        <td>
                            <!-- DESAFIO 2: botão Concluir (envia o código por POST) -->
                            <?php if ($c[6] === "Aberto"): ?>
                                <form method="POST" action="concluir.php">
                                    <input type="hidden" name="codigo" value="<?= e($c[0]) ?>">
                                    <input type="hidden" name="volta" value="<?= e($filtro) ?>">
                                    <button type="submit" class="botao-pequeno">Concluir</button>
                                </form>
                            <?php else: ?>
                                —
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </table>
            </div>
        <?php endif; ?>
    </main>
</body>
</html>