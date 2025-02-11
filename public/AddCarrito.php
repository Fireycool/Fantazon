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

$userId = $_SESSION['user_id'];
$productId = intval($_POST['product_id']);
$quantity = 1;
$negotiatedPrice = isset($_POST['trueque']) ? floatval($_POST['trueque']) : null;

$database = new AccessDB("localhost:3306", "root", "tETOTETO1", "fantazon");
$database->connectarse();
$conn = $database->getConnection();

$sql_add_product_to_cart = "CALL AddProductToCart(?, ?, ?, ?)";
$stmt = $conn->prepare($sql_add_product_to_cart);
$stmt->bind_param("iiid", $userId, $productId, $quantity, $negotiatedPrice);

try {
    $stmt->execute();
    header("Location: Carrito.php");
    exit();
} catch (mysqli_sql_exception $e) {
    if (strpos($e->getMessage(), 'No hay suficiente Cantidad para agregar al carrito.') !== false) {
        echo "<script>alert('No hay suficiente Cantidad para agregar al carrito.'); window.location.href = 'Carrito.php';</script>";
    } else {
        // For any other error
        echo "<script>alert('Error: " . $e->getMessage() . "'); window.location.href = 'Main_Page.php';</script>";
    }
}

$stmt->close();
$conn->close();
?>
