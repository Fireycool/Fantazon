<?php
session_start();
require_once 'Acceso.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: Login.php");
    exit();
}

if (isset($_POST['listaID']) && isset($_POST['productoID'])) {
    $listaID = $_POST['listaID'];
    $productoID = $_POST['productoID'];

    try {
        $database = new AccessDB("localhost:3306", "root", "tETOTETO1", "fantazon");
        $database->connectarse();

        $stmt = $database->getConnection()->prepare("CALL DeleteProdLista(?, ?)");
        $stmt->bind_param("ii", $listaID, $productoID);
        $stmt->execute();

        $stmt->close();
        $database->closeConnection();

        header("Location: List.php?list_id=" . $listaID);
        exit();
    } catch (mysqli_sql_exception $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    header("Location: Main_Page.php");
    exit();
}
?>
