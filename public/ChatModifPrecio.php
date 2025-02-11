<?php
session_start();
require_once 'Acceso.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: Login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $chatId = intval($_POST['chat_id']);
    $newPrice = $_POST['new_price'];

    $database = new AccessDB("localhost:3306", "root", "tETOTETO1", "fantazon");
    $database->connectarse();
    $conn = $database->getConnection();

    $sql = "CALL ModifChatPrecio(?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("id", $chatId, $newPrice);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo "<script>alert('¡Precio del chat modificado con éxito!');</script>";
    } else {
        echo "<script>alert('¡Error al modificar el precio del chat!');</script>";
    }

    $stmt->close();
    $database->closeConnection();

    // Redirect back to the same chat
    header("Location: Chat.php?chat_id=$chatId");
    exit();
}
?>
