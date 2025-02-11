function loginUser() {
    var username = document.getElementById("username").value;
    var password = document.getElementById("password").value;

    if (!username || !password) {
        alert("Favor de ingresar usuario y contraseña.");
        return false;
    }

    $.ajax({
        url: 'Log.php',
        type: 'POST',
        data: {
            username: username,
            password: password
        },
        dataType: 'json',
        success: function(response) {
            if (response.success) {
                window.location.href = 'Perfil.php';
            } else {
                alert(response.message);
            }
        },
        error: function(xhr, status, error) {
            alert('Error: ' + error);
        }
    });

    return false;
}
