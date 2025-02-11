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

$result_destacado = $conn->query("CALL GetDestacado()");
$row_destacado = $result_destacado->fetch_assoc();

$conn->next_result();

$numProducts = 5; 
$sql_populares = "CALL GetPopularProducts($numProducts)";
$result_populares = $conn->query($sql_populares);
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
    <section class="producto-destacado mt-4">
        <h2>Producto Destacado</h2>
        <?php if ($result_destacado && $result_destacado->num_rows > 0) : ?>
            <div class="card-deck">
                <div class="card">
                    <a href="Producto.php?id=<?php echo $row_destacado['Producto_ID']; ?>" class="card-link">
                        <img src="data:image/jpeg;base64,<?php echo base64_encode($row_destacado['Foto_1']); ?>" class="card-img-top" alt="Imagen del Producto" />
                        <div class="card-body">
                            <h3 class="card-title funky-text"><?php echo $row_destacado['Nombre']; ?></h3>
                            <p class="card-text-producto">Precio: $<?php echo $row_destacado['Costo_Base']; ?></p>
                            <p class="card-text-producto">Rating: <?php echo $row_destacado['Average_Rating']; ?></p>
                        </div>
                    </a>
                </div>
            </div>
        <?php else : ?>
            <p>No hay producto destacado disponible.</p>
        <?php endif; ?>
    </section>

    <section class="producto-listado mt-4">
        <h2>Populares</h2>
        <div class="card-deck-container">
            <?php if ($result_populares && $result_populares->num_rows > 0) : ?>
                <?php while ($row = $result_populares->fetch_assoc()) : ?>
                    <div class="card">
                        <a href="Producto.php?id=<?php echo $row['Producto_ID']; ?>" class="card-link">
                            <img src="data:image/jpeg;base64,<?php echo base64_encode($row["Foto_1"]); ?>" class="card-img-top" alt="Imagen del Producto" />
                            <div class="card-body">
                                <h3 class="card-title funky-text"><?php echo $row['Nombre']; ?></h3>
                                <p class="card-text-producto">Precio: $<?php echo $row['Costo_Base']; ?></p>
                                <p class="card-text-producto">Rating: <?php echo $row['Average_Rating']; ?></p>
                            </div>
                        </a>
                    </div>
                <?php endwhile; ?>
            <?php else : ?>
                <p>No hay productos populares disponibles.</p>
            <?php endif; ?>
        </div>
    </section>
</main>

<?php include 'UI/footer.html'; ?>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
?>