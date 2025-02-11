USE fantazon;

-- Vista para la informacion de un Usuario
DROP VIEW IF EXISTS VerUserInfo;
CREATE VIEW VerUserInfo AS
SELECT 
    Usuario_ID, Username, Nombre_Completo, Mail, Sexo, Rol, Fecha_Nac, Foto, Fecha_Ing, 
    CASE 
        WHEN Estatus = 1 THEN 'Activo'
        WHEN Estatus = 0 THEN 'Inactivo'
        ELSE 'Desconocido'
    END AS Estatus
FROM Usuarios;

-- Vista para TODA la informacion de un Usuario
DROP VIEW IF EXISTS VerAllUserInfo;
CREATE VIEW VerAllUserInfo AS
SELECT 
    Usuario_ID, Username, Nombre_Completo, Mail, Contraseña, Sexo, Rol, Fecha_Nac, Foto, Fecha_Ing, 
    CASE 
        WHEN Estatus = 1 THEN 'Activo'
        WHEN Estatus = 0 THEN 'Inactivo'
        ELSE 'Desconocido'
    END AS Estatus
FROM Usuarios;

-- Vista para la info de Categorias
DROP VIEW IF EXISTS VerCategories;
CREATE VIEW VerCategories AS
SELECT 
    c.Categoria_ID, 
    c.Nombre, 
    u.Username AS Usuario_Creador, 
    c.Estatus
FROM 
    Categoria c
JOIN 
    Usuarios u ON c.Usuario_Creador_FK = u.Usuario_ID
WHERE 
    c.Estatus = 1;


-- Vista para todos los comentarios
DROP VIEW IF EXISTS ProductComments;
CREATE VIEW ProductComments AS
SELECT 
    c.Comment_ID, 
    c.Producto_FK AS Producto_ID, 
    p.Nombre AS Producto, 
    c.Texto, 
    DATE(c.Fecha) AS Fecha, 
    c.Rating, 
    u.Username AS Usuario,
    c.Estatus AS Com_Estatus,
    u.Estatus AS Us_Estatus
FROM Comments c
JOIN Usuarios u ON c.Usuario_FK = u.Usuario_ID
JOIN Producto p ON c.Producto_FK = p.Producto_ID;

-- Vista para todos los Productos que faltan por aprovar, de quien es el producto y las primera foto
DROP VIEW IF EXISTS VerPorAprovar;
CREATE VIEW VerPorAprovar AS
SELECT p.Producto_ID, 
       p.Nombre 			AS Producto,
       p.Descripcion, 
       p.Costo_Base 		AS Costo_Base, 
       p.Cantidad,
       u.Nombre_Completo 	AS Usuario,
       f.Foto_1 			AS Imagen,
       p.Estatus
FROM Producto p
JOIN Usuarios u ON p.Usuario_FK = u.Usuario_ID
JOIN Fotos f ON p.Fotos_FK = f.Fotos_ID
WHERE p.Aprobado = 0 AND u.Estatus = TRUE;

-- Vista para que cada vendedor pueda ver sus productos
DROP VIEW IF EXISTS VerMisProd;
CREATE VIEW VerMisProd 		AS
SELECT p.Producto_ID, 
       p.Nombre 			AS Producto,
       p.Descripcion, 
       p.Costo_Base 		AS Costo_Base, 
       p.Cantidad,
       u.Nombre_Completo 	AS Usuario,
       f.Foto_1 			AS Imagen,
       CASE 
           WHEN p.Aprobado = 1 THEN 'Aprobado'
           ELSE 'Desaprobado'
       END AS Aprobacion,
       CASE 
        WHEN p.Estatus = 1 THEN 'Activo'
        WHEN p.Estatus = 0 THEN 'Inactivo'
        ELSE 'Desconocido'
    END AS Estatus,
	IFNULL(v.Ventas_Count, 0) AS Ventas_Count
FROM Producto p
JOIN Usuarios u ON p.Usuario_FK = u.Usuario_ID
JOIN Fotos f ON p.Fotos_FK = f.Fotos_ID
LEFT JOIN 
    (SELECT Productos_FK, COUNT(*) AS Ventas_Count
     FROM Venta
     GROUP BY Productos_FK) v ON p.Producto_ID = v.Productos_FK;
     
     
-- Vista para ver los productos mas populares
DROP VIEW IF EXISTS VerPopularProducts;
CREATE VIEW VerPopularProducts AS
SELECT 
    p.Producto_ID, 
    p.Nombre, 
    p.Costo_Base, 
    p.Average_Rating, 
    f.Foto_1
FROM Producto p
INNER JOIN Fotos f ON p.Fotos_FK = f.Fotos_ID
WHERE p.Aprobado = TRUE AND p.Estatus = TRUE;

-- Vista para ver los articulos del carrito
DROP VIEW IF EXISTS VerCarrito;
CREATE VIEW VerCarrito AS
SELECT 
    c.Carrito_ID,
    c.Usuario_FK,
    cp.Producto_ID, 
    p.Nombre AS Producto, 
    cp.Cantidad, 
    COALESCE(cp.Precio_Negociado, p.Costo_Base) AS Precio,
    f.Foto_1 
FROM Carrito c
JOIN Carrito_Producto cp ON c.Carrito_ID = cp.Carrito_ID
JOIN Producto p ON cp.Producto_ID = p.Producto_ID
JOIN Fotos f ON p.Fotos_FK = f.Fotos_ID
WHERE c.Estatus = TRUE;

-- Vista para ver los detalles de un producto
DROP VIEW IF EXISTS VerProductDetails;
CREATE VIEW VerProductDetails AS
SELECT 
    p.Producto_ID,
    p.Nombre 			AS Nombre			,
    p.Descripcion 		AS Descripcion		,
    p.Video 			AS Video			,
    p.Costo_Base 		AS Costo_Base		,
    p.Cantidad 			AS Cantidad			,
    p.Average_Rating 	AS Average_Rating	,
    f.Foto_1 			AS Foto_1			,
    f.Foto_2 			AS Foto_2			,
    f.Foto_3 			AS Foto_3			,
    c.Nombre 			AS Categoria		,
    u.Nombre_Completo 	AS Vendedor			,
    u.Usuario_ID		AS Vendedor_ID
FROM 
    Producto p
JOIN 
    Fotos f ON p.Fotos_FK = f.Fotos_ID
JOIN 
    Categoria c ON p.Categoria_FK = c.Categoria_ID
JOIN 
    Usuarios u ON p.Usuario_FK = u.Usuario_ID;
    


-- Vista ver las Listas
DROP VIEW IF EXISTS VerListas;
CREATE VIEW VerListas AS
SELECT 
    l.Listas_ID,
    l.Nombre,
    l.Descripcion,
    l.Foto,
    u.Usuario_ID,
    u.Username AS Usuario,
    l.Estatus
FROM 
    Listas l
JOIN 
    Usuarios u ON l.Usuario_FK = u.Usuario_ID;

-- Vista ver las Listas
DROP VIEW IF EXISTS VerProductoEnLista;
CREATE VIEW VerProductoEnLista AS
SELECT 
    lp.Listas_ID,
    l.Nombre 			AS Nombre_Lista,
    p.Producto_ID		,
    p.Nombre 			AS Nombre_Producto,
    p.Descripcion 		AS Descripcion_Producto,
    p.Costo_Base 		AS Costo_Base,
    p.Cantidad 			AS Cantidad,
    p.Average_Rating 	AS Average_Rating,
	f.Foto_1 AS Foto_Principal,
    c.Nombre 			AS Categoria,
    u.Nombre_Completo 	AS Vendedor,
    p.Estatus			AS Estatus
FROM 
    Listas_Productos lp
JOIN 
    Listas l ON lp.Listas_ID = l.Listas_ID
JOIN 
    Producto p ON lp.Producto_ID = p.Producto_ID
JOIN 
    Categoria c ON p.Categoria_FK = c.Categoria_ID
JOIN 
    Usuarios u ON p.Usuario_FK = u.Usuario_ID
JOIN 
    Fotos f ON p.Fotos_FK = f.Fotos_ID;


-- Mi vista de busqueda de productos
DROP VIEW IF EXISTS ProductoBuscar;
CREATE VIEW ProductoBuscar AS
SELECT 
    P.Producto_ID,
    P.Nombre AS Producto,
    P.Descripcion,
    P.Costo_Base AS Precio,
    P.Cantidad AS Cantidad,
    COALESCE(SUM(V.Cantidad_Vendida), 0) AS Cantidad_Vendida,
    C.Nombre AS Categoria,
    C.Categoria_ID,
    U.Username AS Vendedor,
    P.Average_Rating AS Average_Rating,
    F.Foto_1 AS Foto
FROM 
    Producto P
INNER JOIN 
    Categoria C ON P.Categoria_FK = C.Categoria_ID
INNER JOIN 
    Usuarios U ON P.Usuario_FK = U.Usuario_ID
LEFT JOIN 
    Fotos F ON P.Fotos_FK = F.Fotos_ID 
LEFT JOIN
    VerVentasAgrupadas V ON P.Producto_ID = V.Producto_ID
WHERE 
    P.Estatus = TRUE AND P.Aprobado = TRUE
GROUP BY 
    P.Producto_ID
ORDER BY 
    Cantidad_Vendida DESC; 



-- Mi vista de busqueda de productos
DROP VIEW IF EXISTS VerChat;
CREATE VIEW VerChat AS
SELECT 
    c.Chat_ID,
    c.Vendedor_ID,
    v.Username AS Vendedor_Username,
    c.Comprador_ID,
    comprador.Username AS Comprador_Username,
    c.Producto_ID,
    p.Nombre AS Producto_Nombre,
    f.Foto_1 AS Producto_Foto,
    c.Precio_Acordado,
    c.Fecha,
    c.Estatus
FROM 
    Chat c
JOIN 
    Usuarios v ON c.Vendedor_ID = v.Usuario_ID
JOIN 
    Usuarios comprador ON c.Comprador_ID = comprador.Usuario_ID
JOIN 
    Producto p ON c.Producto_ID = p.Producto_ID
JOIN 
    Fotos f ON p.Fotos_FK = f.Fotos_ID;


-- Mi vista de Ventas
DROP VIEW IF EXISTS VerVentas;
CREATE VIEW VerVentas AS
SELECT
    V.Venta_ID,
    V.Precio,
    V.Productos_FK AS Producto_ID,
    P.Nombre AS Producto_Nombre,
    U1.Usuario_ID AS Comprador_ID,
    U1.Username AS Comprador_Username,
    U2.Usuario_ID AS Vendedor_ID,
    U2.Username AS Vendedor_Username,
    V.Fecha,
    V.Estatus
FROM
    Venta V
INNER JOIN Usuarios U1 ON V.Comprador_FK = U1.Usuario_ID
INNER JOIN Producto P ON V.Productos_FK = P.Producto_ID
INNER JOIN Usuarios U2 ON P.Usuario_FK = U2.Usuario_ID;

DROP VIEW IF EXISTS VerVentasAgrupadas;
CREATE VIEW VerVentasAgrupadas AS
SELECT
    P.Producto_ID,
    P.Nombre AS Producto_Nombre,
    U2.Usuario_ID AS Vendedor_ID,
    U2.Username AS Vendedor_Username,
    V.Precio,
    COUNT(V.Venta_ID) AS Cantidad_Vendida,
    SUM(V.Precio) AS Total_Ganancias, 
    U1.Usuario_ID AS Comprador_ID,
    U1.Username AS Comprador_Username,
    V.Fecha,
    F.Foto_1,
    V.Estatus
FROM
    Venta V
INNER JOIN Usuarios U1 ON V.Comprador_FK = U1.Usuario_ID
INNER JOIN Producto P ON V.Productos_FK = P.Producto_ID
INNER JOIN Usuarios U2 ON P.Usuario_FK = U2.Usuario_ID
LEFT JOIN 
    Fotos F ON P.Fotos_FK = F.Fotos_ID 
GROUP BY
    P.Producto_ID,
    P.Nombre,
    U2.Usuario_ID,
    U2.Username,
    V.Precio,
    U1.Usuario_ID,
    U1.Username,
    V.Fecha,
    V.Estatus
    ORDER BY 
    Fecha DESC; 
