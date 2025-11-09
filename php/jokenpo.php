<?php
session_start();

// --- INICIALIZAÇÃO DAS VARIÁVEIS DE SESSÃO ---
if (!isset($_SESSION['pontosHumano'])) {
    $_SESSION['pontosHumano'] = 0;
    $_SESSION['pontosMaquina'] = 0;
    $_SESSION['empate'] = 0;
    $_SESSION['partidas'] = 0;
}

$erros = [];
$resultado = "";
$jogadaMaquina = "";

// --- PROCESSAMENTO DO FORMULÁRIO ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $opcao = $_POST['opcao'] ?? '';

    // SE A JOGADA FOR VÁLIDA
    if (in_array($opcao, ["Pedra", "Papel", "Tesoura"])) {

        // INCREMENTA CONTADOR DE PARTIDAS
        $_SESSION['partidas']++;

        // JOGADA DA MÁQUINA
        $opcoes = ["Pedra", "Papel", "Tesoura"];
        $jogadaMaquina = $opcoes[array_rand($opcoes)];

        // VERIFICA O VENCEDOR
        if ($opcao === $jogadaMaquina) {
            $_SESSION['empate']++;
            $resultado = "🤝 EMPATE!";
        } elseif (
            ($opcao === "Pedra" && $jogadaMaquina === "Tesoura") ||
            ($opcao === "Papel" && $jogadaMaquina === "Pedra") ||
            ($opcao === "Tesoura" && $jogadaMaquina === "Papel")
        ) {
            $_SESSION['pontosHumano']++;
            $resultado = "🏆 JOGADOR VENCEU!";
        } else {
            $_SESSION['pontosMaquina']++;
            $resultado = "💻 COMPUTADOR VENCEU!";
        }
    }

    // BOTÃO DE RESETAR JOGO
    if (isset($_POST['reset'])) {
        session_unset();
        session_destroy();
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>🪨📄✂️ PEDRA, PAPEL E TESOURA</title>
    <link rel="icon" type="image/x-icon" href="../img/icon_jokenpo.png">
    <link rel="stylesheet" href="../css/jokenpo.css">
</head>
<body>

<h1>🪨📄✂️ PEDRA, PAPEL E TESOURA</h1>
<div class="pai">
    <div class="filho1">
<form method="post" action="">
    <p>ESCOLHA SUA JOGADA:</p>
    <input type="radio" id="pedra" name="opcao" value="Pedra">
    <label for="pedra">PEDRA</label><br>

    <input type="radio" id="papel" name="opcao" value="Papel">
    <label for="papel">PAPEL</label><br>

    <input type="radio" id="tesoura" name="opcao" value="Tesoura">
    <label for="tesoura">TESOURA</label><br><br>

    <div class="button-group">
        <input type="submit" value="JOGAR">
        <button type="submit" name="reset">REINICIAR JOGO</button>
    </div>
</form>
</div>
<div class="filho2">
<?php
// EXIBE ERROS
if (!empty($erros)) {
    echo "<ul class='erro'>";
    foreach ($erros as $erro) {
        echo "<li>$erro</li>";
    }
    echo "</ul>";
}

// EXIBE RESULTADO E PLACAR
if (!empty($resultado)) {
    echo "<div class='resultado'>";
    echo "<p>VOCÊ JOGOU: <strong>$opcao</strong></p>";
    echo "<p>COMPUTADOR JOGOU: <strong>$jogadaMaquina</strong></p>";
    echo "<p>$resultado</p>";
    echo "</div>";
}

if (isset($_SESSION['partidas']) && $_SESSION['partidas'] > 0) {
    echo "
    <div class='placar'> 
    <h2>📊 PLACAR</h2>
    <p>RODADAS: {$_SESSION['partidas']}</p>
    <p>EMPATES: {$_SESSION['empate']}</p>
    <p>JOGADOR: {$_SESSION['pontosHumano']}</p>
    <p>COMPUTADOR: {$_SESSION['pontosMaquina']}</p>
    </div>
    ";
}
?>
</div>
</div>
</body>
</html>