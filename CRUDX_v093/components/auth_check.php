<?php
function authorize($allowedRoles) {
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit;
    }

    if (!in_array($_SESSION['role'], $allowedRoles)) {
        echo "Nincs jogosultságod az oldal megtekintéséhez.";
        echo "<br><a href='index.php'>Vissza a főoldalra</a>";
        exit;
    }

    function hasRole($allowedRoles) {
        if (!isset($_SESSION['role'])) {
            return false;
        }
        return in_array($_SESSION['role'], $allowedRoles);
    }
}
?>