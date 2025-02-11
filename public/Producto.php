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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_POST['producto_id'])) {
        echo "Producto no existe.";
        exit();
    }
    
    $productID = intval($_POST['producto_id']);
} else if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (!isset($_GET['id'])) {
        echo "Producto no existe.";
        exit();
    }
    
    $productID = intval($_GET['id']);
} else {
    echo "Algo malo paso aqui.";
    exit();
}


// Funcion para generar Estrellas
function generateStars($rating) {
    $stars = '';
    for ($i = 1; $i <= $rating; $i++) {
        $stars .= '⭐'; 
    }
    return $stars;
}


$sql_product = "CALL GetProductDetails($productID)";
$result_product = $conn->query($sql_product);
$product = $result_product->fetch_assoc();

$conn->next_result();

// Logica para agregar comentario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['texto']) && isset($_POST['rating'])) {
        $texto = $_POST['texto'];
        $rating = intval($_POST['rating']);
        
        $sql_add_comment = "CALL AddComment(?, ?, ?, ?)";
        $stmt = $conn->prepare($sql_add_comment);
        $stmt->bind_param("siii", $texto, $rating, $productID, $userId);
        $stmt->execute();
    }
}

function getListasByUser($user_id) {
    try {
        $database = new AccessDB("localhost:3306", "root", "tETOTETO1", "fantazon");
        $database->connectarse();

        $stmt = $database->getConnection()->prepare("CALL GetListas(?)");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        
        $result = $stmt->get_result();
        $lists = $result->fetch_all(MYSQLI_ASSOC);
        
        $stmt->close();
        $database->closeConnection();

        return $lists;
    } catch (mysqli_sql_exception $e) {
        echo "Error: " . $e->getMessage();
    }
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

    <!-- Listas del Usuario -->
    <section class="producto mt-4">
        <h2 class="funky-text">Mis Listas</h2>    
        <div class="card-deck-container">
            <?php
            $user_id = $_SESSION['user_id'];
            $lists = getListasByUser($user_id);
            foreach ($lists as $list) {
                echo "<div class='card'>";
                echo "<a href='List.php?list_id={$list['Listas_ID']}' class='card-link'>";
                echo "<img src='data:image/jpeg;base64," . base64_encode($list['Foto']) . "' class='card-img-top' alt='{$list['Nombre']}' />";
                echo "<div class='card-body'>";
                echo "<h5 class='card-title funky-text'>{$list['Nombre']}</h5>";
                echo "<p class='card-text'>{$list['Descripcion']}</p>";
                echo "</div>";
                echo "</a>";

                echo "<form action='AgregarProductoLista.php' method='post'>";
                echo "<input type='hidden' name='list_id' value='{$list['Listas_ID']}' />";
                echo "<input type='hidden' name='product_id' value='$productID' />";
                echo "<button type='submit' class='btn btn-outline-warning'>Agregar a Lista</button>";
                echo "</form>";

                echo "</div>";
            }
            ?>
        </div>
    </section>


    <!-- Info del Producto -->
    <section class="producto mt-4">
        <?php if ($product) : ?>
            <div class="row">
                <h1 class="col-md-9"><?php echo htmlspecialchars($product['Nombre']); ?></h1>
                <div class="card col-md-2 text-right"><h3>$<?php echo htmlspecialchars($product['Costo_Base']); ?></h3></div>
            </div>
            <h5 class="col-md-3">Rating: <?php echo generateStars($product['Average_Rating']); ?> <?php echo htmlspecialchars($product['Average_Rating']); ?></h5>
            <div class="row">
                <h5 class="funky-text col-md-9">Categoría: <?php echo htmlspecialchars($product['Categoria']); ?></h5>
                <h5 class="funky-text col-md-2 text-right">Para: Venta</h5>
            </div>
            <p><?php echo htmlspecialchars($product['Descripcion']); ?></p>
            <div class="row">
                <h5 class="funky-text col-md-9">Vendedor: <?php echo htmlspecialchars($product['Vendedor']); ?></h5>
            </div>
            <?php if ($userId != $product['Vendedor_ID']) : ?>
                <form action="AddCarrito.php" method="post">
                    <input type="hidden" name="product_id" value="<?php echo $product['Producto_ID']; ?>">
                    <button type="submit" class="btn btn-outline-warning">Agregar a la Carreta</button>
                </form>
                <form action="ChatStart.php" method="post">
                    <input type="hidden" name="product_id" value="<?php echo $product['Producto_ID']; ?>">
                    <button type="submit" class="btn btn-outline-light">Regatear</button>
                </form>
            <?php else: ?>
                <a href="ModifProducto.php?product_id=<?php echo $product['Producto_ID']; ?>" class="btn btn-outline-info">Editar</a>
            <?php endif; ?>

            <!-- Imagenes y video -->
            <div class="card-deck-container">
                <?php if (!empty($product['Video'])): ?>
                    <video controls class="video" alt="Product Video">
                        <source src="data:video/mp4;base64,<?php echo base64_encode($product['Video']); ?>" type="video/mp4">
                        Browser no funciona con videos.
                    </video>
                <?php endif; ?>
                <img src="data:image/jpeg;base64,<?php echo base64_encode($product['Foto_1']); ?>" class="img-fluid" alt="Product Image 1" />
                <img src="data:image/jpeg;base64,<?php echo base64_encode($product['Foto_2']); ?>" class="img-fluid" alt="Product Image 2" />
                <img src="data:image/jpeg;base64,<?php echo base64_encode($product['Foto_3']); ?>" class="img-fluid" alt="Product Image 3" />
            </div>
            <div class="mt-4">
                <h5 class="funky-text text-right">Cantidad Disponible: <?php echo htmlspecialchars($product['Cantidad']); ?></h5>
            </div>
        <?php else : ?>
            <p>No se encontró el producto.</p>
        <?php endif; ?>
    </section>

    <!-- Agregar Comentarios -->



    <section class="producto mt-4">
        <?php if ($userId != $product['Vendedor_ID']) : ?>

            <form id="commentForm" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
                <input type="hidden" name="producto_id" value="<?php echo $productID; ?>">
                <div class="form-group">
                    <label for="texto">Comentario:</label>
                    <textarea class="form-control" id="texto" name="texto" rows="4" cols="50"></textarea>
                    <div id="textoError" class="text-danger"></div>
                </div>
                <div class="form-group">
                    <label for="rating">Rating:</label>
                    <input type="number" class="form-control" id="rating" name="rating" min="1" max="5" onkeydown="return false">
                    <div id="ratingError" class="text-danger"></div>
                </div>
                <button type="submit" class="btn btn-primary">Agregar Comentario</button>
            </form>
        <?php endif; ?>
        <?php include 'Comentarios.php'; ?>
    </section>
</main>

<?php include 'UI/footer.html'; ?>


<script>
    document.getElementById('commentForm').addEventListener('submit', function(event) {
        document.getElementById('textoError').innerText = '';
        document.getElementById('ratingError').innerText = '';

        var texto = document.getElementById('texto').value.trim();
        var rating = parseInt(document.getElementById('rating').value);

        if (texto.length === 0) {
            document.getElementById('textoError').innerText = 'Por favor, ingresa un comentario.';
            event.preventDefault();
        } else if (texto.length > 1000) {
            document.getElementById('textoError').innerText = 'El comentario no puede tener más de 1000 caracteres.';
            event.preventDefault();
        }

        if (isNaN(rating) || rating < 1 || rating > 5) {
            document.getElementById('ratingError').innerText = 'El rating debe ser un número entre 1 y 5.';
            event.preventDefault();
        }
    });
</script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>

