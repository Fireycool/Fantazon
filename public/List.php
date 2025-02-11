<?php
session_start();
require_once 'Acceso.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: Login.php");
    exit();
}

function getListInfo($list_id) {
    try {
        $database = new AccessDB("localhost:3306", "root", "tETOTETO1", "fantazon");
        $database->connectarse();

        $stmt = $database->getConnection()->prepare("CALL GetListInfo(?)");
        $stmt->bind_param("i", $list_id);
        $stmt->execute();
        
        $result = $stmt->get_result();
        $list_info = $result->fetch_assoc(); 
        
        $stmt->close();
        $database->closeConnection();

        return $list_info;
    } catch (mysqli_sql_exception $e) {
        echo "Error: " . $e->getMessage();
    }
}

function getProductsInList($list_id) {
    try {
        $database = new AccessDB("localhost:3306", "root", "tETOTETO1", "fantazon");
        $database->connectarse();

        $stmt = $database->getConnection()->prepare("CALL GetProductoListas(?)");
        $stmt->bind_param("i", $list_id);
        $stmt->execute();
        
        $result = $stmt->get_result();
        $products = $result->fetch_all(MYSQLI_ASSOC);
        
        $stmt->close();
        $database->closeConnection();

        return $products;
    } catch (mysqli_sql_exception $e) {
        echo "Error: " . $e->getMessage();
    }
}

if (isset($_GET['list_id'])) {
    $list_id = $_GET['list_id'];

    $list_info = getListInfo($list_id);

    $products = getProductsInList($list_id);
} else {
    header("Location: Main_Page.php");
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

    <main class="container">
        <section class="contenido mt-4">
            <h2><?php echo $list_info['Nombre']; ?></h2>
            <p class="unfunky-text"><?php echo $list_info['Descripcion']; ?></p>
            <img src="data:image/jpeg;base64,<?php echo base64_encode($list_info['Foto']); ?>" class="img-fluid" alt="<?php echo $list_info['Nombre']; ?>" />
            <form action="DeleteLista.php" method="POST">
                <input type="hidden" name="listaID" value="<?php echo $list_id; ?>">
                <button type="submit" class="btn btn-danger mt-4">Eliminar Lista</button>
            </form>
        </section>

        <section class="contenido mt-4">
             <!-- PRODUCTOS DE LA LISTA -->
            <h3>Productos en la Lista</h3>
            <div class="card-deck-container">
                <?php foreach ($products as $product) : ?>
                <div class="card">
                    <a href="Producto.php?id=<?php echo $product['Producto_ID']; ?>" class="card-link">
                        <img src="data:image/jpeg;base64,<?php echo base64_encode($product['Foto_Principal']); ?>" class="card-img-top" alt="<?php echo $product['Nombre_Producto']; ?>" />
                        <div class="card-body">
                            <h3 class="card-title funky-text"><?php echo $product['Nombre_Producto']; ?></h3>
                            <p class="card-text"><?php echo $product['Descripcion_Producto']; ?></p>
                            <p class="card-text">Precio: $<?php echo $product['Costo_Base']; ?></p>
                            <p class="card-text">Cantidad Disponible: <?php echo $product['Cantidad']; ?></p>
                            <p class="card-text">Rating: <?php echo $product['Average_Rating']; ?></p>
                            <p class="card-text">Categoría: <?php echo $product['Categoria']; ?></p>
                            <p class="card-text">Vendedor: <?php echo $product['Vendedor']; ?></p>
                            <!-- Delete button for each product -->
                            <form action="DeleteProductoLista.php" method="POST">
                                <input type="hidden" name="listaID" value="<?php echo $list_id; ?>">
                                <input type="hidden" name="productoID" value="<?php echo $product['Producto_ID']; ?>">
                                <button type="submit" class="btn btn-danger">Eliminar</button>
                            </form>
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




