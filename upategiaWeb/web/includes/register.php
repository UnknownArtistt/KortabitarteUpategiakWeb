<?php
include("conf.php");
include("mysql.php");

if ($conx->connect_error) {
    die("Errorea konexioan: " . $conx->connect_error);
}

// Inicializar variables vacías
$email = $firstname = $lastname = $postcode = $city = $stateProv = $country = $telephone = $password = $password2 = $imagen = "";
$error_email = $error_password = $error_postcode = $error_telephone = $error_imagen = "";

// Procesar el formulario al enviar datos
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $firstname = htmlspecialchars($_POST['firstname'], ENT_QUOTES, 'UTF-8');
    $lastname = htmlspecialchars($_POST['lastname'], ENT_QUOTES, 'UTF-8');
    $city = htmlspecialchars($_POST['city'], ENT_QUOTES, 'UTF-8');
    $stateProv = htmlspecialchars($_POST['stateProv'], ENT_QUOTES, 'UTF-8');
    $country = htmlspecialchars($_POST['country'], ENT_QUOTES, 'UTF-8');
    $postcode = preg_replace('/[^0-9]/', '', $_POST['postcode']);
    $telephone = preg_replace('/[^0-9]/', '', $_POST['telephone']);
    $password = $_POST['password'];
    $password2 = $_POST['password2'];

    // Validaciones
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_email = "Korreo ez da baliozkoa";
    }

    if (!preg_match('/^\d{9}$/', $telephone)) {
        $error_telephone = "Telefonoak 9 digitu izan behar ditu";
    }

    if (!preg_match('/^\d{5}$/', $postcode)) {
        $error_postcode = "POsta kodeak 5 digitu izan behar ditu";
    }

    if (strlen($password) < 8) {
        $error_password = "Pasahitzak gutxienez 8 karaktere izan behar ditu";
    } elseif ($password !== $password2) {
        $error_password = "Pasahitzek ez dute bat egiten";
    } else {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    }

    /*
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == UPLOAD_ERR_OK) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $fileType = mime_content_type($_FILES['imagen']['tmp_name']);
        
        if (in_array($fileType, $allowedTypes)) {
            $imagen = uniqid() . "_" . basename($_FILES['imagen']['name']);
            if (!move_uploaded_file($_FILES['imagen']['tmp_name'], "perfiles/" . $imagen)) {
                $error_imagen = "No se pudo mover el archivo a la carpeta destino.";
            }
        } else {
            $error_imagen = "Formato de imagen no permitido.";
        }
    } else {
        $error_imagen = "Error al subir la imagen.";
    }
    */

     // Procesar imagen
    if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] == UPLOAD_ERR_OK) {
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        $fileType = mime_content_type($_FILES['imagen']['tmp_name']);
        
        if (in_array($fileType, $allowedTypes)) {
            $imagen = uniqid() . "_" . basename($_FILES['imagen']['name']);
            move_uploaded_file($_FILES['imagen']['tmp_name'], "perfiles/" . $imagen);
        } else {
            die("Error: Formato de imagen no permitido.");
        }
    }

    // Insertar en la base de datos si no hay errores
    if (empty($error_email) && empty($error_password) && empty($error_postcode) && empty($error_telephone) && empty($error_imagen)) {
        $stmt = $conx->prepare("INSERT INTO users (username, password, izena, abizena, hiria, lurraldea, herrialdea, postakodea, telefonoa, irudia) 
                                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("sssssssiis", $email, $hashedPassword, $firstname, $lastname, $city, $stateProv, $country, $postcode, $telephone, $imagen);

        if (!$stmt->execute()) {
            die("Error: " . $stmt->error);
        } else {
            //header("Location: index.php");
            echo "<script>window.location.href = '" . $_SERVER['PHP_SELF'] . "';</script>";
            exit;
        }

        $stmt->close();
    }
}
?>

<!-- Formulario HTML -->
<div class="content">
    <br/>
    <div class="register">
        <h2>Erregistroa egin</h2>
        <br/>
        <b>Introduce la información.</b>
        <br/>
        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF'] . "?action=register"); ?>" method="POST" enctype="multipart/form-data">
            <p>
                <label>Email/username: </label>
                <input type="text" name="email" value="<?php echo htmlspecialchars($email); ?>" />
                <?php if ($error_email) echo "<p>$error_email</p>"; ?>
            </p>
            <p>
                <label>Izena: </label>
                <input type="text" name="firstname" value="<?php echo htmlspecialchars($firstname); ?>" />
            </p>
            <p>
                <label>Abizena: </label>
                <input type="text" name="lastname" value="<?php echo htmlspecialchars($lastname); ?>" />
            </p>
            <p>
                <label>Hiria: </label>
                <input type="text" name="city" value="<?php echo htmlspecialchars($city); ?>" />
            </p>
            <p>
                <label>Lurraldea: </label>
                <input type="text" name="stateProv" value="<?php echo htmlspecialchars($stateProv); ?>" />
            </p>
            <p>
                <label>Herrialdea: </label>
                <input type="text" name="country" value="<?php echo htmlspecialchars($country); ?>" />
            </p>
            <p>
                <label>Postakodea: </label>
                <input type="text" name="postcode" value="<?php echo htmlspecialchars($postcode); ?>" />
                <?php if ($error_postcode) echo "<p>$error_postcode</p>"; ?>
            </p>
            <p>
                <label>Telefonoa: </label>
                <input type="text" name="telephone" value="<?php echo htmlspecialchars($telephone); ?>" />
                <?php if ($error_telephone) echo "<p>$error_telephone</p>"; ?>
            </p>
            <p>
                <label>Pasahitza: </label>
                <input type="password" name="password" />
                <?php if ($error_password) echo "<p>$error_password</p>"; ?>
            </p>
            <p>
                <label>Pasahitza errepikatu: </label>
                <input type="password" name="password2" />
            </p>
            <p>
                <label>Irudia aukeratu:</label>
                <input name="imagen" type="file" />
                <?php if ($error_imagen) echo "<p>$error_imagen</p>"; ?>
            </p>
            <p>
                <input type="reset" value="Clear" class="button"/>
                <input type="submit" value="Submit" class="button marL10"/>
            </p>
        </form>
    </div>
</div>
