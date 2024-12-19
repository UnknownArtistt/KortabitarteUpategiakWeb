<?php

include("conf.php");
include("mysql.php");

if ($conx->connect_error) {
    die("Errorea konexioan: " . $conx->connect_error);
}

function outputa_garbitu($data) {
    return htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
}

$pic_id = filter_input(INPUT_GET, 'pic_id', FILTER_VALIDATE_INT);

if (isset($_GET['postdescription']) && $pic_id) {
    // Validar y sanitizar entradas
    $deskripzioa = filter_input(INPUT_POST, 'deskripzioa', FILTER_SANITIZE_STRING);
    $salneurria = filter_input(INPUT_POST, 'salneurria', FILTER_VALIDATE_FLOAT);

    $stmt = $conx->prepare("SELECT deskripzioa, salneurria FROM produktuak WHERE ID = ?");
    $stmt->bind_param("i", $pic_id);
    $stmt->execute();
    $crntcomm = $stmt->get_result()->fetch_assoc();

    if (!$salneurria) {
        $salneurria = $crntcomm['salneurria'];
    }

    $stmt = $conx->prepare("UPDATE produktuak SET deskripzioa = ?, salneurria = ? WHERE ID = ?");
    $stmt->bind_param("sdi", $deskripzioa, $salneurria, $pic_id);
    $stmt->execute();

    //header("Location: " . $_SERVER['PHP_SELF']);
    echo "<script>window.location.href = '" . $_SERVER['PHP_SELF'] . "';</script>";
    exit;
} else if ($pic_id) {
    $stmt = $conx->prepare("SELECT * FROM produktuak WHERE ID = ?");
    $stmt->bind_param("i", $pic_id);
    $stmt->execute();
    $data = $stmt->get_result()->fetch_assoc();

    if ($data):
?>

<div align="center">
    <fieldset style="width:300px;">
        <legend><b>Descripción</b></legend>
        <br>
        <h4><?php echo outputa_garbitu($data['izena']) . " - " . outputa_garbitu($data['salneurria']) . "€"; ?></h4>
        <img src="images/<?php echo outputa_garbitu($data['pic']); ?>" border="1"><br>
        <br>
        <form action="<?php echo $_SERVER['PHP_SELF'] . "?action=description&pic_id=" . $pic_id . "&postdescription=1"; ?>" method="POST">
            <h5>Deskripzioa:</h5>
            <textarea name="deskripzioa" cols="50" rows="10"><?php echo outputa_garbitu($data['deskripzioa']); ?></textarea><br>
            <br>
            Salneurri berria: <input type="text" name="salneurria">
            <br><br>
            <input type="submit" value="Aldatu">
        </form>
    </fieldset>
</div>

<?php
    endif;
}

$conx->close();
?>
