use fantazon;

DROP FUNCTION IF EXISTS GetAverageRating;
DROP FUNCTION IF EXISTS IsProductInList;

-- Funcion para calcular el rating promedio de cada producto
DELIMITER //
CREATE FUNCTION GetAverageRating(product_id INT) RETURNS DECIMAL(3, 2) DETERMINISTIC
BEGIN
    DECLARE avg_rating DECIMAL(3, 2);
    SELECT AVG(Rating) INTO avg_rating
    FROM Comments
    WHERE Producto_FK = product_id;
    RETURN IFNULL(avg_rating, 0.00);
END //
DELIMITER ;

-- Funcion revisar si un producto ya se encuentra en una lista antes de meterlo otra vez
DELIMITER //
CREATE FUNCTION IsProductInList (
    p_listas_id INT,
    p_producto_id INT
) RETURNS BOOLEAN
    DETERMINISTIC
    READS SQL DATA
BEGIN
    DECLARE product_exists BOOLEAN;
    SET product_exists = EXISTS (
        SELECT 1
        FROM Listas_Productos
        WHERE Listas_ID = p_listas_id
          AND Producto_ID = p_producto_id
    );

    RETURN product_exists;
END //

DELIMITER ;


