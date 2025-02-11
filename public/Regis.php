<?php
require_once 'Acceso.php';

$database = new Database("localhost:3306", "root", "tETOTETO1", "fantazon");
$database->connectarse();

$username =     $_POST['username'];
$fullName =     $_POST['fullName'];
$email =        $_POST['email'];
$password =     $_POST['password'];
$role =         $_POST['role'];
$image = '';
$birthdate =    $_POST['birthdate'];
$gender =       $_POST['gender'];


$sql = "INSERT INTO Usuarios (Username, Nombre_Completo, Mail, Contraseña, Sexo, Rol, Foto, Fecha_Nac) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
$stmt = $database->getConnection()->prepare($sql);
$stmt->bind_param("ssssssss", $username, $fullName, $email, $password, $gender, $role, $image, $birthdate);
$stmt->execute();

if ($stmt->affected_rows > 0) {
        //TODO BIEN
        echo "<script>alert('TODO BIEN');</script>";
} else {
        //Algo salio mal
        echo "<script>alert('ALGO SALIO MAL');</script>";
}

$stmt->close();
$database->closeConnection();
?>
