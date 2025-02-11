<?php
require_once 'Acceso.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $userid = $_POST["user_id"];

    $database = new AccessDB("localhost:3306", "root", "tETOTETO1", "fantazon");
    $database->connectarse();
    $conn = $database->getConnection();

    $sql_delete_user = "CALL DeleteUser(?)";
    $stmt_delete_user = $conn->prepare($sql_delete_user);
    $stmt_delete_user->bind_param("i", $userid);
    $stmt_delete_user->execute();

    header("Location: Logout.php");
    exit();
}
?>
