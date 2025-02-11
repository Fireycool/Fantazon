<?php
require_once 'Acceso.php';
session_start();

require '../vendor/autoload.php'; 

$config = require('paypal_config.php');

$apiContext = new \PayPal\Rest\ApiContext(
    new \PayPal\Auth\OAuthTokenCredential(
        $config['client_id'],
        $config['secret']
    )
);

$apiContext->setConfig($config['settings']);

if (isset($_GET['paymentId']) && isset($_GET['PayerID']) && isset($_GET['carrito_id'])) {
    $paymentId = $_GET['paymentId'];
    $payerId = $_GET['PayerID'];
    $carrito_id = $_GET['carrito_id'];

    $payment = \PayPal\Api\Payment::get($paymentId, $apiContext);
    $execution = new \PayPal\Api\PaymentExecution();
    $execution->setPayerId($payerId);
    try {
        $result = $payment->execute($execution, $apiContext);

        $database = new AccessDB("localhost:3306", "root", "tETOTETO1", "fantazon");
        $database->connectarse();
        $conn = $database->getConnection();

        $sql_call_procedure = "CALL RealizarVenta(?)";
        $stmt = $conn->prepare($sql_call_procedure);
        $stmt->bind_param("i", $carrito_id);
        $stmt->execute();
        $stmt->close();



        header("Location: Carrito.php");
        echo "<script>alert('Payment was successful!');</script>";
        exit;
    } catch (Exception $ex) {
        echo 'Payment failed! Error: ' . $ex->getMessage();
    }
} else {
    echo 'Payment failed! PaymentId, PayerID, or carrito_id missing.';
}
?>
