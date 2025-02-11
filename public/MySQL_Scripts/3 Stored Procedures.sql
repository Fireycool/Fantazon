use fantazon;

DROP PROCEDURE IF EXISTS GetAllInUser;
DROP PROCEDURE IF EXISTS LogInUser;
DROP PROCEDURE IF EXISTS RegisUser;
DROP PROCEDURE IF EXISTS ModifyUser;
DROP PROCEDURE IF EXISTS CheckUsernameExists;
DROP PROCEDURE IF EXISTS CheckEmailExists;
DROP PROCEDURE IF EXISTS CheckUsernameExistsModif;
DROP PROCEDURE IF EXISTS CheckEmailExistsModif;
DROP PROCEDURE IF EXISTS DeleteUser;
DROP PROCEDURE IF EXISTS ReactivateUser;
DROP PROCEDURE IF EXISTS InsertProducto;
DROP PROCEDURE IF EXISTS AddComment;
DROP PROCEDURE IF EXISTS AprovarProducto;
DROP PROCEDURE IF EXISTS GetUserInfo;
DROP PROCEDURE IF EXISTS GetPorAprovar;
DROP PROCEDURE IF EXISTS InsertFotos;
DROP PROCEDURE IF EXISTS GetMisProd;
DROP PROCEDURE IF EXISTS GetPopularProducts;
DROP PROCEDURE IF EXISTS GetDestacado;
DROP PROCEDURE IF EXISTS GetProductDetails;
DROP PROCEDURE IF EXISTS GetProductComments;
DROP PROCEDURE IF EXISTS GetCarrito;
DROP PROCEDURE IF EXISTS AddProductToCart;
DROP PROCEDURE IF EXISTS UpdateProductInCart;
DROP PROCEDURE IF EXISTS DeleteProductCart;
DROP PROCEDURE IF EXISTS DeleteAllProductCart;
DROP PROCEDURE IF EXISTS AddCategory;
DROP PROCEDURE IF EXISTS GetCategories;
DROP PROCEDURE IF EXISTS DeleteCategory;
DROP PROCEDURE IF EXISTS InsertLista;
DROP PROCEDURE IF EXISTS InsertProdLista;
DROP PROCEDURE IF EXISTS GetListas;
DROP PROCEDURE IF EXISTS GetProductoListas;
DROP PROCEDURE IF EXISTS GetListInfo;
DROP PROCEDURE IF EXISTS GetProductoBuscar;
DROP PROCEDURE IF EXISTS DeleteProdLista;
DROP PROCEDURE IF EXISTS DeleteLista;
DROP PROCEDURE IF EXISTS DeleteProducto;
DROP PROCEDURE IF EXISTS RecoverProducto;
DROP PROCEDURE IF EXISTS InsertChat;
DROP PROCEDURE IF EXISTS GetChat;
DROP PROCEDURE IF EXISTS GetChatdelChat;
DROP PROCEDURE IF EXISTS InsertMessage;
DROP PROCEDURE IF EXISTS GetMessage;
DROP PROCEDURE IF EXISTS GetChatporUser;
DROP PROCEDURE IF EXISTS ModifChatPrecio;
DROP PROCEDURE IF EXISTS RealizarVenta;
DROP PROCEDURE IF EXISTS GetMisVentasAgrupadas;
DROP PROCEDURE IF EXISTS GetMisComprasAgrupadas;
DROP PROCEDURE IF EXISTS EditarProducto;
DROP PROCEDURE IF EXISTS GetProductoRaw;
DROP PROCEDURE IF EXISTS CambiarFoto;
DROP PROCEDURE IF EXISTS GetFotosRaw;
DROP PROCEDURE IF EXISTS GetProductoVideo;


-- Obtener TODA la info de un Usuario
DELIMITER //
CREATE PROCEDURE GetAllInUser(IN p_userid INT)
BEGIN
    SELECT 
        Usuario_ID, Username, Nombre_Completo, Mail, Contraseña, Sexo, Rol, Fecha_Nac, Foto, Fecha_Ing, Estatus
    FROM VerAllUserInfo
    WHERE Usuario_ID = p_userid;
END //
DELIMITER ;

-- Log in
DELIMITER //
CREATE PROCEDURE LogInUser(
    IN p_username VARCHAR(50),
    IN p_password VARCHAR(100),
    OUT p_user_id INT,
    OUT p_out_username VARCHAR(50),
    OUT p_fullname VARCHAR(100),
    OUT p_mail VARCHAR(100),
    OUT p_role ENUM('Comprador', 'Vendedor', 'Admin'),
    OUT p_birthdate DATE,
    OUT p_gender ENUM('Masculino', 'Femenino', 'Otro'),
    OUT p_image LONGBLOB, 
    OUT p_in BOOLEAN
)
BEGIN
    DECLARE v_real_password VARCHAR(100);
    DECLARE v_user_status BOOLEAN;

    -- Traer Contraseña y Estatus
    SELECT Contraseña, Estatus INTO v_real_password, v_user_status
    FROM Usuarios
    WHERE Username = p_username;

    -- Verificacion
    IF v_real_password IS NOT NULL AND v_real_password = p_password AND v_user_status = TRUE THEN
        -- Mandar datos del Usuario
        SELECT Usuario_ID, Username, Nombre_Completo, Mail, Rol, Fecha_Nac, Sexo, Foto
        INTO p_user_id, p_out_username, p_fullname, p_mail, p_role, p_birthdate, p_gender, p_image
        FROM Usuarios
        WHERE Username = p_username;

        -- Bien!!
        SET p_in = TRUE;
    ELSE
        -- Mal grrr
        SET p_in = FALSE;
    END IF;
END //
DELIMITER ;


-- Registrar un Usuario
DELIMITER //
CREATE PROCEDURE RegisUser(
    IN p_username VARCHAR(50),
    IN p_fullname VARCHAR(100),
    IN p_email VARCHAR(100),
    IN p_password VARCHAR(100),
    IN p_sex ENUM('Masculino', 'Femenino', 'Otro'),
    IN p_role ENUM('Comprador', 'Vendedor', 'Admin'),
    IN p_birthdate DATE,
    IN p_foto LONGBLOB
)
BEGIN
    INSERT INTO Usuarios (Username, Nombre_Completo, Mail, Contraseña, Sexo, Rol, Fecha_Nac, Foto)
    VALUES (p_username, p_fullname, p_email, p_password, p_sex, p_role, p_birthdate, p_foto);
END //
DELIMITER ;

-- Modificar un Usuario
DELIMITER //
CREATE PROCEDURE ModifyUser(
    IN p_userid INT,
    IN p_username VARCHAR(50),
    IN p_fullname VARCHAR(100),
    IN p_email VARCHAR(100),
    IN p_password VARCHAR(100),
    IN p_sex ENUM('Masculino', 'Femenino', 'Otro'),
    IN p_role ENUM('Comprador', 'Vendedor', 'Admin'),
    IN p_birthdate DATE,
    IN p_foto LONGBLOB
)
BEGIN
    UPDATE Usuarios
    SET Username = p_username,
        Nombre_Completo = p_fullname,
        Mail = p_email,
        Contraseña = p_password,
        Sexo = p_sex,
        Rol = p_role,
        Fecha_Nac = p_birthdate,
        Foto = p_foto
    WHERE Usuario_ID = p_userid;
END //
DELIMITER ;



-- Verificar Existencia de un Nombre de Usuario
DELIMITER //
CREATE PROCEDURE CheckUsernameExists(
    IN p_username VARCHAR(50)
)
BEGIN
    SELECT EXISTS(SELECT 1 FROM Usuarios WHERE Username = p_username) AS username_exists;
END //
DELIMITER ;

-- Verificar Existencia de Correo 
DELIMITER //
CREATE PROCEDURE CheckEmailExists(
    IN p_email VARCHAR(100)
)
BEGIN
    SELECT EXISTS(SELECT 1 FROM Usuarios WHERE Mail = p_email) AS email_exists;
END //
DELIMITER ;

-- Verificar Existencia de un Nombre de Usuario AL MODIFICAR
DELIMITER //

CREATE PROCEDURE CheckUsernameExistsModif(
    IN p_user_id INT,
    IN p_username VARCHAR(50)
)
BEGIN
    SELECT EXISTS(SELECT 1 FROM Usuarios WHERE Username = p_username AND Usuario_ID != p_user_id) AS username_exists;
END //

DELIMITER ;


-- Verificar Existencia de Correo AL MODIFICAR
DELIMITER //
CREATE PROCEDURE CheckEmailExistsModif(
    IN p_user_id INT,
    IN p_email VARCHAR(100)
)
BEGIN
    SELECT EXISTS(SELECT 1 FROM Usuarios WHERE Mail = p_email AND Usuario_ID != p_user_id) AS email_exists;
END //
DELIMITER ;

-- Borrar un Usuario
DELIMITER //
CREATE PROCEDURE DeleteUser(
    IN p_user_id INT
)
BEGIN
    UPDATE Usuarios SET Estatus = 0 WHERE Usuario_ID = p_user_id;
END //
DELIMITER ;

-- Reactivar un Usuario
DELIMITER //
CREATE PROCEDURE ReactivateUser(
    IN p_userId INT
)
BEGIN
    UPDATE Usuarios
    SET Estatus = 1
    WHERE Usuario_ID = p_userId;
END //
DELIMITER ;

-- Insertar un Nuevo Producto (Incompleto)
DELIMITER $$
CREATE PROCEDURE InsertProducto (
    IN pNombre VARCHAR(100),
    IN pDescripcion TEXT,
    IN pVideo LONGBLOB,
    IN pCosto_Base DECIMAL(10, 2),
    IN pCantidad INT,
    IN pFotos_FK INT,
    IN pUsuario_FK INT,
    IN pCategoria_FK INT
)
BEGIN
    INSERT INTO Producto (
        Nombre,
        Descripcion,
        Video,
        Costo_Base,
        Cantidad,
        Fotos_FK,
        Usuario_FK,
        Categoria_FK
    ) VALUES (
        pNombre,
        pDescripcion,
        pVideo,
        pCosto_Base,
        pCantidad,
        pFotos_FK,
        pUsuario_FK,
        pCategoria_FK
    );
END $$
DELIMITER ;

-- Insertar Fotos
DELIMITER //
CREATE PROCEDURE InsertFotos (
    IN foto1 LONGBLOB,
    IN foto2 LONGBLOB,
    IN foto3 LONGBLOB,
    IN estatus BOOLEAN,
    OUT last_id INT
)
BEGIN
    INSERT INTO Fotos (Foto_1, Foto_2, Foto_3, Estatus)
	VALUES (foto1, foto2, foto3, estatus);
    SET last_id = LAST_INSERT_ID();
END //
DELIMITER ;

-- Agregar un Comentario
DELIMITER //
CREATE PROCEDURE AddComment (
    IN p_text TEXT,
    IN p_rating INT,
    IN p_producto_id INT,
    IN p_usuario_id INT
)
BEGIN
    DECLARE user_exists INT;
    DECLARE product_exists INT;

    -- Revisa si el usuario Existe
    SELECT EXISTS (SELECT 1 FROM Usuarios WHERE Usuario_ID = p_usuario_id) INTO user_exists;
    IF user_exists = 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Usuario no Existe';
    END IF;

    -- Revisa si el Producto Existe
    SELECT EXISTS (SELECT 1 FROM Producto WHERE Producto_ID = p_producto_id) INTO product_exists;
    IF product_exists = 0 THEN
        SIGNAL SQLSTATE '45000'
        SET MESSAGE_TEXT = 'Producto no Existe';
    END IF;

    -- Inserta el Comentario
    INSERT INTO Comments (Texto, Rating, Producto_FK, Usuario_FK)
    VALUES (p_text, p_rating, p_producto_id, p_usuario_id);
END //
DELIMITER ;

DELIMITER //
CREATE PROCEDURE AprovarProducto(
    IN p_productId INT
)
BEGIN
    UPDATE Producto
    SET Aprobado = 1
    WHERE Producto_ID = p_productId;
END //
DELIMITER ;

DELIMITER //
CREATE PROCEDURE GetUserInfo()
BEGIN
    SELECT 
        Usuario_ID, Username, Nombre_Completo, Mail, Sexo, Rol, Fecha_Nac, Foto, Fecha_Ing, Estatus
    FROM VerUserInfo;
END //
DELIMITER ;

DELIMITER //
CREATE PROCEDURE GetPorAprovar()
BEGIN
    SELECT 
        Producto_ID, 
        Producto,
        Descripcion, 
        Costo_Base, 
        Cantidad,
        Usuario,
        Imagen
    FROM VerPorAprovar;
END //
DELIMITER ;

DELIMITER //
CREATE PROCEDURE GetMisProd(IN user_id INT)
BEGIN
    SELECT 
        Producto_ID, 
        Producto,
        Descripcion, 
        Costo_Base, 
        Cantidad,
        Usuario,
        Imagen,
        Aprobacion,
        Ventas_Count,
        Estatus
    FROM VerMisProd
    WHERE Usuario = (SELECT Nombre_Completo FROM Usuarios WHERE Usuario_ID = user_id);
END //
DELIMITER ;

DELIMITER //
CREATE PROCEDURE GetPopularProducts(
    IN numProducts INT
)
BEGIN
    SELECT 
        Producto_ID, 
        Nombre, 
        Costo_Base, 
        Average_Rating, 
        Foto_1 
    FROM VerPopularProducts
    ORDER BY Average_Rating DESC 
    LIMIT numProducts;
END //
DELIMITER ;

DELIMITER $$
CREATE PROCEDURE GetDestacado()
BEGIN
    SELECT 
        Producto_ID, 
        Nombre, 
        Costo_Base, 
        Average_Rating, 
        Foto_1 
    FROM VerPopularProducts 
    ORDER BY Average_Rating DESC 
    LIMIT 1;
END $$
DELIMITER ;

DELIMITER $$
CREATE PROCEDURE GetProductDetails(IN productID INT)
BEGIN
    SELECT 
        Producto_ID	,
        Nombre,
        Descripcion,
        Video,
        Costo_Base,
        Cantidad,
        Average_Rating,
        Foto_1,
        Foto_2,
        Foto_3,
        Categoria,
        Vendedor,
        Vendedor_ID
    FROM 
        VerProductDetails
    WHERE 
        Producto_ID = productID;
END$$
DELIMITER ;

DELIMITER $$
CREATE PROCEDURE GetProductComments(IN productID INT)
BEGIN
    SELECT 
        Comment_ID, 
        Producto_ID, 
        Producto, 
        Texto, 
        Fecha, 
        Rating, 
        Usuario
    FROM ProductComments
    WHERE Producto_ID = productID AND Com_Estatus = 1 AND Us_Estatus = 1
    ORDER BY Fecha DESC;
END$$
DELIMITER ;

-- Sacar contenidos de un Carrito
DELIMITER //
CREATE PROCEDURE GetCarrito(IN userId INT)
BEGIN
    SELECT 
        Producto_ID,
        Carrito_ID,
        Producto, 
        Cantidad, 
        Precio, 
        Foto_1 
    FROM VerCarrito
    WHERE Usuario_FK = userId;
END //
DELIMITER ;

-- Procedure para agregar un producto al carrito
DELIMITER //

CREATE PROCEDURE AddProductToCart(
    IN userId INT,
    IN productId INT,
    IN quantity INT,
    IN negotiatedPrice DECIMAL(10, 2)
)
BEGIN
    DECLARE cartId INT;
    DECLARE stockQuantity INT;
    DECLARE currentCartQuantity INT;

    -- Ver si Usuario ya tiene un Carrito Activo
    SELECT Carrito_ID INTO cartId 
    FROM Carrito 
    WHERE Usuario_FK = userId AND Estatus = TRUE;

    IF cartId IS NULL THEN
        -- Crear Carrito para el Usuario si no lo tiene
        INSERT INTO Carrito (Usuario_FK) VALUES (userId);
        SET cartId = LAST_INSERT_ID();
    END IF;

    -- Obtener la cantidad en stock del producto
    SELECT Cantidad INTO stockQuantity 
    FROM Producto 
    WHERE Producto_ID = productId;

    -- Obtener la cantidad actual del producto en el carrito
    SELECT COALESCE(SUM(Cantidad), 0) INTO currentCartQuantity 
    FROM Carrito_Producto 
    WHERE Carrito_ID = cartId AND Producto_ID = productId;

    -- Checar si hay suficiente stock para agregar la cantidad solicitada
    IF stockQuantity < (currentCartQuantity + quantity) THEN
        -- Manejar el caso donde no hay suficiente stock (puede ser un error o un mensaje)
        SIGNAL SQLSTATE '45000' SET MESSAGE_TEXT = 'No hay suficiente Cantidad para agregar al carrito.';
    ELSE
        -- Checar si el Producto ya esta en el Carrito
        IF EXISTS (SELECT 1 FROM Carrito_Producto WHERE Carrito_ID = cartId AND Producto_ID = productId) THEN
            -- Si el producto ya esta, entonces subir la cantidad y el Precio negociado (por si le hizo trueque)
            UPDATE Carrito_Producto 
            SET Cantidad = Cantidad + quantity,
                Precio_Negociado = COALESCE(negotiatedPrice, Precio_Negociado)
            WHERE Carrito_ID = cartId AND Producto_ID = productId;
        ELSE
            -- Insertar producto si no esta en el carrito
            INSERT INTO Carrito_Producto (Carrito_ID, Producto_ID, Cantidad, Precio_Negociado) 
            VALUES (cartId, productId, quantity, negotiatedPrice);
        END IF;
    END IF;
END //

DELIMITER ;




-- Hacerle update directo al Precio y Cantidad de un producto en el Carrito
DELIMITER //
CREATE PROCEDURE UpdateProductInCart(
    IN userId INT,
    IN productId INT,
    IN quantity INT,
    IN negotiatedPrice DECIMAL(10, 2)
)
BEGIN
    DECLARE cartId INT;

    SELECT Carrito_ID INTO cartId 
    FROM Carrito 
    WHERE Usuario_FK = userId AND Estatus = TRUE;

    IF cartId IS NOT NULL THEN
        UPDATE Carrito_Producto 
        SET Cantidad = quantity,
            Precio_Negociado = COALESCE(negotiatedPrice, Precio_Negociado)
        WHERE Carrito_ID = cartId AND Producto_ID = productId;
    END IF;
END //
DELIMITER ;

-- Borrar un producto del Carrito
DELIMITER //
CREATE PROCEDURE DeleteProductCart (
    IN p_carrito_id INT,
    IN p_producto_id INT
)
BEGIN
    DELETE FROM Carrito_Producto
    WHERE Carrito_ID = p_carrito_id AND Producto_ID = p_producto_id;
END //
DELIMITER ;

-- Borrar todo de un carrito
DELIMITER //
CREATE PROCEDURE DeleteAllProductCart (
    IN p_carrito_id INT
)
BEGIN
    DELETE FROM Carrito_Producto
    WHERE Carrito_ID = p_carrito_id;
END //
DELIMITER ;

-- Crear una Categoria
DELIMITER //
CREATE PROCEDURE AddCategory (
    IN p_Nombre VARCHAR(50),
    IN p_Usuario_Creador_FK INT
)
BEGIN
    INSERT INTO Categoria (Nombre, Usuario_Creador_FK, Estatus)
    VALUES (p_Nombre, p_Usuario_Creador_FK, DEFAULT);
END //
DELIMITER ;

-- Obtener informacion de categoria 
DELIMITER //
CREATE PROCEDURE GetCategories ()
BEGIN
    SELECT 
        Categoria_ID, 
        Nombre, 
        Usuario_Creador, 
        Estatus
    FROM 
        VerCategories
    WHERE 
        Estatus = TRUE; 
END //
DELIMITER ;


-- Obtener informacion de categoria 
DELIMITER //
CREATE PROCEDURE DeleteCategory (
    IN p_categoria_id INT
)
BEGIN
    UPDATE Categoria
    SET Estatus = FALSE
    WHERE Categoria_ID = p_categoria_id;
END //
DELIMITER ;

-- Crear una Lista
DELIMITER //
CREATE PROCEDURE InsertLista (
    IN p_nombre VARCHAR(100),
    IN p_descripcion TEXT,
    IN p_foto LONGBLOB,
    IN p_usuario_fk INT
)
BEGIN
    INSERT INTO Listas (Nombre, Descripcion, Foto, Usuario_FK)
    VALUES (p_nombre, p_descripcion, p_foto, p_usuario_fk);
END //
DELIMITER ;

-- Insertar Producto a Lista
DELIMITER //
CREATE PROCEDURE InsertProdLista (
    IN p_listas_id INT,
    IN p_producto_id INT
)
BEGIN
    -- Funcion para checar si el producto esta en la lista
    IF NOT IsProductInList(p_listas_id, p_producto_id) THEN
        INSERT INTO Listas_Productos (Listas_ID, Producto_ID)
        VALUES (p_listas_id, p_producto_id);
    END IF;
END //
DELIMITER ;

-- Conseguir todas las listas creadas por un Usuario
DELIMITER //
CREATE PROCEDURE GetListas(
    IN p_user_id INT
)
BEGIN
    SELECT 
        Listas_ID,
        Nombre,
        Descripcion,
        Foto,
        Usuario,
        Estatus
    FROM 
        VerListas
    WHERE 
        Usuario_ID = p_user_id AND Estatus = 1;
END //
DELIMITER ;

-- Informacion de una lista especifica
DELIMITER //
CREATE PROCEDURE GetListInfo(
    IN p_list_id INT
)
BEGIN
    SELECT 
        Listas_ID,
        Nombre,
        Descripcion,
        Foto,
        Usuario,
        Estatus
    FROM 
        VerListas
    WHERE 
        Listas_ID = p_list_id;
END //
DELIMITER ;

-- Conseguir la info de todos los productos de un a Lista especifica
DELIMITER //
CREATE PROCEDURE GetProductoListas(
    IN p_list_id INT
)
BEGIN
    SELECT 
        Listas_ID,
        Nombre_Lista,
        Producto_ID,
        Nombre_Producto,
        Descripcion_Producto,
        Costo_Base,
        Cantidad,
        Average_Rating,
        Foto_Principal,
        Categoria,
        Vendedor
    FROM 
        VerProductoEnLista
    WHERE 
        Listas_ID = p_list_id AND Estatus = TRUE;
END //
DELIMITER ;

-- BUSQUEDA DE UN PRODUCTO
DELIMITER //
CREATE PROCEDURE GetProductoBuscar(
    IN searchTerm VARCHAR(255),
    IN categoriaID INT
)
BEGIN
    SELECT 
        Producto_ID,
        Producto,
        Descripcion,
        Precio,
        Cantidad,
        Categoria,
        Vendedor,
        Average_Rating,
        Foto,
        Cantidad_Vendida
    FROM ProductoBuscar
    
    WHERE (Producto LIKE CONCAT('%', searchTerm, '%')
        OR Descripcion LIKE CONCAT('%', searchTerm, '%')
        OR Vendedor LIKE CONCAT('%', searchTerm, '%'))
    AND (categoriaID IS NULL OR Categoria_ID = categoriaID)    
    ORDER BY Average_Rating DESC ;
END //
DELIMITER ;

-- Borrar un producto de una Lista
DELIMITER //
CREATE PROCEDURE DeleteProdLista(IN listaID INT, IN productoID INT)
BEGIN
    DELETE FROM Listas_Productos
    WHERE Listas_ID = listaID AND Producto_ID = productoID;
END//
DELIMITER ;

-- Borrar lista entera con todos sus productos
DELIMITER //
CREATE PROCEDURE DeleteLista(IN listaID INT)
BEGIN
    START TRANSACTION;
    DELETE FROM Listas_Productos WHERE Listas_ID = listaID;
    DELETE FROM Listas WHERE Listas_ID = listaID;    
    COMMIT;
END//
DELIMITER ;

-- Borrar producto de manera logica
DELIMITER //
CREATE PROCEDURE DeleteProducto(IN productoID INT)
BEGIN
    UPDATE Producto
    SET Estatus = FALSE
    WHERE Producto_ID = productoID;
END//
DELIMITER ;

-- Recuperar producto de manera logica
DELIMITER //
CREATE PROCEDURE RecoverProducto(IN productoID INT)
BEGIN
    UPDATE Producto
    SET Estatus = TRUE
    WHERE Producto_ID = productoID;
END//
DELIMITER ;

-- Consegir la informacion del Chat
DELIMITER $$
CREATE PROCEDURE GetChat(
    IN p_Comprador_ID INT,
    IN p_Producto_ID INT
)
BEGIN
    SELECT 
        Chat_ID,
        Vendedor_ID,
        Vendedor_Username,
        Comprador_ID,
        Comprador_Username,
        Producto_ID,
        Producto_Nombre,
        Precio_Acordado,
        Fecha,
        Estatus
    FROM 
        VerChat
    WHERE 
        Comprador_ID = p_Comprador_ID
        AND Producto_ID = p_Producto_ID
        AND Estatus = TRUE;
END$$
DELIMITER ;

-- Consegir la informacion del Chat usando el Chat
DELIMITER $$
CREATE PROCEDURE GetChatdelChat(
    IN p_Chat_ID INT
)
BEGIN
    SELECT 
        Chat_ID,
        Vendedor_ID,
        Vendedor_Username,
        Comprador_ID,
        Comprador_Username,
        Producto_ID,
        Producto_Nombre,
        Producto_Foto,
        Precio_Acordado,
        Fecha,
        Estatus
    FROM 
        VerChat
    WHERE 
        Chat_ID = p_Chat_ID
        AND Estatus = TRUE;
END$$
DELIMITER ;

-- Creacion de Chat
DELIMITER $$
CREATE PROCEDURE InsertChat(
    IN p_Comprador_ID INT,
    IN p_Producto_ID INT
)
BEGIN
    DECLARE v_Vendedor_ID INT;
    DECLARE v_Precio_Acordado DECIMAL(10, 2);

    -- Sacar el ID del Vendedor
    SELECT Usuario_FK INTO v_Vendedor_ID
    FROM Producto
    WHERE Producto_ID = p_Producto_ID;

    -- Hacer el Precio base temporalmente el Precio Acordado
    SELECT Costo_Base INTO v_Precio_Acordado
    FROM Producto
    WHERE Producto_ID = p_Producto_ID;

    -- Crear el Chat Nuevo
    INSERT INTO Chat (Vendedor_ID, Comprador_ID, Producto_ID, Precio_Acordado)
    VALUES (v_Vendedor_ID, p_Comprador_ID, p_Producto_ID, v_Precio_Acordado);
END$$
DELIMITER ;

-- Hacer que un Usuario Mandae un Mensaje a un Chat
DELIMITER $$
CREATE PROCEDURE InsertMessage(
    IN p_Chat_ID INT,
    IN p_Usuario_ID INT,
    IN p_Texto TEXT
)
BEGIN
    INSERT INTO Mensajes (Chat_ID, Usuario_ID, Texto)
    VALUES (p_Chat_ID, p_Usuario_ID, p_Texto);
END$$
DELIMITER ;

-- Conseguir los Mensajes de un Chat
DELIMITER $$
CREATE PROCEDURE GetMessage(
    IN p_Chat_ID INT
)
BEGIN
    SELECT Mensaje_ID, Chat_ID, Usuario_ID, Texto, Fecha
    FROM Mensajes
    WHERE Chat_ID = p_Chat_ID
      AND Estatus = TRUE;
END$$
DELIMITER ;

-- Consegir todos los chats de un Usuario
DELIMITER $$
CREATE PROCEDURE GetChatporUser(
    IN p_User_ID INT
)
BEGIN
    SELECT 
        Chat_ID,
        Vendedor_ID,
        Vendedor_Username,
        Comprador_ID,
        Comprador_Username,
        Producto_ID,
        Producto_Nombre,
        Producto_Foto,
        Precio_Acordado,
        Fecha,
        Estatus
    FROM 
        VerChat
    WHERE 
        Vendedor_ID = p_User_ID
        AND Estatus = TRUE;
END$$
DELIMITER ;

-- Modificar el precio acordado en el Chat
DELIMITER $$
CREATE PROCEDURE ModifChatPrecio(
    IN p_Chat_ID INT,
    IN p_NuevoPrecio DECIMAL(10, 2)
)
BEGIN
    UPDATE Chat
    SET Precio_Acordado = p_NuevoPrecio
    WHERE Chat_ID = p_Chat_ID;
END$$
DELIMITER ;

-- REALIZAR UNA VENTA
DELIMITER //
CREATE PROCEDURE RealizarVenta(IN cartID INT)
BEGIN
    DECLARE productID INT;
    DECLARE productPrice DECIMAL(10, 2);
    DECLARE productQuantity INT;
    DECLARE done INT DEFAULT FALSE;

    -- Usar el VerCarrito 
    DECLARE cur CURSOR FOR 
        SELECT Producto_ID, Precio, Cantidad 
        FROM VerCarrito 
        WHERE Carrito_ID = cartID;

    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;
    OPEN cur;

    -- Loop 
    read_loop: LOOP
        FETCH cur INTO productID, productPrice, productQuantity;

        IF done THEN
            LEAVE read_loop;
        END IF;

        -- Vender producto uno por uno
        WHILE productQuantity > 0 DO
            INSERT INTO Venta (Precio, Productos_FK, Comprador_FK)
            VALUES (productPrice, productID, (SELECT Usuario_FK FROM Carrito WHERE Carrito_ID = cartID));

            -- Restar 1 del inventario
            UPDATE Producto
            SET Cantidad = Cantidad - 1
            WHERE Producto_ID = productID;

            SET productQuantity = productQuantity - 1;
        END WHILE;
    END LOOP;

    CLOSE cur;

    -- Clear the cart
    DELETE FROM Carrito_Producto WHERE Carrito_ID = cartID;
END //


DELIMITER ;

-- Ver las ventas realizadas por el usuario
DELIMITER //
CREATE PROCEDURE GetMisVentasAgrupadas(
    IN currentUserId INT
)
BEGIN
    SELECT
        Producto_ID,
        Producto_Nombre,
        Vendedor_ID,
        Vendedor_Username,
        Precio,
        Cantidad_Vendida,
        Total_Ganancias,
        Comprador_ID,
        Comprador_Username,
        Fecha,
        Foto_1,
        Estatus
    FROM VerVentasAgrupadas
    WHERE Vendedor_ID = currentUserId;
END //
DELIMITER ;

-- Ver las Compras realizadas por el usuario
DELIMITER //
CREATE PROCEDURE GetMisComprasAgrupadas(
    IN p_user_id INT
)
BEGIN
    SELECT
        Producto_ID,
        Producto_Nombre,
        Vendedor_ID,
        Vendedor_Username,
        Precio,
        Cantidad_Vendida,
        Total_Ganancias,
        Comprador_ID,
        Comprador_Username,
        Fecha,
        Foto_1,
        Estatus
    FROM
        VerVentasAgrupadas
    WHERE
        Comprador_ID = p_user_id;
END //
DELIMITER ;

-- EDITAR un producto
DELIMITER //
CREATE PROCEDURE EditarProducto (
    IN productoId INT,
    IN nombreProducto VARCHAR(100),
    IN descripcionProducto TEXT,
    IN costoBase DECIMAL(10, 2),
    IN cantidad INT,
    IN categoriaFK INT,
    IN invideo LONGBLOB
)
BEGIN
    UPDATE Producto
    SET
        Nombre = nombreProducto,
        Descripcion = descripcionProducto,
        Costo_Base = costoBase,
        Cantidad = cantidad,
        Categoria_FK = categoriaFK,
        Video = invideo
    WHERE
        Producto_ID = productoId;
END //
DELIMITER ;

-- Obtener toda la info de un Producto
DELIMITER //
CREATE PROCEDURE GetProductoRaw (
    IN productoId INT
)
BEGIN
    SELECT Producto_ID, Nombre, Descripcion, Video, Costo_Base, Cantidad, Categoria_FK, Fotos_FK
    FROM Producto
    WHERE Producto_ID = productoId;
END //
DELIMITER ;

-- Obtener las 3 imagenes por su ID 
DELIMITER //
CREATE PROCEDURE GetFotosRaw (
    IN fotosId INT
)
BEGIN
    SELECT Fotos_ID, Foto_1, Foto_2, Foto_3
    FROM Fotos
    WHERE Fotos_ID = fotosId;
END //
DELIMITER ;

-- Reemplazar alguna o todas las imagenes
DELIMITER //
CREATE PROCEDURE CambiarFoto (
    IN fotosId INT,
    IN foto1 LONGBLOB,
    IN foto2 LONGBLOB,
    IN foto3 LONGBLOB
)
BEGIN
    UPDATE Fotos
    SET
        Foto_1 = foto1,
        Foto_2 = foto2,
        Foto_3 = foto3
    WHERE
        Fotos_ID = fotosId;
END //
DELIMITER ;

-- Obtener el video de un Producto
DELIMITER //
CREATE PROCEDURE GetProductoVideo (
    IN productoId INT
)
BEGIN
    SELECT Video
    FROM Producto
    WHERE Producto_ID = productoId;
END //
DELIMITER ;

