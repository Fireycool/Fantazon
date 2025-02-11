// Validacion Registro

function validateForm() {
  var username = document.getElementById("username").value;
  var fullName = document.getElementById("fullName").value;
  var email = document.getElementById("email").value;
  var password = document.getElementById("password").value;
  var confirmPassword = document.getElementById("confirmPassword").value;
  var role = document.getElementById("role").value;
  var image = document.getElementById("image").value;
  var birthdate = document.getElementById("birthdate").value;
  var gender = document.getElementById("gender").value;

  if (!username || !fullName || !email || !password || !confirmPassword || !role || !image || !birthdate || !gender) {
    alert("Favor de llenar todos los campos");
    return false;
  }

  if (password !== confirmPassword) {
    alert("Las contraseñas no coinciden.");
    return false;
  }

  //Contraseña
  var passwordRegex = /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/;
  if (!passwordRegex.test(password)) {
    alert("La contraseña debe tener al menos 8 caracteres, una mayúscula, una minúscula, un número y un carácter especial.");
    return false;
  }

  //Email
  var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
  if (!emailRegex.test(email)) {
    alert("Por favor, ingrese un correo electrónico válido.");
    return false;
  }
  
  return true;
}

  
  