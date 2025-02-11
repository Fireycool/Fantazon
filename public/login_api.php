<?php
require_once 'Acceso.php';
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // RECIBIR INFO DE USUARIO
    $loginUsername = $_POST["username"];
    $loginPassword = $_POST["password"];

    // CONECTAR A LA BASE DE DATOS
    $database = new AccessDB("localhost:3306", "root", "tETOTETO1", "fantazon");
    $database->connectarse();
    $conn = $database->getConnection();

    // Usar el Stored Procedure
    $sql = "CALL LogInUser(?, ?, @user_id, @username, @fullname, @mail, @role, @birthdate, @gender, @image, @in)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $loginUsername, $loginPassword);
    $stmt->execute();

    $result = $conn->query("SELECT @user_id, @username, @fullname, @mail, @role, @birthdate, @gender, @image, @in");
    $row = $result->fetch_assoc();

    // Checar si funciono
    $loggedIn = $row['@in'];

    $response = array();
    if ($loggedIn) {
        // Guardar Info de Sesion
        $_SESSION['user_id']    = $row['@user_id'];
        $_SESSION['username']   = $row['@username'];
        $_SESSION['fullName']   = $row['@fullname'];
        $_SESSION['email']      = $row['@mail'];
        $_SESSION['role']       = $row['@role'];
        $_SESSION['birthdate']  = $row['@birthdate'];
        $_SESSION['gender']     = $row['@gender'];
        $_SESSION['image']      = $row['@image']; 

        $response['success'] = true;
    } else {
        $response['success'] = false;
        $response['message'] = 'Login fallido. Nombre de Usuario o Contraseña incorrecto.';
    }

    $database->closeConnection();

    header("Content-type: application/json");
    echo json_encode($response);
} else {
    http_response_code(405); 
    echo json_encode(array('error' => 'Method not allowed.'));
}
?>
