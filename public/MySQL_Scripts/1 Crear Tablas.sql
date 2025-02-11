USE fantazon;

DROP TABLE IF EXISTS Carrito_Producto;
DROP TABLE IF EXISTS Comments;
DROP TABLE IF EXISTS Mensajes;
DROP TABLE IF EXISTS Chat;
DROP TABLE IF EXISTS Listas_Productos;
DROP TABLE IF EXISTS Listas;
DROP TABLE IF EXISTS Venta;
DROP TABLE IF EXISTS Producto;
DROP TABLE IF EXISTS Categoria;
DROP TABLE IF EXISTS Carrito;
DROP TABLE IF EXISTS Usuarios;
DROP TABLE IF EXISTS Fotos;

-- Usuarios
CREATE TABLE Usuarios (
    Usuario_ID INT AUTO_INCREMENT PRIMARY KEY,
    Username VARCHAR(50) UNIQUE NOT NULL,
    Nombre_Completo VARCHAR(100) NOT NULL,
    Mail VARCHAR(100) UNIQUE NOT NULL,
    Contraseña VARCHAR(100) NOT NULL,
    Sexo ENUM('Masculino', 'Femenino', 'Otro') NOT NULL,
    Rol ENUM('Comprador', 'Vendedor', 'Admin') NOT NULL,
    Foto LONGBLOB,  
    Fecha_Nac DATE,
    Fecha_Ing TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    Estatus BOOLEAN DEFAULT TRUE
);

-- Fotos
CREATE TABLE Fotos (
    Fotos_ID INT AUTO_INCREMENT PRIMARY KEY,
    Foto_1 LONGBLOB,
    Foto_2 LONGBLOB,
    Foto_3 LONGBLOB,
    Estatus BOOLEAN DEFAULT TRUE
);

-- Categoría
CREATE TABLE Categoria (
    Categoria_ID INT AUTO_INCREMENT PRIMARY KEY,
    Nombre VARCHAR(50) NOT NULL,
    Usuario_Creador_FK INT NOT NULL,
    Estatus BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (Usuario_Creador_FK) REFERENCES Usuarios(Usuario_ID)
);

-- Productos
CREATE TABLE Producto (
    Producto_ID INT AUTO_INCREMENT PRIMARY KEY,
    Nombre VARCHAR(100) NOT NULL,
    Descripcion TEXT,
    Video LONGBLOB,
    Costo_Base DECIMAL(10, 2) NOT NULL,
    Cantidad INT,
    Fotos_FK INT,
    Usuario_FK INT,
    Categoria_FK INT NOT NULL,
    Aprobado BOOLEAN DEFAULT FALSE,
    Estatus BOOLEAN DEFAULT TRUE,
    Average_Rating DECIMAL(3, 2) DEFAULT 0.00,
    FOREIGN KEY (Fotos_FK) REFERENCES Fotos(Fotos_ID),
    FOREIGN KEY (Usuario_FK) REFERENCES Usuarios(Usuario_ID),
    FOREIGN KEY (Categoria_FK) REFERENCES Categoria(Categoria_ID)
);

-- Ventas
CREATE TABLE Venta (
    Venta_ID INT AUTO_INCREMENT PRIMARY KEY,
    Precio DECIMAL(10, 2) NOT NULL,
    Productos_FK INT,
    Comprador_FK INT,
    Fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    Estatus BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (Productos_FK) REFERENCES Producto(Producto_ID),
    FOREIGN KEY (Comprador_FK) REFERENCES Usuarios(Usuario_ID)
);

-- Listas
CREATE TABLE Listas (
    Listas_ID INT AUTO_INCREMENT PRIMARY KEY,
    Nombre VARCHAR(100),
    Descripcion TEXT,
    Foto LONGBLOB,
    Usuario_FK INT,
    Estatus BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (Usuario_FK) REFERENCES Usuarios(Usuario_ID)
);

-- Categorizamiento de productos por Lista
CREATE TABLE Listas_Productos (
    Listas_ID INT NOT NULL,
    Producto_ID INT NOT NULL,
    Estatus BOOLEAN DEFAULT TRUE,
    PRIMARY KEY (Listas_ID, Producto_ID),
    FOREIGN KEY (Listas_ID) REFERENCES Listas(Listas_ID),
    FOREIGN KEY (Producto_ID) REFERENCES Producto(Producto_ID)
);

-- Chat
CREATE TABLE Chat (
    Chat_ID INT AUTO_INCREMENT PRIMARY KEY,
    Vendedor_ID INT NOT NULL,
    Comprador_ID INT NOT NULL,
    Producto_ID INT NOT NULL,
    Precio_Acordado DECIMAL(10, 2),
    Fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    Estatus BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (Vendedor_ID) REFERENCES Usuarios(Usuario_ID),
    FOREIGN KEY (Comprador_ID) REFERENCES Usuarios(Usuario_ID),
    FOREIGN KEY (Producto_ID) REFERENCES Producto(Producto_ID)
);

-- Mensajes
CREATE TABLE Mensajes (
    Mensaje_ID INT AUTO_INCREMENT PRIMARY KEY,
    Chat_ID INT NOT NULL,
    Usuario_ID INT NOT NULL,
    Texto TEXT NOT NULL,
    Fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    Estatus BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (Chat_ID) REFERENCES Chat(Chat_ID),
    FOREIGN KEY (Usuario_ID) REFERENCES Usuarios(Usuario_ID)
);

-- Comments
CREATE TABLE Comments (
    Comment_ID INT AUTO_INCREMENT PRIMARY KEY,
    Texto TEXT NOT NULL,
    Fecha TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    Producto_FK INT NOT NULL,
    Usuario_FK INT NOT NULL,
    Rating INT CHECK (Rating >= 1 AND Rating <= 5),
    Estatus BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (Producto_FK) REFERENCES Producto(Producto_ID),
    FOREIGN KEY (Usuario_FK) REFERENCES Usuarios(Usuario_ID)
);

-- Carritos de Compras
CREATE TABLE Carrito (
    Carrito_ID INT AUTO_INCREMENT PRIMARY KEY,
    Usuario_FK INT,
    Estatus BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (Usuario_FK) REFERENCES Usuarios(Usuario_ID)
);

-- Productos en el Carrito de Compras
CREATE TABLE Carrito_Producto (
    Carrito_ID INT NOT NULL,
    Producto_ID INT NOT NULL,
    Cantidad INT NOT NULL,
    Precio_Negociado DECIMAL(10, 2) DEFAULT NULL,
    Estatus BOOLEAN DEFAULT TRUE,
    PRIMARY KEY (Carrito_ID, Producto_ID),
    FOREIGN KEY (Carrito_ID) REFERENCES Carrito(Carrito_ID),
    FOREIGN KEY (Producto_ID) REFERENCES Producto(Producto_ID)
);

CREATE INDEX idx_usuario_username ON Usuarios(Username);
CREATE INDEX idx_producto_usuario_fk ON Producto(Usuario_FK);
CREATE INDEX idx_chat_usuario1 ON Chat(Vendedor_ID);
CREATE INDEX idx_chat_usuario2 ON Chat(Comprador_ID);
CREATE INDEX idx_chat_producto ON Chat(Producto_ID);
CREATE INDEX idx_Mensajes_chat ON Mensajes(Chat_ID);
CREATE INDEX idx_comments_producto ON Comments(Producto_FK);
CREATE INDEX idx_comments_usuario ON Comments(Usuario_FK);
