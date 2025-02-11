<?php
session_start();
require_once 'Acceso.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: Login.html");
    exit();
}

$userid = $_SESSION['user_id'];

$database = new AccessDB("localhost:3306", "root", "tETOTETO1", "fantazon");
$database->connectarse();

$sql_get_user_info = "CALL GetAllInUser(?)";
$stmt_get_user_info = $database->getConnection()->prepare($sql_get_user_info);
$stmt_get_user_info->bind_param("i", $userid);
$stmt_get_user_info->execute();
$result_get_user_info = $stmt_get_user_info->get_result();
$user_info = $result_get_user_info->fetch_assoc();

$stmt_get_user_info->close();
$database->closeConnection();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $database->connectarse();

    $username = $_POST['username'];
    $email = $_POST['email'];
    $fullName = $_POST['fullName'];
    $password = $_POST['password'];
    $role = $_POST['role'];
    if (isset($_FILES['image']) && $_FILES['image']['size'] > 0) {
        $image = file_get_contents($_FILES['image']['tmp_name']);
    } else {
        $image = $user_info['Foto'];
    }
    $birthdate = $_POST['birthdate'];
    $gender = $_POST['gender'];

    // Checar si Username existe a parte del propio 
    $sql_verify_username = "CALL CheckUsernameExistsModif(?, ?)";
    $stmt_verify_username = $database->getConnection()->prepare($sql_verify_username);
    $stmt_verify_username->bind_param("is", $userid, $username);
    $stmt_verify_username->execute();
    $result_verify_username = $stmt_verify_username->get_result();
    $row_verify_username = $result_verify_username->fetch_assoc();
    $usernameExists = $row_verify_username['username_exists'];
    $stmt_verify_username->close();

    // Checar si Correo existe a parte del propio   
    $sql_verify_email = "CALL CheckEmailExistsModif(?, ?)";
    $stmt_verify_email = $database->getConnection()->prepare($sql_verify_email);
    $stmt_verify_email->bind_param("is", $userid, $email);
    $stmt_verify_email->execute();
    $result_verify_email = $stmt_verify_email->get_result();
    $row_verify_email = $result_verify_email->fetch_assoc();
    $emailExists = $row_verify_email['email_exists'];
    $stmt_verify_email->close();

    if ($usernameExists) {
        echo "<script>alert('El nombre de usuario ya está en uso.');</script>";
    } elseif ($emailExists) {
        echo "<script>alert('El correo electrónico ya está en uso.');</script>";
    } else {
        $sql = "CALL ModifyUser(?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $database->getConnection()->prepare($sql);
        $stmt->bind_param("issssssss", $userid, $username, $fullName, $email, $password, $gender, $role, $birthdate, $image);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            echo "<script>alert('¡Información actualizada exitosamente!');</script>";
            header("Location: Perfil.php");
            exit();
        } else {
            echo "<script>alert('¡La actualización falló!');</script>";
        }

        $stmt->close();
    }

    $database->closeConnection();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Modificar Información de Usuario</title>
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
                    <h2 class="mb-4">Modificar Información</h2>

                    <form id="modificationForm" action="ModificarUsuario.php" method="post" onsubmit="return validateForm()" enctype="multipart/form-data">
                        <div class="form-group">
                            <label for="username">Usuario:</label>
                            <input type="text" class="form-control" id="username" name="username" placeholder="Ingrese su nombre de Usuario" value="<?php echo htmlspecialchars($user_info['Username']); ?>" />
                        </div>
                        <div class="form-group">
                            <label for="fullName">Nombre Completo:</label>
                            <input type="text" class="form-control" id="fullName" name="fullName" placeholder="Ingrese su Nombre Completo" value="<?php echo htmlspecialchars($user_info['Nombre_Completo']); ?>" />
                        </div>
                        <div class="form-group">
                            <label for="email">Correo Electrónico:</label>
                            <input type="email" class="form-control" id="email" name="email" placeholder="Ingrese su Correo Electrónico" value="<?php echo htmlspecialchars($user_info['Mail']); ?>" />
                        </div>
                        <div class="form-group">
                            <label for="password">Contraseña:</label>
                            <input type="password" class="form-control" id="password" name="password" placeholder="Ingrese su contraseña" value="<?php echo htmlspecialchars($user_info['Contraseña']); ?>" />
                        </div>
                        <div class="form-group">
                            <label for="confirmPassword">Confirmar Contraseña:</label>
                            <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" placeholder="Confirme su contraseña" value="<?php echo htmlspecialchars($user_info['Contraseña']); ?>" />
                        </div>
                        <div class="form-group">
                            <label for="role">Rol:</label>
                            <select class="form-control" id="role" name="role">
                                <option value="Comprador" <?php if ($user_info['Rol'] == 'Comprador') echo 'selected'; ?>>Comprador</option>
                                <option value="Vendedor" <?php if ($user_info['Rol'] == 'Vendedor') echo 'selected'; ?>>Vendedor</option>
                                <option value="Admin" <?php if ($user_info['Rol'] == 'Admin') echo 'selected'; ?>>Admin</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="image">Imagen:</label>
                            <input type="file" class="form-control-file" id="image" name="image" />
                        </div>
                        <div class="form-group">
                            <label for="birthdate">Fecha de Nacimiento:</label>
                            <input type="date" class="form-control" id="birthdate" name="birthdate" value="<?php echo htmlspecialchars($user_info['Fecha_Nac']); ?>" />
                        </div>
                        <div class="form-group">
                            <label for="gender">Género:</label>
                            <select class="form-control" id="gender" name="gender">
                                <option value="Masculino" <?php if ($user_info['Sexo'] == 'Masculino') echo 'selected'; ?>>Masculino</option>
                                <option value="Femenino" <?php if ($user_info['Sexo'] == 'Femenino') echo 'selected'; ?>>Femenino</option>
                                <option value="Otro" <?php if ($user_info['Sexo'] == 'Otro') echo 'selected'; ?>>Otro</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-outline-light">Actualizar</button>
                    </form>
                </div>
            </div>
        </section>
    </main>

    <?php include 'UI/footer.html'; ?>

    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="Scripts/valid_modif.js"></script>
</body>
</html>
