<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: Login.html");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['categoria_id'])) {
    require_once 'Acceso.php';

    $database = new AccessDB("localhost:3306", "root", "tETOTETO1", "fantazon");
    $database->connectarse();

    $categoria_id = $_POST['categoria_id'];

    $sql_delete_category = "CALL DeleteCategory(?)";
    $stmt_delete_category = $database->getConnection()->prepare($sql_delete_category);
    $stmt_delete_category->bind_param("i", $categoria_id);
    $stmt_delete_category->execute();

    if ($stmt_delete_category->affected_rows > 0) {
        echo "<script>alert('¡Categoría eliminada exitosamente!');</script>";
    } else {
        echo "<script>alert('¡Error al eliminar la categoría!');</script>";
    }

    $stmt_delete_category->close();
    $database->closeConnection();
}

header("Location: AddCategory.php");
exit();
?>
