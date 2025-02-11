<?php
require_once 'Acceso.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: Login.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_POST['list_id']) || !isset($_POST['product_id'])) {
        echo "Parámetros incorrectos.";
        exit();
    }
    
    $list_id = intval($_POST['list_id']);
    $product_id = intval($_POST['product_id']);

    $database = new AccessDB("localhost:3306", "root", "tETOTETO1", "fantazon");
    $database->connectarse();
    $conn = $database->getConnection();

    $stmt = $conn->prepare("CALL InsertProdLista(?, ?)");
    $stmt->bind_param("ii", $list_id, $product_id);
    $stmt->execute();

    header("Location: List.php?list_id={$list_id}");
    exit();
} else {
    echo "Método de solicitud incorrecto.";
    exit();
}
?>
