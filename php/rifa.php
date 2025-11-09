<?php
// BLOCO DE PROCESSAMENTO (PHP)
$quantidade = $premio = $valor = 0;
$titulo = $data_sorteio = "";
$erros = [];

// VALORES FILTRADOS
$quantidade_filtrada = $valor_filtrado = 0;
$premio_filtrado = $data_sorteio_filtrada = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // OBTÉM E SANITIZA OS VALORES DO POST
    if (isset($_POST['quantidade']))
        $quantidade = trim($_POST['quantidade']);
    if (isset($_POST['premio']))
        $premio = trim($_POST['premio']);
    if (isset($_POST['valor']))
        $valor = trim($_POST['valor']);
    if (isset($_POST['titulo']))
        $titulo = trim($_POST['titulo']);
    if (isset($_POST['data_sorteio']))
        $data_sorteio = trim($_POST['data_sorteio']);

    // VALIDAÇÃO DOS CAMPOS
    if (empty($quantidade)) {
        $erros[] = "⚠️ POR FAVOR, PREENCHA O CAMPO DA QUANTIDADE.";
    } else {
        $quantidade_filtrada = filter_var($quantidade, FILTER_VALIDATE_INT);
        if ($quantidade_filtrada === false || $quantidade_filtrada <= 0) {
            $erros[] = "⚠️ A QUANTIDADE DEVE SER UM NÚMERO VÁLIDO E MAIOR QUE ZERO.";
        }
    }

    if (empty($premio)) {
        $erros[] = "⚠️ POR FAVOR, PREENCHA O CAMPO DO PRÊMIO.";
    } elseif (strlen($premio) < 3) {
        $erros[] = "⚠️ A DESCRIÇÃO DO PRÊMIO DEVE TER PELO MENOS 3 LETRAS.";
    } else {
        $premio_filtrado = htmlspecialchars($premio);
    }

    if (empty($valor)) {
        $erros[] = "⚠️ POR FAVOR, PREENCHA O CAMPO DO VALOR.";
    } else {
        $valor_filtrado = filter_var($valor, FILTER_VALIDATE_FLOAT);
        if ($valor_filtrado === false || $valor_filtrado <= 0) {
            $erros[] = "⚠️ O VALOR DEVE SER UM NÚMERO VÁLIDO E MAIOR QUE ZERO.";
        }
    }

    if (empty($titulo)) {
        $erros[] = "⚠️ POR FAVOR, PREENCHA O CAMPO DO TÍTULO.";
    } elseif (strlen($titulo) < 3) {
        $erros[] = "⚠️ O TÍTULO DEVE TER MAIS DE 3 LETRAS.";
    }

    if (empty($data_sorteio)) {
        $erros[] = "⚠️ POR FAVOR, SELECIONE A DATA DO SORTEIO.";
    } else {
        $data_sorteio_filtrada = htmlspecialchars($data_sorteio);
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>RIFA</title>
    <link rel="icon" type="image/x-icon" href="../img/icon_rifa.png">
    <link rel="stylesheet" href="../css/rifa.css">
</head>

<body>
    <div class="container">
        <h2>🎟️ CRIE SUA RIFA ONLINE</h2>
        <p>PREENCHA AS INFORMAÇÕES DA SUA RIFA!</p>

        <?php
        if (!empty($erros)) {
            echo "<div class='erro'><strong>⚠️ ERROS ENCONTRADOS:</strong><ul>";
            foreach ($erros as $erro)
                echo "<li>$erro</li>";
            echo "</ul></div>";
        }
        ?>

        <!-- FORMULÁRIO -->
        <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
            <div class="form-group">
                <label for="titulo">🏷️ TÍTULO DA RIFA</label>
                <input type="text" id="titulo" name="titulo"
                    value="<?= (!empty($titulo) && $titulo != '0') ? htmlspecialchars($titulo) : '' ?>"
                    placeholder="EX: RIFA DO CHURRASCO" required>
            </div>

            <div class="form-group">
                <label for="premio">🎁 PRÊMIO</label>
                <input type="text" id="premio" name="premio"
                    value="<?= (!empty($premio) && $premio != '0') ? htmlspecialchars($premio) : '' ?>"
                    placeholder="EX: 1 CAIXA DE CERVEJA" required>
            </div>

            <div class="form-group">
                <label for="valor">💰 VALOR DA RIFA</label>
                <input type="number" step="0.01" id="valor" name="valor"
                    value="<?= (!empty($valor) && $valor != '0') ? htmlspecialchars($valor) : '' ?>"
                    placeholder="EX: 5.00" required>
            </div>

            <div class="form-group">
                <label for="quantidade">🔢 QUANTIDADE DE RIFAS</label>
                <input type="number" id="quantidade" name="quantidade"
                    value="<?= (!empty($quantidade) && $quantidade != '0') ? htmlspecialchars($quantidade) : '' ?>"
                    placeholder="EX: 100" required>
            </div>

            <div class="form-group">
                <label for="data_sorteio">📅 DATA DO SORTEIO</label>
                <input type="date" id="data_sorteio" name="data_sorteio"
                    value="<?= (!empty($data_sorteio) && $data_sorteio != '0') ? htmlspecialchars($data_sorteio) : '' ?>"
                    required>
            </div>

            <div class="form-group">
                <input type="submit" value="🎟️ GERAR RIFAS">
            </div>
        </form>

        <?php
        // --- EXIBIÇÃO DAS RIFAS GERADAS ---
        if ($_SERVER["REQUEST_METHOD"] == "POST" && empty($erros)) {
            echo '<div class="container-botoes no-print">';
            echo '<button type="button" class="btn-imprimir" onclick="imprimirRifas()">🖨️ IMPRIMIR RIFAS</button>';
            echo '</div>';
            
            echo '<div class="rifas-container">';
            
            for ($i = 0; $i < $quantidade_filtrada; $i++) {
                $numero_formatado = str_pad($i + 1, 3, "0", STR_PAD_LEFT);

                echo '
            <div class="rifa">
                <!-- CANHOTO DO VENDEDOR -->
                <div class="primeira">
                    <h1>RIFA LEGAL</h1>
                    <h3>VALOR: R$ ' . htmlspecialchars(number_format($valor_filtrado, 2, ',', '.')) . '</h3>
                    <p><strong>Nº DO BILHETE:</strong> ' . $numero_formatado . '</p>
                    <p><strong>PRÊMIO:</strong> ' . htmlspecialchars($premio_filtrado) . '</p>
                    <p><strong>DATA DO SORTEIO:</strong> ' . date("d/m/Y", strtotime($data_sorteio_filtrada)) . '</p>
                    <p><strong>COMPRADOR:</strong> ___________________________</p>
                    <p><strong>TELEFONE:</strong> ____________________________</p>
                </div>

                <!-- CANHOTO DO CLIENTE -->
                <div class="segunda">
                    <h1>' . htmlspecialchars($titulo) . '</h1>
                    <h2>Nº ' . $numero_formatado . '</h2>
                    <h2>R$ ' . htmlspecialchars(number_format($valor_filtrado, 2, ',', '.')) . '</h2>
                    <p><strong>PRÊMIO:</strong> ' . htmlspecialchars($premio_filtrado) . '</p>
                    <p><strong>DATA DO SORTEIO:</strong> ' . date("d/m/Y", strtotime($data_sorteio_filtrada)) . '</p>
                </div>
            </div>';
            }
            echo '</div>'; 
        }
        ?>
    </div>
    <!-- GERADO POR IA! -->
    <script>
        // FUNÇÃO PARA IMPRIMIR APENAS AS RIFAS
        function imprimirRifas() {
            window.print();
        }

        // ATALHO CTRL+P PARA IMPRIMIR
        document.addEventListener('keydown', function(e) {
            if ((e.ctrlKey || e.metaKey) && e.key === 'p') {
                e.preventDefault();
                imprimirRifas();
            }
        });

        // MENSAGEM INFORMATIVA SOBRE O ATALHO
        document.addEventListener('DOMContentLoaded', function() {
            <?php if ($_SERVER["REQUEST_METHOD"] == "POST" && empty($erros)): ?>
            console.log('📝 DICA: USE CTRL+P PARA IMPRIMIR AS RIFAS');
            <?php endif; ?>
        });
    </script>
</body>

</html>