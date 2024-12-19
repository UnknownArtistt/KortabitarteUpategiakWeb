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

if ($_SESSION['username'] != "admin@bdweb") {
    //header("Location: " . htmlspecialchars($_SERVER['PHP_SELF']));
    echo "<script>window.location.href = '" . $_SERVER['PHP_SELF'] . "';</script>";
    exit;
}

if (isset($_GET['pic_id'])) {
    $pic_id = intval($_GET['pic_id']);

    $stmt = $conx->prepare("SELECT pic FROM produktuak WHERE id = ?");
    $stmt->bind_param("i", $pic_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $delfile = $result->fetch_assoc();

    if ($delfile) {
        unlink("images/" . outputa_garbitu($delfile['pic']));
        
        $stmt = $conx->prepare("DELETE FROM produktuak WHERE id = ?");
        $stmt->bind_param("i", $pic_id);
        $stmt->execute();

        echo "<div align='center'><h5>Produktua ezabatuta</h5></div><br>";
    }
}

/*
if (isset($_GET['upload'])) {
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
    $path = "images/" . basename($_FILES['upfile']['name']);
    $uploader = $_SESSION['username'];

    if (in_array($_FILES['upfile']['type'], $allowed_types) && move_uploaded_file($_FILES['upfile']['tmp_name'], $path)) {
        $izena = $conx->real_escape_string($_POST['izena']);
        $deskripzioa = $conx->real_escape_string($_POST['deskripzioa']);
        $salneurria = floatval($_POST['salneurria']);
        $stock = intval($_POST['stock']);
        $pic = $conx->real_escape_string($_FILES['upfile']['name']);

        $stmt = $conx->prepare("INSERT INTO produktuak (izena, deskripzioa, salneurria, pic, stock) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssdsi", $izena, $deskripzioa, $salneurria, $pic, $stock);
        $stmt->execute();

        echo "<div align='center'><h5>Produktu \"" . outputa_garbitu($izena) . "\" txertatuta</h5></div><br>";
    } else {
        echo "<div align='center'><h5>Errorea: Fitxategia ez da egokia edo ezin izan da igo.</h5></div><br>";
    }
}
*/

if (isset($_GET['upload'])) {
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];

    // Verifica si se subió un archivo
    if (isset($_FILES['upfile']) && $_FILES['upfile']['error'] === UPLOAD_ERR_OK) {
        // Imprime información de depuración
        var_dump($_FILES);

        // Verifica el tipo de archivo
        $fileType = mime_content_type($_FILES['upfile']['tmp_name']);
        if (!in_array($fileType, $allowed_types)) {
            die("Error: Tipo de archivo no permitido. Tipo recibido: " . $fileType);
        }

        // Genera un nombre único para la imagen
        $pic = uniqid() . "_" . basename($_FILES['upfile']['name']);
        $path = "images/" . $pic;

        // Mueve el archivo a la carpeta de destino
        if (move_uploaded_file($_FILES['upfile']['tmp_name'], $path)) {
            // Procesa los datos y realiza la inserción en la base de datos
            $izena = $conx->real_escape_string($_POST['izena']);
            $deskripzioa = $conx->real_escape_string($_POST['deskripzioa']);
            $salneurria = floatval($_POST['salneurria']);
            $stock = intval($_POST['stock']);

            $stmt = $conx->prepare("INSERT INTO produktuak (izena, deskripzioa, salneurria, pic, stock) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("ssdsi", $izena, $deskripzioa, $salneurria, $pic, $stock);

            if ($stmt->execute()) {
                echo "<div align='center'><h5>Produktu \"" . htmlspecialchars($izena) . "\" txertatuta</h5></div><br>";
            } else {
                echo "<div align='center'><h5>Errorea: " . $stmt->error . "</h5></div><br>";
            }
        } else {
            die("Error: No se pudo mover el archivo. Verifica permisos de la carpeta.");
        }
    } else {
        // Imprime el código de error para entender qué pasó
        $error_code = $_FILES['upfile']['error'];
        die("Error: Problema al subir el archivo. Código de error: $error_code");
    }
}

?>

<div align="center">
    <table width="1000" cellpadding="10" cellspacing="10" align="center">
        <tr>
            <td valign="top" align="left">
                <fieldset style="width:300px;">
                    <legend><b>Produktu berria</b></legend>
                    <form enctype="multipart/form-data" action="<?php echo outputa_garbitu($_SERVER['PHP_SELF']) . "?action=updel&upload=1"; ?>" method="POST">
                        Izena: <input type="text" name="izena" required><br>
                        Deskripzioa: <input type="text" name="deskripzioa" required><br>
                        Salneurria: <input type="number" step="0.01" name="salneurria" required><br>
                        Stock: <input type="number" name="stock" required><br>
                        Irudia aukeratu:<br><br>
                        <input name="upfile" type="file" required><br><br>
                        <input type="submit" value="Igo">
                    </form>
                </fieldset>
            </td>
            <td valign="top" align="left">
                <fieldset style="width:300px;">
                    <legend><b>Ezabatu</b></legend>
                    <?php
                    $delquery = $conx->query("SELECT * FROM produktuak");
                    while ($produktua = $delquery->fetch_assoc()) {
                        ?>
                        <p><?php echo outputa_garbitu($produktua['izena']); ?></p><br>
                        <a href="images/<?php echo outputa_garbitu($produktua['pic']); ?>">
                            <img src="images/<?php echo outputa_garbitu($produktua['pic']); ?>" border="1">
                        </a><br>
                        <a href="<?php echo outputa_garbitu($_SERVER['PHP_SELF']) . "?action=updel&pic_id=" . intval($produktua['id']); ?>">
                            <b>Produktua ezabatu</b>
                        </a>
                        <br><br>
                        <?php
                    }
                    ?>
                </fieldset>
            </td>
        </tr>
    </table>
</div>

<?php
$conx->close();
?>
