USE fantazon;

-- Siempre cargar los siguientes

INSERT INTO Usuarios (Username, Nombre_Completo, Mail, Contraseña, Sexo, Rol, Fecha_Nac)
VALUES ('Rock', 'Rock', 'rock@gmail.com', 'Rock', 'Masculino', 'Comprador', '2000-05-13');

INSERT INTO Usuarios (Username, Nombre_Completo, Mail, Contraseña, Sexo, Rol, Fecha_Nac)
VALUES ('Vendedor', 'Vendedor', 'vende@gmail.com', 'V', 'Masculino', 'Vendedor', '2000-05-13');

INSERT INTO Usuarios (Username, Nombre_Completo, Mail, Contraseña, Sexo, Rol, Fecha_Nac)
VALUES ('Admin', 'Admin', 'admin@gmail.com', 'A', 'Femenino', 'Admin', '2000-05-13');

CALL AddCategory('Magicos', 3);
CALL AddCategory('Libros', 3);
CALL AddCategory('Muebles', 3);
CALL AddCategory('Accesorios', 3);
CALL AddCategory('Armas', 3);




UPDATE Usuarios
    SET 
        Rol = 'Admin'
    WHERE Usuario_ID = 3;



CALL GetCategories();
CALL GetUserInfo();
SELECT * FROM VerUserInfo;
SELECT * FROM Usuarios;

INSERT INTO Fotos (Foto_1, Foto_2, Foto_3)
VALUES (NULL, NULL, NULL);

INSERT INTO Fotos (Foto_1, Foto_2, Foto_3) VALUES (LOAD_FILE('C:\\Users\\Owner\\Pictures\\PROFILE CONTENT\\bep.jpg'), NULL, NULL);
SELECT * FROM Fotos;
SELECT 'File Path:', 'C:\\Users\\Owner\\Pictures\\PROFILE CONTENT\\bepito.png';
SELECT Foto_1 FROM Fotos;


SELECT * FROM Producto WHERE Aprobado = TRUE ORDER BY Average_Rating DESC LIMIT 1;

CALL InsertProducto('Ejemplo', 'Descripcion del producto bla bla bla bla bla', NULL, 100.0, 10, 1, 3,1);
SELECT * FROM VerMisProd;

CALL AprovarProducto(1);
SELECT * FROM Producto;


SELECT * FROM VerMisProd;

CALL GetPorAprovar();
CALL GetMisProd(3);


CALL AddComment('WOW QUE PRODUCTAZO!', 5, 1, 1);
CALL AddComment('Que producto bien basura', 5, 1, 2);
CALL AddComment('Alv olvide ponerle un 1', 1, 1, 2);


CALL AddComment('WOW QUE PRODUCTAZO!', 5, 2, 1);
CALL AddComment('WOW QUE PRODUCTAZO!', 5, 3, 3);

CALL GetPopularProducts(5);
CALL GetDestacado();

CALL GetProductDetails(1);

SELECT * FROM ProductComments;
CALL GetProductComments(1);

SELECT * FROM Carrito;
SELECT * FROM VerCarrito;
CALL GetCarrito(3);
TRUNCATE TABLE Carrito_Producto;

CALL InsertLista('Lista de Prueba', 'aaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaaa', NULL, 1);
CALL InsertProdLista(3, 4);

SELECT * FROM VerListas;
SELECT * FROM Listas_Productos;
SELECT * FROM VerProductoEnLista;


CALL GetListInfo(1);
CALL GetProductoListas(3);

TRUNCATE TABLE Listas_Productos;

CALL GetProductoBuscar('Ejemplo', 1);

CALL DeleteProductCart (1,1);
CALL GetCarrito(3);

SELECT * FROM VerChat;
CALL GetChat (1,1);


SELECT * FROM Carrito_Producto WHERE Carrito_ID = 2;

SELECT * FROM VerVentasAgrupadas;

CALL RealizarVenta (2);
SELECT * FROM Venta;
SELECT * FROM VerMisProd;
