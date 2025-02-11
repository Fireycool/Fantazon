<?php
require_once 'Acceso.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $database = new AccessDB("localhost:3306", "root", "tETOTETO1", "fantazon");
    $database->connectarse();

    $username = $_POST['username'];
    $email = $_POST['email'];

    // Checar si Username existe
    $sql_check_username = "CALL CheckUsernameExists(?)";
    $stmt_check_username = $database->getConnection()->prepare($sql_check_username);
    $stmt_check_username->bind_param("s", $username);
    $stmt_check_username->execute();
    $result_check_username = $stmt_check_username->get_result();
    $row_check_username = $result_check_username->fetch_assoc();
    $username_exists = $row_check_username['username_exists'];

    $result_check_username->close();
    $stmt_check_username->close();

    // Checar si correo existe
    $sql_check_email = "CALL CheckEmailExists(?)";
    $stmt_check_email = $database->getConnection()->prepare($sql_check_email);
    $stmt_check_email->bind_param("s", $email);
    $stmt_check_email->execute();
    $result_check_email = $stmt_check_email->get_result();
    $row_check_email = $result_check_email->fetch_assoc();
    $email_exists = $row_check_email['email_exists'];

    $result_check_email->close();
    $stmt_check_email->close();


    if ($username_exists) {
        // Nombre de usuario existe, Adios
        echo "<script>alert('¡El nombre de usuario ya existe en la base de datos!');</script>";
    } elseif ($email_exists) {
        // Correo Existe, adios
        echo "<script>alert('¡El correo electrónico ya existe en la base de datos!');</script>";
    } else {
        $fullName = $_POST['fullName'];
        $password = $_POST['password'];
        $role = $_POST['role'];
        if (isset($_FILES['image']) && $_FILES['image']['size'] > 0) {
            $image = file_get_contents($_FILES['image']['tmp_name']);
        } else {
            $image = null;
        }
        $birthdate = $_POST['birthdate'];
        $gender = $_POST['gender'];

        $sql = "CALL RegisUser(?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $database->getConnection()->prepare($sql);
        $stmt->bind_param("ssssssss", $username, $fullName, $email, $password, $gender, $role, $birthdate, $image);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            header("Location: Login.php");
            exit();
        } else {
            echo "<script>alert('¡El registro falló!');</script>";
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
    <title>Fantazon</title>
    <link
      rel="stylesheet"
      href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css"
    />
    <link rel="stylesheet" href="Estilos/MainPage_Style.css" />
  </head>

  <body>
    <?php include 'UI/header.php'; ?>
    <?php include 'UI/navegacion.php'; ?>

    <!-- REGISTRO DE USUARIO -->
    <main class="container-fluid">
      <section class="registration mt-4">
        <div class="row justify-content-center">
          <div class="col-md-6">
            <h2 class="mb-4">Registro</h2>

            <form id="registrationForm" action="Registros.php" method="post" onsubmit="return validateForm()" enctype="multipart/form-data">
              <div class="form-group">
                <label for="username">Usuario:</label>
                <input type="text" class="form-control" id="username" name="username" placeholder="Ingrese su nombre de Usuario"/>
              </div>
              <div class="form-group">
                <label for="fullName">Nombre Completo:</label>
                <input type="text" class="form-control" id="fullName" name="fullName" placeholder="Ingrese su Nombre Completo"/>
              </div>
              <div class="form-group">
                <label for="email">Correo Electrónico:</label>
                <input type="email" class="form-control" id="email" name="email" placeholder="Ingrese su Correo Electrónico"/>
              </div>
              <div class="form-group">
                <label for="password">Contraseña:</label>
                <input type="password" class="form-control" id="password" name="password" placeholder="Ingrese su contraseña"/>
              </div>
              <div class="form-group">
                <label for="confirmPassword">Confirmar Contraseña:</label>
                <input type="password" class="form-control" id="confirmPassword" name="confirmPassword" placeholder="Confirme su contraseña"/>
              </div>
              <div class="form-group">
                <label for="role">Rol:</label>
                <select class="form-control" id="role" name="role">
                  <option value="Comprador">Comprador</option>
                  <option value="Vendedor">Vendedor</option>
                </select>
              </div>
              <div class="form-group">
                <label for="image">Imagen:</label>
                <input type="file" class="form-control-file" id="image" name="image"/>
              </div>
              <div class="form-group">
                <label for="birthdate">Fecha de Nacimiento:</label>
                <input type="date" class="form-control" id="birthdate" name="birthdate"/>
              </div>
              <div class="form-group">
                <label for="gender">Género:</label>
                <select class="form-control" id="gender" name="gender">
                  <option value="Masculino">Masculino</option>
                  <option value="Femenino">Femenino</option>
                  <option value="Otro">Otro</option>
                </select>
              </div>
              <button type="submit" class="btn btn-outline-light">Registrarse</button>
            </form>
          </div>
        </div>
      </section>
    </main>

    <?php include 'UI/footer.html'; ?>

    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script src="Scripts/valid_registro.js"></script>
  </body>
</html>
