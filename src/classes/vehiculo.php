<?php

class Vehiculo
{
    private $conn;
    private $logger;

    public function __construct($monolog_OBJ)
    {
        $this->logger = $monolog_OBJ;
        $pdoMysql = new pdoMysql($this->logger);
        $this->conn = $pdoMysql->conectar();
    }

    public function listarVehiculos()
    {
        $sql = "SELECT * FROM vehiculo;";
        try {
            $sth = $this->conn->prepare($sql);
            $sth->execute();
            return $sth->fetchAll();
        } catch (Exception $e) {
            $this->logger->warning('listarVehiculos() - ', [$e->getMessage()]);
            return 500;
        }
    }
    public function detalleVehiculo($idvehiculo)
    {
        $sql = "SELECT 
                    idvehiculo, vehiculo_patente AS patente, 
                    vehiculo_vin AS 
                    vin, vehiculo_color AS color 
                FROM 
                    vehiculo 
                WHERE 
                    idvehiculo =:idvehiculo;";
        try {
            $sth = $this->conn->prepare($sql);
            $sth->execute(array(
                ':idvehiculo' => $idvehiculo,
            ));
            return $sth->fetch();
        } catch (Exception $e) {
            $this->logger->warning('listarVehiculos() - ', [$e->getMessage()]);
            return 500;
        }

    }

    public function insertarVehiculo($modelo, $patente, $vin, $color)
    {
        $sql = "INSERT
        INTO `vehiculo`
        (`idvehiculo`, `vhModelo_idvhModelo`, `vehiculo_patente`, `vehiculo_vin`, `vehiculo_color`)
        VALUES
        (NULL, :modelo, :patente, :vin, :color);";
        try {
            $sth = $this->conn->prepare($sql);
            $sth->execute(array(
                ':modelo' => $modelo,
                ':patente' => $patente,
                ':vin' => $vin,
                ':color' => $color,
            ));
            return $this->listarVehiculos();
        } catch (Exception $e) {
            $this->logger->warning('insertarVehiculo() - ', [$e->getMessage()]);
            return 500;
        }
    }

    public function actualizarVehiculo($idvehiculo, $patente, $vin, $color)
    {
        try {
            /** INSERTA LA ORDEN */
            $sql = "UPDATE
                        `vehiculo`
                    SET
                        `vehiculo_patente` = :patente,
                        `vehiculo_vin` = :vin,
                        `vehiculo_color` = :color
                    WHERE
                        `vehiculo`.`idvehiculo` = :idvehiculo;";

            $sth = $this->conn->prepare($sql);

            $sth->execute(array(
                ':patente' => $patente,
                ':vin' => $vin,
                ':color' => $color,
                ':idvehiculo' => $idvehiculo,
            ));

            return $this->detalleVehiculo($idvehiculo);

        } catch (Exception $e) {
            $this->logger->warning('insertarSeguroEnReferencia() - ', [$e->getMessage()]);
            return 500;
        }

    }

    public function eliminarVehiculo($idVehiculo)
    {
        $sql = "DELETE FROM `vehiculo` WHERE `vehiculo`.`idvehiculo` = :idVehiculo;";
        try {
            $sth = $this->conn->prepare($sql);
            $sth->execute(array(
                ':idVehiculo' => $idVehiculo,
            ));
            return $this->listarVehiculos();
        } catch (Exception $e) {
            $this->logger->warning('eliminar Vehiculo() - ', [$e->getMessage()]);
            return 500;
        }
    }

}
