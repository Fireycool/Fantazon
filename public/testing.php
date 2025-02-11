<?php
require_once 'Acceso.php'; 

$imageData = file_get_contents('C:\\Users\\Owner\\Pictures\\PROFILE CONTENT\\bep.jpg');


$database = new AccessDB("localhost:3306", "root", "tETOTETO1", "fantazon");

$database->connectarse();
$conn = $database->getConnection();

if (!$conn) {
    die("Connection failed: " . $database->getError());
}


$sql = "INSERT INTO Fotos (Foto_1, Foto_2, Foto_3) VALUES (?,?,?)";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sss", $imageData, $imageData, $imageData);

if ($stmt->execute()) {
    echo "Image inserted successfully.";
} else {
    echo "Error inserting image: " . $conn->error;
}

$stmt->close();
$database->closeConnection();
?>
