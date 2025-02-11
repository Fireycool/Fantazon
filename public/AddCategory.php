<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: Login.html");
    exit();
}

require_once 'Acceso.php';

$database = new AccessDB("localhost:3306", "root", "tETOTETO1", "fantazon");
$database->connectarse();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nombre = $_POST['nombre'];
    $usuarioCreadorFK = $_SESSION['user_id'];

    $sql_add_category = "CALL AddCategory(?, ?)";
    $stmt_add_category = $database->getConnection()->prepare($sql_add_category);
    $stmt_add_category->bind_param("si", $nombre, $usuarioCreadorFK);
    $stmt_add_category->execute();

    if ($stmt_add_category->affected_rows > 0) {
        echo "<script>alert('¡Categoría añadida exitosamente!');</script>";
    } else {
        echo "<script>alert('¡Error al añadir la categoría!');</script>";
    }

    $stmt_add_category->close();
}

$sql_get_categories = "CALL GetCategories()";
$result_get_categories = $database->getConnection()->query($sql_get_categories);

$categories = [];
if ($result_get_categories) {
    while ($row = $result_get_categories->fetch_assoc()) {
        $categories[] = $row;
    }
    $result_get_categories->close();
}

$database->closeConnection();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Crear Categoría</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" />
    <link rel="stylesheet" href="Estilos/MainPage_Style.css" />
</head>
<body>
    <?php include 'UI/header.php'; ?>
    <?php include 'UI/navegacion.php'; ?>

    <main class="container-fluid">
        <section class="registration mt-4">
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <h2 class="mb-4">Crear Nueva Categoría</h2>

                    <form id="categoryForm" action="AddCategory.php" method="post" onsubmit="return validateForm()">
                        <div class="form-group">
                            <label for="nombre">Nombre de Categoría:</label>
                            <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Ingrese el nombre de la categoría" required />
                        </div>
                        <button type="submit" class="btn btn-outline-light">Crear Categoría</button>
                    </form>
                </div>
            </div>
        </section>

        <section class="producto mt-4">
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <h2 class="mb-4">Categorías Existentes</h2>
                    <ul class="list-group">
                        <?php foreach ($categories as $category) { ?>
                            <li class="list-group-item funky-text">
                                <?php echo htmlspecialchars($category['Nombre']); ?> - Creado por: <?php echo htmlspecialchars($category['Usuario_Creador']); ?>
                                <form action="DeleteCategory.php" method="post" class="d-inline">
                                    <input type="hidden" name="categoria_id" value="<?php echo $category['Categoria_ID']; ?>">
                                    <button type="submit" class="btn btn-danger btn-sm ml-2">Eliminar</button>
                                </form>
                            </li>
                        <?php } ?>
                    </ul>
                </div>
            </div>
        </section>
    </main>

    <?php include 'UI/footer.html'; ?>

    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
