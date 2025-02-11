<?php
session_start();
require_once 'Acceso.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: Login.php");
    exit();
}

if ($_SESSION['role'] == 'Comprador') {
    header("Location: Main_Page.php");
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (!isset($_SESSION['username'])) {
        echo "<script>alert('¡Debe iniciar sesión para registrar un producto!');</script>";
        exit();
    }

    try {
        $usuario_FK = $_SESSION['user_id'];

        $database = new AccessDB("localhost:3306", "root", "tETOTETO1", "fantazon");
        $database->connectarse();

        $nombre = $_POST['productName'];
        $descripcion = $_POST['productDescription'];
        $costo = $_POST['productPrice'];
        $cantidad = $_POST['productQuantity'];
        $categoria = $_POST['categoria'];

        $fotos_FK = subirImagenes($_FILES['productImage1'], $_FILES['productImage2'], $_FILES['productImage3']);

        $video = null;
        if (isset($_FILES['productVideo']) && $_FILES['productVideo']['size'] > 0) {
            $video = file_get_contents($_FILES['productVideo']['tmp_name']);
        }

        $sql = "CALL InsertProducto(?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $database->getConnection()->prepare($sql);
        $stmt->bind_param("sssdiisi", $nombre, $descripcion, $video, $costo, $cantidad, $fotos_FK, $usuario_FK, $categoria);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            echo "<script>alert('¡Producto registrado con éxito!');</script>";
        } else {
            echo "<script>alert('¡Error al registrar el producto!');</script>";
        }

        $stmt->close();
        $database->closeConnection();
        header("Location: Main_Page.php");
        exit(); 
    } catch (mysqli_sql_exception $e) {
        echo "Error: " . $e->getMessage();
    }
}

function subirImagenes($image1, $image2, $image3) {
    if ($image1['error'] != UPLOAD_ERR_OK || $image2['error'] != UPLOAD_ERR_OK || $image3['error'] != UPLOAD_ERR_OK) {
        die("Error uploading one or more images.");
    }

    $database = new AccessDB("localhost:3306", "root", "tETOTETO1", "fantazon");
    $database->connectarse();

    $foto1 = file_get_contents($image1['tmp_name']);
    $foto2 = file_get_contents($image2['tmp_name']);
    $foto3 = file_get_contents($image3['tmp_name']);

    $sql = "CALL InsertFotos(?, ?, ?, ?, @last_id)";
    $stmt = $database->getConnection()->prepare($sql);
    $estatus = true;
    $stmt->bind_param("ssss", $foto1, $foto2, $foto3, $estatus);
    $stmt->execute();

    if (!$stmt) {
        die("Error executing stored procedure: " . $database->getConnection()->error);
    }

    $stmt->close();

    $result = $database->getConnection()->query("SELECT @last_id as last_id");
    if ($result && $row = $result->fetch_assoc()) {
        $fotos_FK = $row['last_id'];
    } else {
        die("Error fetching last inserted ID: " . $database->getConnection()->error);
    }

    $database->closeConnection();

    return $fotos_FK;
    
}

if (!function_exists('getCategorias')) {
    function getCategorias() {
        $database = new AccessDB("localhost:3306", "root", "tETOTETO1", "fantazon");
        $database->connectarse();

        $sql = "CALL GetCategories()";
        $result = $database->getConnection()->query($sql);

        $categorias = [];
        if ($result) {
            while ($row = $result->fetch_assoc()) {
                $categorias[] = $row;
            }
        }

        $database->closeConnection();
        return $categorias;
    }
}
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
        <section class="contenido mt-4">
            <h2>Registrar Nuevo Producto</h2>
            <form id="productRegistrationForm" action="Vender.php" method="post" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="productName">Nombre del Producto:</label>
                    <input type="text" class="form-control" id="productName" name="productName" placeholder="Ingrese el nombre del producto" />
                    <div id="productNameError" class="text-danger"></div>
                </div>

                <div class="form-group">
                    <label for="productPrice">Precio:</label>
                    <input type="text" class="form-control" id="productPrice" name="productPrice" placeholder="Ingrese el precio del producto" />
                    <div id="productPriceError" class="text-danger"></div>
                </div>

                <div class="form-group">
                    <label for="productDescription">Descripción:</label>
                    <textarea class="form-control" id="productDescription" name="productDescription" placeholder="Ingrese la descripción del producto"></textarea>
                    <div id="productDescriptionError" class="text-danger"></div>
                </div>

                <div class="form-group">
                    <label for="productQuantity">Cantidad:</label>
                    <input type="text" class="form-control" id="productQuantity" name="productQuantity" placeholder="Ingrese la cantidad" />
                    <div id="productQuantityError" class="text-danger"></div>
                </div>

                <div class="form-group">
                    <label for="categoria">Categoría:</label>
                    <select class="form-control" id="categoria" name="categoria">
                        <option value="">Seleccione una categoría</option>
                        <?php
                        $categorias = getCategorias();
                        foreach ($categorias as $categoria) {
                            $selected = ($categoria['Categoria_ID'] == $_POST['categoria']) ? 'selected' : '';
                            echo "<option value='{$categoria['Categoria_ID']}' $selected>{$categoria['Nombre']}</option>";
                        }
                        ?>
                    </select>

                    <div id="categoryError" class="text-danger"></div>
                </div>

                <div class="form-group">
                    <label for="productVideo">Video (Menor a 50MB):</label>
                    <input type="file" class="form-control-file" id="productVideo" name="productVideo" accept="video/*" />
                    <div id="productVideoError" class="text-danger"></div>
                </div>

                <div class="form-group">
                    <label for="productImage1">Imagen 1 (Menor a 2MB):</label>
                    <input type="file" class="form-control-file" id="productImage1" name="productImage1" accept="image/*" />
                    <div id="productImage1Error" class="text-danger"></div>
                </div>

                <div class="form-group">
                    <label for="productImage2">Imagen 2 (Menor a 2MB):</label>
                    <input type="file" class="form-control-file" id="productImage2" name="productImage2" accept="image/*" />
                    <div id="productImage2Error" class="text-danger"></div>
                </div>

                <div class="form-group">
                    <label for="productImage3">Imagen 3 (Menor a 2MB):</label>
                    <input type="file" class="form-control-file" id="productImage3" name="productImage3" accept="image/*" />
                    <div id="productImage3Error" class="text-danger"></div>
                </div>

                <button type="submit" id="submitBtn" class="btn btn-outline-light">Registrar Producto</button>
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
