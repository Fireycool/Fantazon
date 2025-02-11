<?php
require_once 'Acceso.php';

// Retrieve the search term and category ID from the form submission
$searchTerm = $_GET['searchTerm'] ?? '';
$categoryID = $_GET['category'] ?? '';

$categoryID = $categoryID === '' ? 'NULL' : intval($categoryID);

// Call the stored procedure with the search term and category ID
$database = new AccessDB("localhost:3306", "root", "tETOTETO1", "fantazon");
$database->connectarse();

$searchTermEscaped = $database->getConnection()->real_escape_string($searchTerm);
$sql = "CALL GetProductoBuscar('$searchTermEscaped', $categoryID)";
$result = $database->getConnection()->query($sql);

$productos = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $productos[] = $row;
    }
}

$database->closeConnection();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultados de Búsqueda</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="Estilos/MainPage_Style.css">
</head>
<body>

<?php include 'UI/header.php'; ?>
<?php include 'UI/navegacion.php'; ?>

<main class="container">
    <section class="producto-listado mt-4">
        <h2>Resultados de Búsqueda</h2>

        <div class="card-comment-container">
            <?php foreach ($productos as $producto): ?>
                <div class="card">
                    <a href="Producto.php?id=<?php echo $producto['Producto_ID']; ?>" class="card-link">
                        <img src="data:image/jpeg;base64,<?php echo base64_encode($producto['Foto']); ?>" class="img-fluid" alt="<?php echo $producto['Producto']; ?>" />
                        <div class="card-body">
                            <h3 class="card-title funky-text"><?php echo $producto['Producto']; ?></h3>
                            <p class="card-text"><?php echo $producto['Descripcion']; ?></p>
                            <p class="card-text">Precio: $<?php echo $producto['Precio']; ?></p>
                            <p class="card-text">Cantidad Disponible: <?php echo $producto['Cantidad']; ?></p>
                            <p class="card-text">Rating: <?php echo $producto['Average_Rating']; ?></p>
                            <p class="card-text">Categoría: <?php echo $producto['Categoria']; ?></p>
                            <p class="card-text">Vendedor: <?php echo $producto['Vendedor']; ?></p>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
</main>

<?php include 'UI/footer.html'; ?>

<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
