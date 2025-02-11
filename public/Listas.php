<?php
session_start();
require_once 'Acceso.php';

if (!isset($_SESSION['user_id'])) {
  header("Location: Login.php");
  exit();
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

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_SESSION['username'])) {
        echo "<script>alert('¡Debe iniciar sesión para crear una lista!');</script>";
        exit();
    }

    try {
        $usuario_FK = $_SESSION['user_id'];

        $database = new AccessDB("localhost:3306", "root", "tETOTETO1", "fantazon");
        $database->connectarse();

        $nombre = $_POST['listName'];
        $descripcion = $_POST['listDescription'];
        $foto = file_get_contents($_FILES['listImage']['tmp_name']); 

        $sql = "CALL InsertLista(?, ?, ?, ?)";
        $stmt = $database->getConnection()->prepare($sql);
        $stmt->bind_param("sssi", $nombre, $descripcion, $foto, $usuario_FK);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            echo "<script>alert('¡Lista creada con éxito!');</script>";
        } else {
            echo "<script>alert('¡Error al crear la lista!');</script>";
        }

        $stmt->close();
        $database->closeConnection();
    } catch (mysqli_sql_exception $e) {
        echo "Error: " . $e->getMessage();
    }
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
            <h2>Crear Lista</h2>
            <form id="listCreationForm" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="listName">Nombre de la Lista:</label>
                    <input type="text" class="form-control" id="listName" name="listName" placeholder="Ingrese el nombre de la lista" />
                    <div id="listNameError" class="text-danger"></div>
                </div>

                <div class="form-group">
                    <label for="listDescription">Descripción de la Lista:</label>
                    <textarea class="form-control" id="listDescription" name="listDescription" placeholder="Escriba una descripción de la lista"></textarea>
                    <div id="listDescriptionError" class="text-danger"></div>
                </div>

                <div class="form-group">
                    <label for="listImage">Imagen de la Lista (opcional):</label>
                    <input type="file" class="form-control-file" id="listImage" name="listImage" />
                    <div id="listImageError" class="text-danger"></div>
                </div>

                <button type="submit" class="btn btn-outline-light">Crear Lista</button>
            </form>
        </section>

        <!-- Listas del Usuario -->
        <section class="contenido mt-4">
            <h2>Mis Listas</h2>
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
                    echo "</div>";
                }
                ?>
            </div>
        </section>

    </main>
    
        <?php include 'UI/footer.html'; ?>
    
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
        
        <script>
                document.addEventListener('DOMContentLoaded', function() {
                document.getElementById('listCreationForm').addEventListener('submit', function(event) {
                    if (!validateForm()) {
                        event.preventDefault();
                    }
                });
            });

            function validateForm() {
                var listName = document.getElementById('listName').value.trim();
                var listDescription = document.getElementById('listDescription').value.trim();
                var listImage = document.getElementById('listImage').files[0];

                var valid = true;

                if (!listName || listName.length > 100) {
                    document.getElementById('listNameError').innerText = 'El nombre de la lista es obligatorio y no debe exceder los 100 caracteres.';
                    valid = false;
                } else {
                    document.getElementById('listNameError').innerText = '';
                }

                if (!listDescription) {
                    document.getElementById('listDescriptionError').innerText = 'La descripción de la lista es obligatoria.';
                    valid = false;
                } else {
                    document.getElementById('listDescriptionError').innerText = '';
                }

                if (!listImage) {
                    document.getElementById('listImageError').innerText = 'Debe cargar una imagen para la lista.';
                    valid = false;
                } else if (listImage.size > 2 * 1024 * 1024) {
                    document.getElementById('listImageError').innerText = 'La imagen de la lista debe ser menor a 2MB.';
                    valid = false;
                } else {
                    document.getElementById('listImageError').innerText = '';
                }

                return valid;
            }

        </script>

    </body>
    </html>
    