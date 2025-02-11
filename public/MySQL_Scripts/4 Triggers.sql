use fantazon;

DROP TRIGGER IF EXISTS AfterCommentInsert;
DROP TRIGGER IF EXISTS AfterCommentUpdate;
DROP TRIGGER IF EXISTS DesaprovProdalBorrarUser;
DROP TRIGGER IF EXISTS RemoveProdMalosdelCarrito;


-- Triggers para cuando Comentarios hagan cambios al rating
DELIMITER //
CREATE TRIGGER AfterCommentInsert
AFTER INSERT ON Comments
FOR EACH ROW
BEGIN
    DECLARE new_avg_rating DECIMAL(3, 2);
    SET new_avg_rating = GetAverageRating(NEW.Producto_FK);
    UPDATE Producto
    SET Average_Rating = new_avg_rating
    WHERE Producto_ID = NEW.Producto_FK;
END //
DELIMITER ;

-- Esto es si se hace Update al rating de un comentario (dudo que lo use pero por si las moscas)
DELIMITER //
CREATE TRIGGER AfterCommentUpdate
AFTER UPDATE ON Comments
FOR EACH ROW
BEGIN
    DECLARE new_avg_rating DECIMAL(3, 2);
    SET new_avg_rating = GetAverageRating(NEW.Producto_FK);
    UPDATE Producto
    SET Average_Rating = new_avg_rating
    WHERE Producto_ID = NEW.Producto_FK;
END //
DELIMITER ;


DELIMITER //

CREATE TRIGGER DesaprovProdalBorrarUser
AFTER UPDATE ON Usuarios
FOR EACH ROW
BEGIN
    IF OLD.Estatus = TRUE AND NEW.Estatus = FALSE THEN
        UPDATE Producto
        SET Aprobado = FALSE
        WHERE Usuario_FK = OLD.Usuario_ID;
    END IF;
END //

DELIMITER ;

DELIMITER //

CREATE TRIGGER RemoveProdMalosdelCarrito
AFTER UPDATE ON Producto
FOR EACH ROW
BEGIN
    IF OLD.Aprobado = TRUE AND NEW.Aprobado = FALSE THEN
        DELETE FROM Carrito_Producto
        WHERE Producto_ID = OLD.Producto_ID;
    END IF;
    
    IF OLD.Estatus = TRUE AND NEW.Estatus = FALSE THEN
        DELETE FROM Carrito_Producto
        WHERE Producto_ID = OLD.Producto_ID;
    END IF;
END //

DELIMITER ;


