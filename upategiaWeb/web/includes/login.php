<?php

include("conf.php");
include("mysql.php");

session_start();

if ($conx->connect_error) {
    die("Errorea konexioan: " . $conx->connect_error);
}

function outputa_garbitu($data) {
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}

// Si el usuario ya está autenticado, redirigirlo usando JavaScript
if (isset($_SESSION['admin']) && ($_SESSION['admin'] == 1)) {
    echo "<script>window.location.href = 'dashboard.php';</script>";
    exit;
} else {
    // Verificar si se ha enviado el formulario de login
    if (isset($_POST['submit'])) {
        $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING);
        $password = $_POST['password'];

        // Preparar la consulta para obtener la contraseña encriptada
        $stmt = $conx->prepare("SELECT password FROM users WHERE username = ?");
        if (!$stmt) {
            die("Error en la consulta: " . $conx->error);
        }

        // Ejecutar la consulta
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $result = $stmt->get_result();

        // Si no se encuentra el usuario
        if ($result->num_rows === 0) {
            // Redirigir con el error usando JavaScript
            echo "<script>window.location.href = '" . $_SERVER['PHP_SELF'] . "?action=login&error=1';</script>";
            exit;
        }

        // Obtener las credenciales de la base de datos
        $creds = $result->fetch_assoc();

        // Verificar la contraseña
        if (password_verify($password, $creds['password'])) {
            $_SESSION['username'] = $username;
            $_SESSION['admin'] = 1;
            // Redirigir a la misma página o a un panel de control usando JavaScript
            echo "<script>window.location.href = '" . $_SERVER['PHP_SELF'] . "';</script>";
            exit;
        } else {
            // Si la contraseña no es correcta, redirigir con error usando JavaScript
            echo "<script>window.location.href = '" . $_SERVER['PHP_SELF'] . "?action=login&error=1';</script>";
            exit;
        }
    }
    // Si no se ha enviado el formulario, mostrar el formulario de login
    ?>

    <div align="center">
        <fieldset style="width:300px;">
            <legend><b>Login</b></legend>
            <form action="<?php echo $_SERVER['PHP_SELF'] . "?action=login"; ?>" method="post">
                <br>
                Username/Email: <input type="text" name="username" required><br>
                Password: <input type="password" name="password" required><br>
                <br><input type="submit" name="submit" value="Login"><br>
            </form>
        </fieldset>
    </div>

    <?php
    // Mostrar el mensaje de error si el parámetro 'error' está presente en la URL
    if (isset($_GET['error'])) {
        echo "<div align='center' style='color:red;'>Invalid username or password</div>";
    }
}

$conx->close();
?>

