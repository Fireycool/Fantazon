<?php
session_start();
require_once 'Acceso.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: Login.html");
    exit();
}

$userid = $_SESSION['user_id'];

// Fetch user information from the database
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

$username = $user_info['Username'];
$fullName = $user_info['Nombre_Completo'];
$email = $user_info['Mail'];
$role = $user_info['Rol'];
$birthdate = $user_info['Fecha_Nac'];
$gender = $user_info['Sexo'];
$image = $user_info['Foto'];
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

    <main class="container-fluid">
      <section class="contenido mt-4">
        <div class="row justify-content-center">
          <div class="col-md-6">
            <h2 class="mb-4">Perfil de Usuario</h2>

            <div class="form-group">
              <label for="profileImage">Imagen de Perfil:</label><br>
              <?php
                if (!empty($image)) {
                    $imageData = base64_encode($image);
                    echo '<img src="data:image/jpeg;base64,'.$imageData.'" alt="User Profile" class="img-fluid" />';
                } else {
                    echo '<img src="Imagenes/helmet.jpg" alt="Default Profile" class="img-fluid" />';
                }
              ?>
            </div>

            <div class="form-group">
              <label for="username">Usuario:</label>
              <input type="text" class="form-control" id="username" value="<?php echo htmlspecialchars($username); ?>" readonly />
            </div>

            <div class="form-group">
              <label for="fullName">Nombre Completo:</label>
              <input type="text" class="form-control" id="fullName" value="<?php echo htmlspecialchars($fullName); ?>" readonly />
            </div>

            <div class="form-group">
              <label for="email">Correo Electrónico:</label>
              <input type="email" class="form-control" id="email" value="<?php echo htmlspecialchars($email); ?>" readonly />
            </div>

            <div class="form-group">
              <label for="role">Rol:</label>
              <input type="text" class="form-control" id="role" value="<?php echo htmlspecialchars($role); ?>" readonly />
            </div>

            <div class="form-group">
              <label for="birthdate">Fecha de Nacimiento:</label>
              <input type="text" class="form-control" id="birthdate" value="<?php echo htmlspecialchars($birthdate); ?>" readonly />
            </div>

            <div class="form-group">
              <label for="gender">Género:</label>
              <input type="text" class="form-control" id="gender" value="<?php echo htmlspecialchars($gender); ?>" readonly />
            </div>
            
            <a href="ModificarUsuario.php" class="btn btn-outline-light">Editar Perfil</a>
          </div>
        </div>
      </section>

      <?php if ($_SESSION['role'] !== 'Admin') : ?>
      <section class="contenido mt-4">
        <div class="form-group">
            <form action="DeleteUser.php" method="post">
                <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($userid); ?>">
                <button type="submit" class="btn btn-danger" onclick="return confirm('Seguro que desea borrar la cuenta?');">Borrar Cuenta</button>
            </form>
        </div>
      </section>
      <?php endif; ?>



    </main>

    <?php include 'UI/footer.html'; ?>

    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
  </body>
</html>
