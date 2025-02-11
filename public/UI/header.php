<header class="container-fluid">
    <div class="row">
        <div class="col-md-3">
            <a href="Main_Page.php" class="logo-link">
                <h1 class="logo">Fantazon</h1>
                <h6 class="logo">Equipamiento Para Toda Aventura</h6>
            </a>
        </div>
        <div class="col-md-5">
            <form class="form-inline" action="Busqueda.php" method="GET">
                <input
                    class="form-control custom-search"
                    type="text"
                    placeholder="Buscar productos..."
                    aria-label="Búsqueda"
                    name="searchTerm"
                />
                <select class="form-control custom-select" id="category" name="category">
                    <option value="">Todas las Categorias</option>
                    <?php
                    require_once 'Acceso.php';
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
                    $categorias = getCategorias();
                    foreach ($categorias as $categoria) {
                        echo "<option value='{$categoria['Categoria_ID']}'>{$categoria['Nombre']}</option>";
                    }
                    ?>
                </select>
                <button class="btn btn-outline-light my-2 my-sm-0" type="submit">Buscar</button>
            </form>
        </div>
        <div class="col-md-4 text-right">
            <?php
            if (session_status() == PHP_SESSION_NONE) {
                session_start();
            }            
            if (isset($_SESSION['username'])) {
                echo '<a href="Perfil.php" class="btn btn-outline-light">Perfil</a>';
                echo '<a href="Listas.php" class="btn btn-outline-light">Listas</a>';
                echo '<a href="Logout.php" class="btn btn-outline-light">Cerrar Sesión</a>';
            } else {
                echo '<a href="Login.php" class="btn btn-outline-light">Iniciar Sesión</a>';
                echo '<a href="Registros.php" class="btn btn-outline-light">Registrarse</a>';
            }
            ?>
            <a href="Carrito.php" class="btn btn-outline-warning">Carreta</a>
        </div>
    </div>
</header>
