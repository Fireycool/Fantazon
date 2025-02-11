<?php 

require_once 'Acceso.php';
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);

if (!isset($_SESSION['user_id'])) {
    header("Location: Login.php");
    exit();
}

$userId = $_SESSION['user_id'];

$database = new AccessDB("localhost:3306", "root", "tETOTETO1", "fantazon");
$database->connectarse();
$conn = $database->getConnection();

// Call the stored procedure to get cart contents
$sql_get_cart = "CALL GetCarrito(?)";
$stmt = $conn->prepare($sql_get_cart);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$cartContents = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Borrar un solo articulo
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_item'])) {
    $carrito_id = $_POST['carrito_id'];
    $producto_id = $_POST['producto_id'];

    $sql_delete_item = "CALL DeleteProductCart(?, ?)";
    $stmt_delete_item = $conn->prepare($sql_delete_item);
    $stmt_delete_item->bind_param("ii", $carrito_id, $producto_id);
    $stmt_delete_item->execute();
    $stmt_delete_item->close();

    header("Location: Carrito.php");
    exit();
}

// Borrar el Carrito Entero
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['clear_cart'])) {
    $carrito_id = $_POST['carrito_id'];

    $sql_clear_cart = "CALL DeleteAllProductCart(?)";
    $stmt_clear_cart = $conn->prepare($sql_clear_cart);
    $stmt_clear_cart->bind_param("i", $carrito_id);
    $stmt_clear_cart->execute();
    $stmt_clear_cart->close();

    header("Location: Carrito.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Fantazon</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" />
    <link rel="stylesheet" href="Estilos/MainPage_Style.css" />
</head>
<body>
    <?php include 'UI/header.php'; ?>
    <?php include 'UI/navegacion.php'; ?>

    <!-- CARRITO -->
    <main class="container">
        <section class="contenido mt-4">
            <h2>Carrito de Compras</h2>
            <form action="Carrito.php" method="post">
                <div class="card-deck-container">
                    <?php if (empty($cartContents)): ?>
                        <p>Tu carrito está vacío.</p>
                    <?php else: ?>
                        <?php $total = 0; ?>
                        <?php foreach ($cartContents as $item): ?>
                            <?php $subtotal = $item['Cantidad'] * $item['Precio']; ?>
                            <?php $total += $subtotal; ?>
                            <div class="card">
                                <img src="data:image/jpeg;base64,<?php echo base64_encode($item['Foto_1']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($item['Producto']); ?>" />
                                <div class="card-body">
                                    <h3 class="card-title"><?php echo htmlspecialchars($item['Producto']); ?></h3>
                                    <p class="card-text-producto">$<?php echo number_format($item['Precio'], 2); ?></p>
                                    <p class="card-text-producto">Cantidad: <?php echo htmlspecialchars($item['Cantidad']); ?></p>
                                    <p class="card-text-producto">Subtotal: $<?php echo number_format($subtotal, 2); ?></p>
                                </div>
                                <div class="card-footer">
                                    <form action="Carrito.php" method="post">
                                        <button type="submit" class="btn btn-outline-danger" name="delete_item">Eliminar</button>
                                        <input type="hidden" name="carrito_id" value="<?php echo $item['Carrito_ID']; ?>">
                                        <input type="hidden" name="producto_id" value="<?php echo $item['Producto_ID']; ?>">
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </form>

            <?php if (!empty($cartContents)): ?>
                <div class="mt-4">
                    <h4>Total: $<?php echo number_format($total, 2); ?></h4>
                </div>
                <form action="Carrito.php" method="post">
                    <button type="submit" class="btn btn-danger mt-4" name="clear_cart">Vaciar Carrito</button>
                    <input type="hidden" name="carrito_id" value="<?php echo $cartContents[0]['Carrito_ID']; ?>">
                </form>
                <div class="mt-4">
                <a href="create_payment.php?carrito_id=<?php echo $cartContents[0]['Carrito_ID']; ?>" class="btn btn-warning">Pagar con PayPal</a>
                <a href="Pago.php?carrito_id=<?php echo $cartContents[0]['Carrito_ID']; ?>" class="btn btn-warning">Pagar Sin PayPal</a>
                </div>
            <?php endif; ?>
        </section>
    </main>

    <?php include 'UI/footer.html'; ?>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
