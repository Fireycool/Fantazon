<?php
session_start();
require_once 'Acceso.php';

if ($_SESSION['role'] !== 'Admin') {
    header("Location: Main_Page.php");
    exit();
}

function respondAndExit($response) {
    header("Content-type: application/json");
    echo json_encode($response);
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['productId'])) {
    $database = new AccessDB("localhost:3306", "root", "tETOTETO1", "fantazon");
    $database->connectarse();
    $conn = $database->getConnection();

    $productId = $_POST['productId'];

    $sql = "CALL AprovarProducto(?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $productId);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        $response['success'] = true;
        $response['message'] = 'Producto Aprobado con Éxito.';
    } else {
        $response['success'] = false;
        $response['message'] = 'No se logró aprobar el producto.';
    }

    $stmt->close();
    $database->closeConnection();

    header("Content-type: application/json"); 
    echo json_encode($response); 
    exit(); 
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
        <h2>Productos por Aprobar</h2>

        <div class="card-comment-container mt-1">
            <?php
            require_once 'Acceso.php';
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }
            error_reporting(E_ALL);
            ini_set('display_errors', 1);

            if (!isset($_SESSION['username'])) {
                header("Location: Login.php");
                exit();
            }

            $database = new AccessDB("localhost:3306", "root", "tETOTETO1", "fantazon");
            $database->connectarse();
            $conn = $database->getConnection();

            $sql = "CALL GetPorAprovar()";
            $result = $conn->query($sql);

            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo '<div class="card row">';                   
                    echo '<img src="data:image/jpeg;base64,' . base64_encode($row["Imagen"]) . '" class="card-img-fluid" alt="Imagen del Producto" />';                                       
                    echo '<div class="card-body">';
                    echo '<h3 class="card-title">Producto: ' . $row["Producto"] . '</h3>';
                    echo '<p class="card-text-producto">Descripción: ' . $row["Descripcion"] . '</p>';
                    echo '<p class="card-text-producto">Costo Base: ' . $row["Costo_Base"] . '</p>';
                    echo '<p class="card-text-producto">Cantidad: ' . $row["Cantidad"] . '</p>';
                    echo '<p class="card-text-producto">Usuario: ' . $row["Usuario"] . '</p>';
                    echo '<button class="btn btn-success" onclick="approveProduct(' . $row["Producto_ID"] . ')">Aprobar</button>';
                    echo '</div>';
                    echo '</div>';
                }
            } else {
                echo "No hay productos por aprobar.";
            }

            $database->closeConnection();
            ?>
        </div>
    </section>
</main>

<?php include 'UI/footer.html'; ?>

<script>
function approveProduct(productId) {
    fetch('Aprovaciones.php', { 
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded'
        },
        body: 'productId=' + productId
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert(data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
    });
}

</script>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>