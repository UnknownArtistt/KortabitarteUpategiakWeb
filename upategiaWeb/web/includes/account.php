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

if (!isset($_SESSION['admin'])) {
    echo "<script>window.location.href = '" . $_SERVER['PHP_SELF'] . "';</script>";
    exit;
}

if (isset($_GET['changepass'])) {
    $newpass = $_POST['newpass'];
    $confnewpass = $_POST['confnewpass'];

    if ($newpass !== $confnewpass) {
    	echo "<script>window.location.href = '" . $_SERVER['PHP_SELF'] . "?action=account';</script>";
        //header("Location: " . $_SERVER['PHP_SELF'] . "?action=account");
        exit;
    }

    $oldpass = $_SESSION['password'];
    $usr = $_SESSION['username'];
    $newpass_hash = password_hash($newpass, PASSWORD_DEFAULT);

    $stmt = $conx->prepare("UPDATE users SET password = ? WHERE username = ?");
    $stmt->bind_param("ss", $newpass_hash, $usr);
    $stmt->execute();

    session_destroy();
    echo "<script>window.location.href = '" . $_SERVER['PHP_SELF'] . "';</script>";
    //header("Location: " . $_SERVER['PHP_SELF']);
    exit;
}

elseif (isset($_GET['adduser']) && $_SESSION['username'] === 'admin@bdweb') {
    $newuser = filter_input(INPUT_POST, 'newuser', FILTER_SANITIZE_STRING);
    $newuserpass = password_hash($_POST['newuserpass'], PASSWORD_DEFAULT);

    $stmt = $conx->prepare("INSERT INTO users (username, password) VALUES (?, ?)");
    $stmt->bind_param("ss", $newuser, $newuserpass);
    $stmt->execute();

    //header("Location: " . $_SERVER['PHP_SELF'] . "?action=account");
    echo "<script>window.location.href = '" . $_SERVER['PHP_SELF'] . "?action=account';</script>";
    exit;
}

elseif (isset($_GET['deleteuser']) && $_SESSION['username'] === 'admin@bdweb') {
    $deleteuser = filter_input(INPUT_GET, 'deleteuser', FILTER_SANITIZE_STRING);

    if ($deleteuser === $_SESSION['username']) {
        //header("Location: " . $_SERVER['PHP_SELF'] . "?action=account");
        echo "<script>window.location.href = '" . $_SERVER['PHP_SELF'] . "?action=account';</script>";
        exit;
    }

    $stmt = $conx->prepare("DELETE FROM users WHERE username = ?");
    $stmt->bind_param("s", $deleteuser);
    $stmt->execute();

    //header("Location: " . $_SERVER['PHP_SELF'] . "?action=account");
    echo "<script>window.location.href = '" . $_SERVER['PHP_SELF'] . "?action=account';</script>";
    exit;
}

?>
<div align="center">
    <table width="1000" cellpadding="10" cellspacing="10">
        <tr>
            <td valign="top" align="right">
                <fieldset style="width:300px;">
                    <legend><b>Change Password</b></legend>
                    <form action="<?php echo $_SERVER['PHP_SELF'] . "?action=account&changepass=1"; ?>" method="POST">
                        New Password: <input type="password" name="newpass" required><br>
                        Confirm New Password: <input type="password" name="confnewpass" required><br>
                        <br>
                        <div align="center"><input type="submit" value="Change"></div>
                    </form>
                </fieldset>
            </td>
            <?php if ($_SESSION['username'] === 'admin@bdweb') : ?>
            <td valign="top" align="left">
                <fieldset style="width:300px;">
                    <legend><b>Add User</b></legend>
                    <form action="<?php echo $_SERVER['PHP_SELF'] . "?action=account&adduser=1"; ?>" method="POST">
                        New user's username: <input type="text" name="newuser" required><br>
                        New user's password: <input type="password" name="newuserpass" required><br>
                        <br>
                        <div align="center"><input type="submit" value="Add"></div>
                    </form>
                </fieldset><br>

                <fieldset style="width:300px;">
                    <legend><b>Delete User</b></legend>
                    <table cellpadding="2" cellspacing="2" width="100%">
                        <?php
                        $users = $conx->query("SELECT username FROM users");
                        while ($user = $users->fetch_assoc()) :
                        ?>
                            <tr>
                                <td align="left" class="box"><?php echo outputa_garbitu($user['username']); ?></td>
                                <td align="right" class="box" width="60">
                                    <?php if ($user['username'] !== $_SESSION['username']) : ?>
                                        <a href="<?php echo $_SERVER['PHP_SELF'] . "?action=account&deleteuser=" . outputa_garbitu($user['username']); ?>">[delete]</a>&nbsp;
                                    <?php else : ?>
                                        <del>[delete]</del>&nbsp;
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </table>
                </fieldset>
            </td>
            <?php endif; ?>
        </tr>
    </table>
</div>

<?php
$conx->close();
