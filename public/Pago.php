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
            <h2>Pago con Tarjeta</h2>
            <form id="paymentForm" action="PagoSinPaypal.php?carrito_id=<?php echo htmlspecialchars($_GET['carrito_id']); ?>" method="post">
                <div class="form-group">
                    <label for="cardNumber">Número de Tarjeta:</label>
                    <input type="text" class="form-control" id="cardNumber" name="cardNumber" placeholder="Ingrese el número de tarjeta" />
                    <div id="cardNumberError" class="text-danger"></div>
                </div>

                <div class="form-group">
                    <label for="expiryDate">Fecha de Vencimiento:</label>
                    <input type="text" class="form-control" id="expiryDate" name="expiryDate" placeholder="MM/AA" />
                    <div id="expiryDateError" class="text-danger"></div>
                </div>

                <div class="form-group">
                    <label for="cvv">CVV:</label>
                    <input type="text" class="form-control" id="cvv" name="cvv" placeholder="Ingrese el codigo CVV" />
                    <div id="cvvError" class="text-danger"></div>
                </div>
                
                <div class="form-group">
                    <label for="fullName">Nombre Completo:</label>
                    <input type="text" class="form-control" id="fullName" name="fullName" placeholder="Ingrese Su Nombre Completo" />
                    <div id="fullNameError" class="text-danger"></div>
                </div>

                <div class="form-group">
                    <label for="phoneNumber">Telefono:</label>
                    <input type="text" class="form-control" id="phoneNumber" name="phoneNumber" placeholder="Ingrese su numero telefonico" />
                    <div id="phoneNumberError" class="text-danger"></div>
                </div>

                <button type="submit" class="btn btn-warning">Pagar</button>
            </form>
            <a href="PagoSinPaypal.php?carrito_id=<?php echo htmlspecialchars($_GET['carrito_id']); ?>" class="btn btn-warning">Saltarse Validacion y solo "Pagar"</a>

        </section>
    </main>

    <?php include 'UI/footer.html'; ?>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('paymentForm').addEventListener('submit', function(event) {
                var cardNumber = document.getElementById('cardNumber').value.trim();
                var expiryDate = document.getElementById('expiryDate').value.trim();
                var cvv = document.getElementById('cvv').value.trim();
                var fullName = document.getElementById('fullName').value.trim();
                var phoneNumber = document.getElementById('phoneNumber').value.trim();
                var valid = true;

                var cardNumberRegex = /^[0-9]{16}$/;
                var expiryDateRegex = /^(0[1-9]|1[0-2])\/?([0-9]{2})$/;
                var cvvRegex = /^[0-9]{3,4}$/;
                var fullNameRegex = /^[A-Za-z\s]{3,}$/;
                var phoneNumberRegex = /^\+?[0-9]{7,}$/;

                if (!cardNumberRegex.test(cardNumber)) {
                    document.getElementById('cardNumberError').innerText = 'Por favor, ingrese un número de tarjeta válido.';
                    valid = false;
                } else {
                    document.getElementById('cardNumberError').innerText = '';
                }

                if (!expiryDateRegex.test(expiryDate)) {
                    document.getElementById('expiryDateError').innerText = 'Por favor, ingrese una fecha de vencimiento válida (MM/AA).';
                    valid = false;
                } else {
                    document.getElementById('expiryDateError').innerText = '';
                }

                if (!cvvRegex.test(cvv)) {
                    document.getElementById('cvvError').innerText = 'Por favor, ingrese un código CVV válido.';
                    valid = false;
                } else {
                    document.getElementById('cvvError').innerText = '';
                }

                if (!fullNameRegex.test(fullName)) {
                    document.getElementById('fullNameError').innerText = 'Por favor, ingrese un nombre válido.';
                    valid = false;
                } else {
                    document.getElementById('fullNameError').innerText = '';
                }

                if (!phoneNumberRegex.test(phoneNumber)) {
                    document.getElementById('phoneNumberError').innerText = 'Por favor, ingrese un número de teléfono válido.';
                    valid = false;
                } else {
                    document.getElementById('phoneNumberError').innerText = '';
                }

                if (!valid) {
                    event.preventDefault();
                }
            });
        });
    </script>
</body>
</html>
