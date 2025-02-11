<?php
require_once 'Acceso.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['productoID'])) {
    $productoID = $_POST['productoID'];
    
    try {
        $database = new AccessDB("localhost:3306", "root", "tETOTETO1", "fantazon");
        $database->connectarse();

        $sql = "CALL RecoverProducto(?)";
        $stmt = $database->getConnection()->prepare($sql);
        $stmt->bind_param("i", $productoID);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            echo "<script>alert('Producto recuperado exitosamente.');</script>";
        } else {
            echo "<script>alert('Falló la recuperación del producto.');</script>";
        }

        $stmt->close();
        $database->closeConnection();
    } catch (mysqli_sql_exception $e) {
        echo "Error: " . $e->getMessage();
    }
    
    header("Location: MisProductos.php");
    exit();
}
?>
