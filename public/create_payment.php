<?php
session_start();
require_once 'Acceso.php';

require '../vendor/autoload.php';

$config = require('paypal_config.php');

$apiContext = new \PayPal\Rest\ApiContext(
    new \PayPal\Auth\OAuthTokenCredential(
        $config['client_id'],
        $config['secret']
    )
);

$apiContext->setConfig($config['settings']);

if (isset($_GET['carrito_id'])) {
    $cartId = $_GET['carrito_id'];
} else {
    echo 'Error: carrito_id is missing.';
    exit();
}

$userId = $_SESSION['user_id'];

$database = new AccessDB("localhost:3306", "root", "tETOTETO1", "fantazon");
$database->connectarse();
$conn = $database->getConnection();

$sql_get_cart = "CALL GetCarrito(?)";
$stmt = $conn->prepare($sql_get_cart);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();
$cartContents = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

$totalAmount = 0;
foreach ($cartContents as $item) {
    $totalAmount += $item['Precio'] * $item['Cantidad'];
}

// Create PayPal payment
$payer = new \PayPal\Api\Payer();
$payer->setPaymentMethod('paypal');

$amount = new \PayPal\Api\Amount();
$amount->setTotal($totalAmount);
$amount->setCurrency('USD');

$transaction = new \PayPal\Api\Transaction();
$transaction->setAmount($amount);
$transaction->setDescription('Payment for items in cart');

$redirectUrls = new \PayPal\Api\RedirectUrls();
$redirectUrls   ->setReturnUrl('http://localhost:8080/Fantazon/public/execute_payment.php?carrito_id=' . $cartId)
                ->setCancelUrl('http://localhost:8080/Fantazon/public/Carrito.php');

$payment = new \PayPal\Api\Payment();
$payment->setIntent('sale')
    ->setPayer($payer)
    ->setTransactions([$transaction])
    ->setRedirectUrls($redirectUrls);

try {
    $payment->create($apiContext);
    ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Pago con PayPal</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css"/>
    <link rel="stylesheet" href="Estilos/MainPage_Style.css"/>
</head>
<body>
<main class="container">
    <section class="contenido mt-4">
        <h2>Ejecutar Pago con Paypal?</h2>
        <div class="row">
            <div class="card">
                <div class="card-body">
                    <h3 class="card-title">Confirmar Pago</h3>
                    <p class="card-text">Total a pagar: $<?php echo number_format($totalAmount, 2); ?></p>
                    <p class="card-text">Productos:</p>
                    <ul>
                        <?php foreach ($cartContents as $item) : ?>
                            <li><?php echo $item['Producto'] . ' (x' . $item['Cantidad'] . '): $' . number_format($item['Precio'] * $item['Cantidad'], 2); ?></li>
                        <?php endforeach; ?>
                    </ul>
                    <a href="<?php echo $payment->getApprovalLink(); ?>" class="btn btn-warning">Pagar con PayPal</a>
                    <a href="<?php echo $redirectUrls->getCancelUrl(); ?>" class="btn btn-danger">Cancelar</a>
                </div>
            </div>
        </div>
    </section>
</main>
</body>
</html>

    <?php
} catch (Exception $ex) {
    echo $ex->getMessage();
}
?>
