<?php
require_once 'Acceso.php';
session_start();

if (!isset($_GET['carrito_id'])) {
    header("Location: Main_Page.php");
    exit();
}

$carrito_id = intval($_GET['carrito_id']);

$database = new AccessDB("localhost:3306", "root", "tETOTETO1", "fantazon");
$database->connectarse();
$conn = $database->getConnection();

$sql_call_procedure = "CALL RealizarVenta(?)";

$stmt = $conn->prepare($sql_call_procedure);
$stmt->bind_param("i", $carrito_id);

try {
    $stmt->execute();

    header("Location: Carrito.php");
    exit();
} catch (mysqli_sql_exception $e) {
    echo "Error: " . $e->getMessage();
}

$stmt->close();
$conn->close();
?>
