<?php
if (session_status() == PHP_SESSION_NONE) {
  session_start();
}

if (isset($_SESSION['username']) && isset($_SESSION['role'])) {
    if ($_SESSION['role'] == 'Vendedor' || $_SESSION['role'] == 'Admin') {
        include 'nav_vende.php'; 
    } else {
        include 'nav_reg.php'; 
    }

    if ($_SESSION['role'] == 'Admin') {
        include 'nav_admin.php'; 
    }
} else {
    include 'nav_reg.php'; 
}
?>
