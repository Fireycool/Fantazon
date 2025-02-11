<?php
require_once 'Acceso.php';

session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: Login.php");
    exit();
}

$userID = $_SESSION['user_id'];

$database = new AccessDB("localhost:3306", "root", "tETOTETO1", "fantazon");
$database->connectarse();

$sql_get_chats = "CALL GetChatporUser(?)";
$stmt = $database->getConnection()->prepare($sql_get_chats);
$stmt->bind_param("i", $userID);
$stmt->execute();
$result = $stmt->get_result();

$chats = [];
if ($result) {
    while ($row = $result->fetch_assoc()) {
        $chats[] = $row;
    }
}

$database->closeConnection();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mis Chats</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="Estilos/MainPage_Style.css">
</head>
<body>

<?php include 'UI/header.php'; ?>
<?php include 'UI/navegacion.php'; ?>

<main class="container">
    <section class="producto-listado mt-4">
        <h2>Mis Chats</h2>

        <div class="card-comment-container">
            <?php foreach ($chats as $chat): ?>
                <div class="card">
                    <a href="Chat.php?chat_id=<?php echo $chat['Chat_ID']; ?>" class="card-link">
                        <img src="data:image/jpeg;base64,<?php echo base64_encode($chat['Producto_Foto']); ?>" class="img-fluid" alt="<?php echo $chat['Producto_Nombre']; ?>" />
                        <div class="card-body">
                            <h3 class="card-title funky-text"><?php echo $chat['Producto_Nombre']; ?></h3>
                            <p class="card-text">Precio Acordado: $<?php echo $chat['Precio_Acordado']; ?></p>
                            <p class="card-text">Comprador: <?php echo $chat['Comprador_Username']; ?></p>
                        </div>
                    </a>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
</main>

<?php include 'UI/footer.html'; ?>

<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
