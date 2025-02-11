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

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['userId'])) {
    $database = new AccessDB("localhost:3306", "root", "tETOTETO1", "fantazon");
    $database->connectarse();
    $conn = $database->getConnection();

    $userId = $_POST['userId'];

    $sql = "CALL DeleteUser(?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        $response['success'] = true;
        $response['message'] = 'Usuario Borrado con Éxito.';
    } else {
        $response['success'] = false;
        $response['message'] = 'No se logró borrar el usuario.';
    }

    $stmt->close();
    $database->closeConnection();

    respondAndExit($response);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['reactivateUserId'])) {
    $database = new AccessDB("localhost:3306", "root", "tETOTETO1", "fantazon");
    $database->connectarse();
    $conn = $database->getConnection();

    $userId = $_POST['reactivateUserId'];

    $sql = "CALL ReactivateUser(?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $userId);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        $response['success'] = true;
        $response['message'] = 'Usuario Reactivado con Éxito.';
    } else {
        $response['success'] = false;
        $response['message'] = 'No se logró reactivar el usuario.';
    }

    $stmt->close();
    $database->closeConnection();

    respondAndExit($response);
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
        <h2>Usuarios Totales</h2>

        <div class="card-comment-container">
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

            $sql = "CALL GetUserInfo()"; 
            $result = $conn->query($sql);
            
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo '<div class="card row">';
                    if (!empty($row["Foto"])) {
                        $photoData = base64_encode($row["Foto"]);
                        echo '<img src="data:image/jpeg;base64,' . $photoData . '" class="card-img-fluid" alt="User Image" />';
                    } else {
                        echo '<img src="Imagenes/helmet.jpg" class="card-img-fluid" alt="User Image" />';
                    }
                    echo '<div class="card-body">';
                    echo '<h3 class="card-title">Nombre de Usuario: ' . $row["Username"] . '</h3>';
                    echo '<p class="card-text-producto">Nombre Completo: ' . $row["Nombre_Completo"] . '</p>';
                    echo '<p class="card-text-producto">Correo Electronico: ' . $row["Mail"] . '</p>';
                    echo '<p class="card-text-producto">Genero: ' . $row["Sexo"] . '</p>';
                    echo '<p class="card-text-producto">Rol: ' . $row["Rol"] . '</p>';
                    echo '<p class="card-text-producto">Fecha de Nacimiento: ' . $row["Fecha_Nac"] . '</p>';
                    echo '<p class="card-text-producto">Tiempo de Ingreso: ' . $row["Fecha_Ing"] . '</p>';
                    echo '<p class="card-text-producto">Estatus: ' . $row["Estatus"] . '</p>';
                    if ($row["Estatus"] == "Inactivo") {
                        echo '<button class="btn btn-outline-warning" onclick="reactivateUser(' . $row["Usuario_ID"] . ')">Reactivar</button>';
                    }
                    if ($row["Rol"] !== "Admin" && $row["Estatus"] == "Activo") {
                        echo '<button class="btn btn-danger" onclick="deleteUser(' . $row["Usuario_ID"] . ')">Eliminar</button>';
                    }
                    echo '</div>';
                    echo '</div>';
                }
            } else {
                echo "No Existen Usuarios";
            }

            $database->closeConnection();
            ?>
        </div>
    </section>
</main>

<?php include 'UI/footer.html'; ?>

<script>
    function deleteUser(userId) {
        fetch('UserList.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: 'userId=' + userId
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

    function reactivateUser(userId) {
        fetch('UserList.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded'
            },
            body: 'reactivateUserId=' + userId
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
