<?php
require_once 'Acceso.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: Login.php");
    exit();
}

$chatId = intval($_GET['chat_id']);

$database = new AccessDB("localhost:3306", "root", "tETOTETO1", "fantazon");
$database->connectarse();
$conn = $database->getConnection();

// INFO DEL CHAT
$sql_get_chat_info = "CALL GetChatdelChat(?)";
$stmt = $conn->prepare($sql_get_chat_info);
$stmt->bind_param("i", $chatId);
$stmt->execute();
$result = $stmt->get_result();
$chatInfo = $result->fetch_assoc();

$stmt->free_result();
$stmt->close();

// MENSAJES
$sql_get_chat_messages = "CALL GetMessage(?)";
$stmt = $conn->prepare($sql_get_chat_messages);
$stmt->bind_param("i", $chatId);
$stmt->execute();
$result = $stmt->get_result();

$chatMessages = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $chatMessages[] = $row;
    }
}

$stmt->free_result();
$stmt->close();
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
    <section class="producto-destacado mt-4">
        <h2>Producto a Regatear</h2>
        <div class="card-deck">
            <a href="Producto.php?id=<?php echo $chatInfo['Producto_ID']; ?>" class="card-link">
                <div class="card">
                    <img src="data:image/jpeg;base64,<?php echo base64_encode($chatInfo['Producto_Foto']); ?>" alt="Product Image" />
                    <div class="card-body">
                        <h3 class="card-title funky-text"><?php echo $chatInfo['Producto_Nombre']; ?></h3>
                        <h5 class="card-text-producto">Precio Acordado: $<?php echo $chatInfo['Precio_Acordado']; ?></h5>
                    </div>
                </div>
            </a>
        </div>
    </section>


    <?php if ($_SESSION['user_id'] == $chatInfo['Vendedor_ID']):?>
        <section class="producto mt-4">
            <form id="modify-price-form" action="ChatModifPrecio.php" method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="new-price">Nuevo Precio:</label>
                    <input type="text" class="form-control" id="new-price" name="new_price" step="0.01" required>
                    <div id="newPriceError" class="text-danger"></div>
                </div>
                <input type="hidden" name="chat_id" value="<?php echo $chatId; ?>">
                <button class="btn btn-primary" type="submit">Modificar Precio</button>
            </form>
        </section>
    <?php elseif ($_SESSION['user_id'] == $chatInfo['Comprador_ID']): ?>
        <section class="producto mt-4">
            <form id="access-price-form" action="AddCarrito.php" method="post">
                <input type="hidden" name="product_id" value="<?php echo $chatInfo['Producto_ID']; ?>">
                <input type="hidden" name="trueque" value="<?php echo $chatInfo['Precio_Acordado']; ?>">
                <button class="btn btn-outline-warning" type="submit">Acceder al Precio</button>
            </form>
        </section>
    <?php endif; ?>


    <section class="contenido mt-4">
        <h2>Chat:</h2>
        <div class="card chatcard">
            <div class="card-header row">
                <h4 class="col-md-6 funky-text"><?php echo $chatInfo['Vendedor_Username']; ?></h4>
                <h4 class="unfunky-text col-md-6 text-right"><?php echo $chatInfo['Comprador_Username']; ?></h4>
            </div>

            <?php foreach ($chatMessages as $message): ?>
                <?php
                    $isBuyerMessage = $message['Usuario_ID'] == $chatInfo['Comprador_ID'];
                    $messageClass = $isBuyerMessage ? 'initial' : 'funky-text';
                ?>
                <div class="card-body card card-comment-container <?php echo $messageClass; ?>">
                    <?php echo $message['Texto']; ?>
                </div>
            <?php endforeach; ?>

            <div class="card-footer">
                <form id="chat-form" action="SendMessage.php" method="post">
                    <input class="form-control" type="text" placeholder="Escribir Mensaje..." id="chat-input" name="message" />
                    <input type="hidden" name="chat_id" value="<?php echo $chatId; ?>">
                    <button class="btn btn-outline-light mt-2" type="submit">Enviar</button>
                </form>
            </div>
        </div>
    </section>
</main>

<?php include 'UI/footer.html'; ?>


<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('new-price').addEventListener('input', function(event) {
            this.value = this.value.replace(/[^0-9.]/g, '');
        });

        document.getElementById('modify-price-form').addEventListener('submit', function(event) {
            if (!validateNewPrice()) {
                event.preventDefault();
            }
        });
    });

    function validateNewPrice() {
        var newPrice = document.getElementById('new-price').value.trim();
        var valid = true;

        if (!newPrice || isNaN(newPrice) || parseFloat(newPrice) <= 0) {
            document.getElementById('newPriceError').innerText = 'Nuevo Precio debe ser un número válido mayor que 0.';
            valid = false;
        } else {
            document.getElementById('newPriceError').innerText = '';
        }

        return valid;
    }
</script>

<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
