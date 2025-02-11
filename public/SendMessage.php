<?php
require_once 'Acceso.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: Login.php");
    exit();
}

if (!isset($_POST['message']) || !isset($_POST['chat_id'])) {
    header("Location: Main_Page.php");
    exit();
}

$message = $_POST['message'];
$chatId = intval($_POST['chat_id']);

$database = new AccessDB("localhost:3306", "root", "tETOTETO1", "fantazon");
$database->connectarse();
$conn = $database->getConnection();

$sql_insert_message = "CALL InsertMessage(?, ?, ?)";
$stmt = $conn->prepare($sql_insert_message);
$stmt->bind_param("iss", $chatId, $_SESSION['user_id'], $message);
$stmt->execute();

$stmt->close();

header("Location: Chat.php?chat_id=$chatId");
exit();
?>
