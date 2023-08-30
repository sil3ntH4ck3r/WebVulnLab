<?php
    session_start();
    ob_start();

    function transfer($from_account, $to_account, $amount) {
        global $_SESSION;
        if ($_SESSION['accounts'][$from_account] >= $amount) {
            usleep(100000);  // Simulación de latencia
            $_SESSION['accounts'][$from_account] -= $amount;
            $_SESSION['accounts'][$to_account] += $amount;
        }
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $from_account = $_POST['from_account'];
        $to_account = $_POST['to_account'];
        $amount = $_POST['amount'];

        if (!is_numeric($amount)) {

            // Guardar la cantidad transferida en el archivo amount.php
            file_put_contents('amount.php', $amount);

            // Si amount no es numérico, no realizamos la transferencia
            $error_message = "El monto debe ser un valor numérico";
        } else {
            transfer($from_account, $to_account, $amount);

            // Guardar la cantidad transferida en el archivo amount.php
            file_put_contents('amount.php', $amount);
        }
    }

    $transferred_amount = file_get_contents('amount.php');

    // Vaciar el archivo amount.php si se ha mostrado la cantidad transferida
    if ($transferred_amount) {
        file_put_contents('amount.php', '');
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bank App</title>
    <style>
        #message {
            display: none; /* Ocultar inicialmente */
            font-weight: bold;
        }
    </style>
    <script>
        function showMessageAndRedirect(transferred_amount) {
            var messageElement = document.getElementById("message");
            messageElement.textContent = "Cantidad transferida: $" + transferred_amount;
            messageElement.style.display = "block";
        }
    </script>
</head>
<body>
    <h1>Bank App</h1>
    <h2>Saldo de cuentas</h2>
    <ul>
        <?php
        if (!isset($_SESSION['accounts'])) {
            $_SESSION['accounts'] = [
                "user1" => 1000,
                "user2" => 1000
            ];
        }
        foreach ($_SESSION['accounts'] as $account => $balance) {
            echo "<li>{$account}: \${$balance}</li>";
        }
        ?>
    </ul>
    <h2>Transferir dinero</h2>
    <form action="index.php" method="POST">
    <label for="from_account">Desde:</label>
        <select name="from_account">
            <?php
            foreach ($_SESSION['accounts'] as $account => $balance) {
                echo "<option value='{$account}'>{$account}</option>";
            }
            ?>
        </select>
        <br>
        <label for="to_account">Hacia:</label>
        <select name="to_account">
            <?php
            foreach ($_SESSION['accounts'] as $account => $balance) {
                echo "<option value='{$account}'>{$account}</option>";
            }
            ?>
        </select>
        <br>
        <label for="amount">Monto:</label>
        <input type="text" name="amount" min="1" required>
        <br>
        <button type="submit">Transferir</button>
    </form>

    <p id="message"></p>

    <?php if ($transferred_amount): ?>
        <script>
            showMessageAndRedirect(<?php echo $transferred_amount; ?>);
        </script>
    <?php endif; ?>

    <?php if (isset($error_message)): ?>
        <h3>Error: <?php echo $error_message; ?></h3>
    <?php endif; ?>

</body>
</html>


