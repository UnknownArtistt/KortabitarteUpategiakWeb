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

$images = array_diff(scandir("images"), array(".", ".."));

if (sizeof($images) == 0) {
    echo "<fieldset style='width:500px;'>";
    echo "<legend><b>Ez dago produkturik katalogoan</b></legend>";
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
        foreach ($images as $pic) {
            // Evitar inyecciones SQL con prepared statements
            $stmt = $conx->prepare("SELECT * FROM produktuak WHERE pic = ?");
            $stmt->bind_param("s", $pic);
            $stmt->execute();
            $data = $stmt->get_result()->fetch_assoc();

            if ($data) {
                ?>
                <tr>
                    <td align="center" valign="top" width="40%">
                        <fieldset>
                            <br>
                            <a href="<?php echo "images/" . outputa_garbitu($pic); ?>">
                                <img src="images/<?php echo outputa_garbitu($pic); ?>" border="1">
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
                            <?php echo outputa_garbitu($data['descripzioa']); ?>
                            <br>
                        </fieldset>
                        <br>
                        <a href=""><img src="images/pngegg.png"></a>
                        <br>
                        <?php
                        if (isset($_SESSION['admin']) && ($_SESSION['admin'] == 1) && ($_SESSION['username'] == 'admin')) {
                            ?>
                            <table width="100%" cellpadding="2" cellspacing="2" align="center">
                                <tr>
                                    <td width="50%" align="left">
                                        <a href="<?php echo outputa_garbitu($_SERVER['PHP_SELF']) . "?action=description&pic_id=" . outputa_garbitu($data['ID']); ?>">
                                            <b>Zer sartuko dot hemen?</b>
                                        </a>
                                    </td>
                                    <td width="50%" align="right">
                                        <a href="<?php echo outputa_garbitu($_SERVER['PHP_SELF']) . "?action=description&del_id=" . outputa_garbitu($data['ID']); ?>">
                                            <b>Eta hemen?</b>
                                        </a>
                                    </td>
                                </tr>
                            </table>
                            <?php
                        }
                        ?>
                    </td>
                </tr>
                <?php
            }
        }
        ?>
    </table>
    <?php
}

$conx->close();
?>
