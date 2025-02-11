<?php
session_start();
require_once 'Acceso.php';

$fotosFk = null;

if (!isset($_SESSION['user_id'])) {
    header("Location: Login.php");
    exit();
}

if ($_SESSION['role'] == 'Comprador') {
    header("Location: Main_Page.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $database = new AccessDB("localhost:3306", "root", "tETOTETO1", "fantazon");
        $database->connectarse();

        $productoId = $_POST['productId'];
        $nombre = $_POST['productName'];
        $descripcion = $_POST['productDescription'];
        $costo = $_POST['productPrice'];
        $cantidad = $_POST['productQuantity'];
        $categoria = $_POST['categoria'];

        if (isset($_FILES['productVideo']) && $_FILES['productVideo']['error'] === UPLOAD_ERR_OK) {
            $video = file_get_contents($_FILES['productVideo']['tmp_name']);
        } else {
            $sql = "CALL GetProductoVideo(?)";
            $stmt = $database->getConnection()->prepare($sql);
            $stmt->bind_param("i", $productoId);
            $stmt->execute();
            $result = $stmt->get_result();
            
            $videoRow = $result->fetch_assoc();
            $video = $videoRow['Video'];

            $result->free();
            $stmt->close();
        }

        $sql = "CALL EditarProducto(?, ?, ?, ?, ?, ?, ?)";
        $stmt = $database->getConnection()->prepare($sql);
        $stmt->bind_param("isssdis", $productoId, $nombre, $descripcion, $costo, $cantidad, $categoria, $video);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            echo "<script>alert('¡Producto modificado con éxito!');</script>";
        } else {
            echo "<script>alert('¡Error al modificar el producto!');</script>";
        }


        $sql = "CALL GetFotosRaw(?)";
        $stmt = $database->getConnection()->prepare($sql);
        $stmt->bind_param("i", $productoId);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            $existingFotos = $result->fetch_assoc();
            // Assign the value of $fotosFk here
            $fotosFk = $existingFotos['Fotos_FK'];
        } else {
            echo "Error: Imágenes no encontradas.";
            exit();
        }
        $stmt->close();
        $result->free();
        $database->getConnection()->next_result();
        
        $existingPhoto1 = $existingFotos['Foto_1'];
        $existingPhoto2 = $existingFotos['Foto_2'];
        $existingPhoto3 = $existingFotos['Foto_3'];
        
        $newPhoto1 = $_FILES['productImage1']['tmp_name'] ? file_get_contents($_FILES['productImage1']['tmp_name']) : $existingPhoto1;
        $newPhoto2 = $_FILES['productImage2']['tmp_name'] ? file_get_contents($_FILES['productImage2']['tmp_name']) : $existingPhoto2;
        $newPhoto3 = $_FILES['productImage3']['tmp_name'] ? file_get_contents($_FILES['productImage3']['tmp_name']) : $existingPhoto3;
        

        if ($newPhoto1 || $newPhoto2 || $newPhoto3) {
            $sql = "CALL GetProductoRaw(?)";
            $stmt = $database->getConnection()->prepare($sql);
            $stmt->bind_param("i", $productoId);
            $stmt->execute();
            $result = $stmt->get_result();
            $producto = $result->fetch_assoc();
            $fotosFk = $producto['Fotos_FK'];
            $stmt->close();
            $result->free();
            $database->getConnection()->next_result();

            $sql = "CALL CambiarFoto(?, ?, ?, ?)";
            $stmt = $database->getConnection()->prepare($sql);
            $stmt->bind_param("isss", $fotosFk, $newPhoto1, $newPhoto2, $newPhoto3);
            $stmt->execute();
            $stmt->close();
        }

        $database->closeConnection();
        header("Location: Producto.php?id=$productoId");
        exit();
    } catch (mysqli_sql_exception $e) {
        echo "Error: " . $e->getMessage();
    }
}

$productoId = $_GET['product_id'] ?? null;
if (!$productoId) {
    echo "Error: Producto ID no especificado.";
    exit();
}

try {
    $database = new AccessDB("localhost:3306", "root", "tETOTETO1", "fantazon");
    $database->connectarse();

    $sql = "CALL GetProductoRaw(?)";
    $stmt = $database->getConnection()->prepare($sql);
    $stmt->bind_param("i", $productoId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $producto = $result->fetch_assoc();
        // Assign the value of $fotosFk here
        $fotosFk = $producto['Fotos_FK']; 
    } else {
        echo "Error: Producto no encontrado.";
        exit();
    }

    $stmt->close();
    $result->free();
    $database->getConnection()->next_result();


    $sql = "CALL GetFotosRaw(?)";
    $stmt = $database->getConnection()->prepare($sql);
    $stmt->bind_param("i", $fotosFk); 
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $fotos = $result->fetch_assoc();
    } else {
        echo "Error: Imágenes no encontradas.";
        exit();
    }

    $stmt->close();
    $result->free();
    $database->getConnection()->next_result(); 
    $database->closeConnection();
} catch (mysqli_sql_exception $e) {
    echo "Error: " . $e->getMessage();
}

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Fantazon - Modificar Producto</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="Estilos/MainPage_Style.css" />
</head>
<body>
    <?php include 'UI/header.php'; ?>
    <?php include 'UI/navegacion.php'; ?>

    <main class="container">
        <section class="contenido mt-4">
            <h2>Modificar Producto</h2>
            <form id="productModificationForm" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post" enctype="multipart/form-data">
                <input type="hidden" name="productId" value="<?php echo $productoId; ?>">
                <div class="form-group">
                    <label for="productName">Nombre del Producto:</label>
                    <input type="text" class="form-control" id="productName" name="productName" value="<?php echo $producto['Nombre']; ?>">
                </div>
                <div class="form-group">
                    <label for="productDescription">Descripción:</label>
                    <textarea class="form-control" id="productDescription" name="productDescription"><?php echo $producto['Descripcion']; ?></textarea>
                </div>
                <div class="form-group">
                    <label for="productPrice">Precio:</label>
                    <input type="text" class="form-control" id="productPrice" name="productPrice" value="<?php echo $producto['Costo_Base']; ?>">
                </div>
                <div class="form-group">
                    <label for="productQuantity">Cantidad:</label>
                    <input type="text" class="form-control" id="productQuantity" name="productQuantity" value="<?php echo $producto['Cantidad']; ?>">
                </div>
                <div class="form-group">
                    <label for="categoria">Categoría:</label>
                    <select class="form-control" id="categoria" name="categoria">
                        <option value="">Seleccione una categoría</option>
                        <?php
                        $categorias = getCategorias();
                        foreach ($categorias as $categoria) {
                            $selected = ($categoria['Categoria_ID'] == $producto['Categoria_FK']) ? 'selected' : '';
                            echo "<option value='{$categoria['Categoria_ID']}' $selected>{$categoria['Nombre']}</option>";
                        }
                        ?>
                    </select>
                </div>
                <div class="form-group">
                    <label for="productVideo">Video:</label>
                    <input type="file" class="form-control-file" id="productVideo" name="productVideo" accept="video/*">
                </div>
                <!-- Image Uploads -->
                <div class="form-group">
                    <label for="productImage1">Imagen 1:</label>
                    <input type="file" class="form-control-file" id="productImage1" name="productImage1" accept="image/*">
                </div>
                <div class="form-group">
                    <label for="productImage2">Imagen 2:</label>
                    <input type="file" class="form-control-file" id="productImage2" name="productImage2" accept="image/*">
                </div>
                <div class="form-group">
                    <label for="productImage3">Imagen 3:</label>
                    <input type="file" class="form-control-file" id="productImage3" name="productImage3" accept="image/*">
                </div>
                <button type="submit" class="btn btn-primary">Modificar Producto</button>
            </form>
        </section>
    </main>

    <footer class="container-fluid mt-5">
        <p>Proyecto hecho por Ricardo Ponce de León Herrera 1941445.</p>
    </footer>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('productPrice').addEventListener('input', function(event) {
                this.value = this.value.replace(/[^0-9.]/g, '');
            });

            document.getElementById('productQuantity').addEventListener('input', function(event) {
                this.value = this.value.replace(/[^0-9]/g, '');
            });

            document.getElementById('submitBtn').addEventListener('click', function(event) {
                if (!validateForm()) {
                    event.preventDefault();
                }
            });
        });

        function validateForm() {
            var productName = document.getElementById('productName').value.trim();
            var productPrice = document.getElementById('productPrice').value.trim();
            var productDescription = document.getElementById('productDescription').value.trim();
            var productQuantity = document.getElementById('productQuantity').value.trim();
            var productVideo = document.getElementById('productVideo').files[0];
            var productImage1 = document.getElementById('productImage1').files[0];
            var productImage2 = document.getElementById('productImage2').files[0];
            var productImage3 = document.getElementById('productImage3').files[0];
            var category = document.getElementById('categoria').value;

            var valid = true;

            if (!productName || productName.length > 100) {
                document.getElementById('productNameError').innerText = 'Nombre del Producto es obligatorio y no debe exceder los 100 caracteres.';
                valid = false;
            } else {
                document.getElementById('productNameError').innerText = '';
            }

            if (!productPrice || isNaN(productPrice) || parseFloat(productPrice) <= 0) {
                document.getElementById('productPriceError').innerText = 'Precio debe ser un número válido mayor que 0.';
                valid = false;
            } else {
                document.getElementById('productPriceError').innerText = '';
            }

            if (!productDescription) {
                document.getElementById('productDescriptionError').innerText = 'Descripción es obligatoria.';
                valid = false;
            } else {
                document.getElementById('productDescriptionError').innerText = '';
            }

            if (!productQuantity || isNaN(productQuantity) || parseInt(productQuantity) <= 0) {
                document.getElementById('productQuantityError').innerText = 'Cantidad debe ser un número entero positivo.';
                valid = false;
            } else {
                document.getElementById('productQuantityError').innerText = '';
            }

            if (!category || isNaN(category)) {
                document.getElementById('categoryError').innerText = 'Debe seleccionar una categoría.';
                valid = false;
            } else {
                document.getElementById('categoryError').innerText = '';
            }

            if (!productVideo) {
                document.getElementById('productVideoError').innerText = 'Debe cargar un video.';
                valid = false;
            } else if (productVideo.size > 50 * 1024 * 1024) {
                document.getElementById('productVideoError').innerText = 'El video debe ser menor a 50MB.';
                valid = false;
            } else {
                document.getElementById('productVideoError').innerText = '';
            }

            if (!productImage1 || !productImage2 || !productImage3) {
                document.getElementById('productImage1Error').innerText = !productImage1 ? 'Debe cargar tres imágenes.' : '';
                document.getElementById('productImage2Error').innerText = !productImage2 ? 'Debe cargar tres imágenes.' : '';
                document.getElementById('productImage3Error').innerText = !productImage3 ? 'Debe cargar tres imágenes.' : '';
                valid = false;
            } else {
                document.getElementById('productImage1Error').innerText = '';
                document.getElementById('productImage2Error').innerText = '';
                document.getElementById('productImage3Error').innerText = '';
            }

            if (productImage1 && productImage1.size > 2 * 1024 * 1024) {
                document.getElementById('productImage1Error').innerText = 'Cada imagen debe ser menor a 2MB.';
                valid = false;
            }

            if (productImage2 && productImage2.size > 2 * 1024 * 1024) {
                document.getElementById('productImage2Error').innerText = 'Cada imagen debe ser menor a 2MB.';
                valid = false;
            }

            if (productImage3 && productImage3.size > 2 * 1024 * 1024) {
                document.getElementById('productImage3Error').innerText = 'Cada imagen debe ser menor a 2MB.';
                valid = false;
            }

            return valid;
        }
    </script>
</body>
</html>