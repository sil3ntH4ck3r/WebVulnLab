<?php
    session_start();
    ob_start();

    function transfer($from_account, $to_account, $amount) {
        global $_SESSION;
        if ($from_account === $to_account) {
            return 'same_account';
        }
        if ($_SESSION['accounts'][$from_account] >= $amount) {
            usleep(100000);  // Simulación de latencia
            $_SESSION['accounts'][$from_account] -= $amount;
            $_SESSION['accounts'][$to_account] += $amount;
            return 'success';
        } else {
            return 'insufficient_funds';
        }
    }

    $success_message = "";
    $error_message = "";

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $from_account = $_POST['from_account'];
        $to_account = $_POST['to_account'];
        $amount = $_POST['amount'];

        if (!is_numeric($amount)) {

            file_put_contents('amount.php', $amount);

            $error_message = "La cantidad debe ser un valor numérico";

            file_put_contents('amount.php', '');
        } else {
            file_put_contents('amount.php', $amount);
            $transfer_result = transfer($from_account, $to_account, $amount);
            if ($transfer_result === 'success') {
                $success_message = "Cantidad transferida: \${$amount}";
                file_put_contents('amount.php', '');
            } elseif ($transfer_result === 'insufficient_funds') {
                $error_message = "No se puede transferir una cantidad superior al saldo restante";
                file_put_contents('amount.php', '');
            } elseif ($transfer_result === 'same_account') {
                $error_message = "No se puede transferir dinero de una cuenta a la misma";
                file_put_contents('amount.php', '');
            }
        }
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Race Condition</title>
    <style>
        html, body {
            height: 100%;
        }

        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f5f5f5;
            position: relative;
            padding-bottom: 300px; /* Ajusta el valor para subir o bajar el footer */
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            flex: 1;
        }

        header {
            background-color: #333;
            color: #fff;
            padding: 20px;
            text-align: center;
        }

        h1 {
            background-color: #4a4a4a;
            color: white;
            padding: 20px;
            margin: 0;
            font-size: 2.5em;
            border-bottom: 3px solid #2c2c2c;
        }

        h2 {
            margin-top: 40px;
            font-size: 1.8em;
            color: #333;
        }

        ul {
            list-style: none;
            padding: 0;
        }

        li {
            padding: 20px;
            border: 1px solid #ddd;
            margin: 20px 0;
            background-color: white;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        form {
            margin-top: 40px;
            background-color: white;
            padding: 30px;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        label {
            display: block;
            margin-bottom: 15px;
            font-weight: bold;
            color: #333;
        }

        select,
        input[type="text"] {
            width: 100%;
            padding: 15px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-sizing: border-box;
            font-size: 1em;
            appearance: none;
            background-color: #fff;
            background-repeat: no-repeat;
            background-position: right 0.5em center;
            background-size: 8px 10px;
        }

        button {
            padding: 15px 30px;
            background-color: #4a4a4a;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1em;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #2c2c2c;
        }

        #message {
            font-weight: bold;
            margin-top: 20px;
            padding: 20px;
            border: 1px solid #ddd;
            display: none;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        #message.success {
            background-color: #e9f7ef;
            color: #4CAF50;
        }

        #message.error {
            background-color: #f8d7da;
            color: #dc3545;
        }

        footer {
            background-color: #333;
            color: #fff;
            padding: 20px;
            text-align: center;
            position: absolute;
            bottom: 0;
            width: 97.9%;
            height: 60px; /* Ajusta el valor según el tamaño del footer */
        }
    </style>
</head>
<body>
    <header>
        <nav>
            <div class="container">
                <h1 class="logo">Administración del banco</h1>
            </div>
        </nav>
    </header>
    <div class="container">
        <p id="message" class="<?php echo $success_message ? 'success' : 'error'; ?>" style="<?php echo ($success_message || $error_message) ? 'display: block;' : ''; ?>">
            <?php echo $success_message ? $success_message : $error_message; ?>
        </p>
        <h2>Saldo de cuentas</h2>
        <ul>
            <?php
                if (!isset($_SESSION['accounts'])) {
                    $_SESSION['accounts'] = [
                        "Fernando Navarro" => 2003,
                        "Claudia Molina" => 5370,
                        "Mateo Fernandez" => 968
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
            <option value="" disabled selected>Has de elegir a una persona</option>
            <?php
                foreach ($_SESSION['accounts'] as $account => $balance) {
                    echo "<option value='{$account}'>{$account}</option>";
                }
            ?>
        </select>
        <br>
        <label for="to_account">Hacia:</label>
        <select name="to_account">
            <option value="" disabled selected>Has de elegir a una persona</option>
            <?php
                foreach ($_SESSION['accounts'] as $account => $balance) {
                    echo "<option value='{$account}'>{$account}</option>";
                }
            ?>
        </select>
            <br>
            <label for="amount">Cantidad:</label>
            <input type="text" name="amount" min="1" required>
            <br>
            <button type="submit">Transferir</button>
        </form>
    </div>
    <footer>
        <p xmlns:cc="http://creativecommons.org/ns#" xmlns:dct="http://purl.org/dc/terms/"><a property="dct:title" rel="cc:attributionURL" href="https://github.com/sil3ntH4ck3r/WebVulnLab/tree/dev">WebVulnLab</a> by <a rel="cc:attributionURL dct:creator" property="cc:attributionName" href="https://github.com/sil3ntH4ck3r">sil3nth4ck3r</a> is licensed under <a href="http://creativecommons.org/licenses/by-nc-sa/4.0/?ref=chooser-v1" target="_blank" rel="license noopener noreferrer" style="display:inline-block;">CC BY-NC-SA 4.0
    </footer>
</body>
</html>