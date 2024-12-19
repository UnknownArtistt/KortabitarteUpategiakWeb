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

$testua = isset($_GET['keyword']) ? trim($_GET['keyword']) : "";
$testua = $conx->real_escape_string($testua);

$search = "%" . $testua . "%";

// Usar prepared statement para prevenir inyección SQL
$stmt = $conx->prepare("SELECT * FROM produktuak WHERE izena LIKE ? OR deskripzioa LIKE ?");
$stmt->bind_param("ss", $search, $search);
$stmt->execute();
$result = $stmt->get_result();

$produktuak = array();
while ($row = $result->fetch_assoc()) {
    $produktuak[] = $row;
}

if (empty($produktuak)) {
    echo "<fieldset style='width:500px;'>";
    echo "<legend><b>Ez dago produkturik katalogoan " . outputa_garbitu($testua) . " deitzen denik</b></legend>";
    if (isset($_SESSION['admin'])) {
        echo "<div align='center'><h3><b><a href='" . outputa_garbitu($_SERVER['PHP_SELF']) . "?action=updel'>Eguneratu</a> katalogoa.</b></h3></div>";
    } else {
        echo "<div align='center'><h3>Enpresaren hasierako orria</h3></div>";
    }
    echo "</fieldset>";
} else {
    ?>
    <table width="1000" cellpadding="10" cellspacing="10" align="center">
        <?php
        foreach ($produktuak as $data) {
            ?>
            <tr>
                <td align="center" valign="top" width="40%">
                    <fieldset>
                        <br>
                        <a href="<?php echo "images/" . outputa_garbitu($data["pic"]); ?>">
                            <img src="images/<?php echo outputa_garbitu($data["pic"]); ?>" border="1">
                        </a>
                        <br><br>
                    </fieldset>
                </td>
                <td valign="top" width="60%">
                    <fieldset>
                        <legend><b>Izena</b></legend>
                        <br>
                        <?php 
                        echo outputa_garbitu($data['izena']) . " - " . outputa_garbitu($data['salneurria']) . "€"; 
                        ?>
                        <br>
                    </fieldset>
                    <fieldset>
                        <legend><b>Deskripzioa</b></legend>
                        <br>
                        <?php echo outputa_garbitu($data['deskripzioa']); ?>
                        <br>
                    </fieldset>
                    <br>
                    <?php
                    //if (isset($_SESSION['admin']) && ($_SESSION['admin'] == 1)) {
                        if ($_SESSION['username'] == 'admin@bdweb') {
                            ?>
                            <table width="100%" cellpadding="2" cellspacing="2" align="center">
                                <tr>
                                    <td width="50%" align="left">
                                        <a href="<?php echo outputa_garbitu($_SERVER['PHP_SELF']) . "?action=description&pic_id=" . outputa_garbitu($data['id']); ?>">
                                            <b>Deskripzioa/Salneurria aldatu</b>
                                        </a>
                                    </td>
                                </tr>
                            </table>
                            <?php
                        } else {
                            echo "<a href='#'><img src='images/pngegg.png'></a>";
                        }
                    //}
                    ?>
                </td>
            </tr>
            <?php
        }
        ?>
    </table>
    <?php
}

$stmt->close();
$conx->close();
?>
