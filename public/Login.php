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

<!-- INICIAR SESION -->
<main class="container-fluid">
    <section class="login mt-4">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <h2 class="mb-4">Iniciar Sesión</h2>
                <form id="loginForm" method="POST" action="login_api.php"> 
                    <div class="form-group">
                        <label for="username">Usuario:</label>
                        <input type="text" class="form-control" id="username" name="username" placeholder="Ingrese su usuario" />
                    </div>
                    <div class="form-group">
                        <label for="password">Contraseña:</label>
                        <input type="password" class="form-control" id="password" name="password" placeholder="Ingrese su contraseña" />
                    </div>
                    <button type="submit" id="loginBtn" class="btn btn-outline-light"> 
                        Iniciar Sesión
                    </button>
                </form>
            </div>
        </div>
    </section>
</main>

<?php include 'UI/footer.html'; ?>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script>
    $(document).ready(function() {
        $('#loginForm').submit(function(event) {
            event.preventDefault(); 

            var username = $('#username').val();
            var password = $('#password').val();

            $.ajax({
                url: 'http://localhost:8080/Fantazon/public/login_api.php',
                type: 'POST',
                dataType: 'json',
                data: { username: username, password: password },
                success: function(response) {
                    if (response.success) {
                        window.location.href = 'Perfil.php';
                    } else {
                        alert('Login fallido. Nombre de Usuaro o Contraseña incorrecto.');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error:', error);
                    alert('Ocurrio un error intentar luego.');
                }
            });
        });
    });
</script>

</body>
</html>
