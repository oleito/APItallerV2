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
                ':color' => $color
            ));
            return $this->listarVehiculos();
        } catch (Exception $e) {
            $this->logger->warning('insertarVehiculo() - ', [$e->getMessage()]);
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
