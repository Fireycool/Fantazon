<?php
require_once 'Acceso.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: Login.php");
    exit();
}

if (!isset($_POST['product_id'])) {
    header("Location: Main_Page.php");
    exit();
}

$buyerId = $_SESSION['user_id'];
$productId = intval($_POST['product_id']);

$database = new AccessDB("localhost:3306", "root", "tETOTETO1", "fantazon");
$database->connectarse();
$conn = $database->getConnection();

// Ver si el chat de este producto ya existe
$sql_check_chat = "CALL GetChat(?, ?)";
$stmt = $conn->prepare($sql_check_chat);
$stmt->bind_param("ii", $buyerId, $productId);
$stmt->execute();
$result = $stmt->get_result();

if ($result === false) {
    // Handle query execution error
    echo "Error executing query: " . $conn->error;
    exit();
}

$chatExists = $result->num_rows > 0;

$stmt->close();

// Si ya existe entonces accesar al chat
if ($chatExists) {
    $chatInfo = $result->fetch_assoc();
    $chatId = $chatInfo['Chat_ID'];
    header("Location: Chat.php?chat_id=$chatId");
    exit();
}

// Si no existe, crear nuevo chat y accesar
$sql_create_chat = "CALL InsertChat(?, ?)";
$stmt = $conn->prepare($sql_create_chat);
$stmt->bind_param("ii", $buyerId, $productId);
$stmt->execute();
$result = $stmt->get_result();

if ($result === false) {
    echo "Error executing query: " . $conn->error;
    exit();
}

$newChatId = $result->fetch_assoc()['New_Chat_ID'];

header("Location: Chat.php?chat_id=$newChatId");
exit();
?>
