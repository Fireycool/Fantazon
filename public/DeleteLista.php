<?php
session_start();
require_once 'Acceso.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: Login.php");
    exit();
}

if (isset($_POST['listaID'])) {
    $listaID = $_POST['listaID'];

    try {
        $database = new AccessDB("localhost:3306", "root", "tETOTETO1", "fantazon");
        $database->connectarse();

        $stmt = $database->getConnection()->prepare("CALL DeleteLista(?)");
        $stmt->bind_param("i", $listaID);
        $stmt->execute();

        $stmt->close();
        $database->closeConnection();

        header("Location: Listas.php");
        exit();
    } catch (mysqli_sql_exception $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    header("Location: Main_Page.php");
    exit();
}
?>
