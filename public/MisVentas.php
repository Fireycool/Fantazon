<?php
require_once 'Acceso.php';
session_start();

if ($_SESSION['role'] == 'Comprador') {
    header("Location: Main_Page.php");
    exit();
}

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

$sql = "CALL GetMisVentasAgrupadas(?)"; 
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $userId);
$stmt->execute();
$result = $stmt->get_result();

// Calculate total earnings
$totalEarnings = 0;
while ($row = $result->fetch_assoc()) {
    $totalEarnings += $row["Total_Ganancias"];
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Fantazon</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css"/>
    <link rel="stylesheet" href="Estilos/MainPage_Style.css"/>
</head>
<body>
<?php include 'UI/header.php'; ?>
<?php include 'UI/navegacion.php'; ?>

<main class="container">
    <section class="producto-listado mt-4">
        <h2>Tus Ventas</h2>
        <h3 class="unfunky-text">Ganancias Totales: $<?php echo number_format($totalEarnings, 2); ?></h3>
        <div class="card-comment-container">
            <?php
            $result->data_seek(0);
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo '<div class="card row">';
                    echo '<img src="data:image/jpeg;base64,' . base64_encode($row["Foto_1"]) . '" class="img-fluid" alt="Imagen del Producto" />';
                    echo '<div class="card-body">';
                    echo '<h3 class="card-title funky-text">Producto: ' . $row["Producto_Nombre"] . '</h3>';
                    echo '<p class="card-text-producto">Precio de articulo Individual: $' . $row["Precio"] . '</p>';
                    echo '<p class="card-text-producto">Cantidad Vendida: ' . $row["Cantidad_Vendida"] . '</p>';
                    echo '<p class="card-text-producto">Comprador: ' . $row["Comprador_Username"] . '</p>';
                    echo '<p class="card-text-producto">Fecha de Venta: ' . $row["Fecha"] . '</p>';
                    echo '<h3 class="card-text-producto funky-text">Total Ganancias: $' . $row["Total_Ganancias"] . '</h3>';
                    echo '</div>';                    
                    echo '</div>';
                }
            } else {
                echo "No tienes ventas.";
            }

            $stmt->close();
            $database->closeConnection();
            ?>
        </div>
    </section>
</main>

<?php include 'UI/footer.html'; ?>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
