<?php

class Orden
{
    private $conn;
    private $logger;

    public function __construct($monolog_OBJ)
    {
        $this->logger = $monolog_OBJ;
        $pdoMysql = new pdoMysql($this->logger);
        $this->conn = $pdoMysql->conectar();
    }

    public function listarOrdenes()
    {
        $sql = "SELECT * FROM orden;";
        try {
            $sth = $this->conn->prepare($sql);
            $sth->execute();
            return $sth->fetchAll();
        } catch (Exception $e) {
            $this->logger->warning('listarOrdens() - ', [$e->getMessage()]);
            return 500;
        }
    }

    public function insertarOrden($vehiculo, $seguro, $fecha, $repuestos, $observacion)
    {
        $sql = "INSERT
                INTO `orden`
                (`idorden`, `vehiculo_idvehiculo`, `seguro_idseguro`, `orden_entrega_pactada`, `orden_repuestos`, `orden_observaciones`)
                VALUES
                (NULL, :vehiculo, :seguro, :fecha, :repuestos, :observacion);";
        try {
            $sth = $this->conn->prepare($sql);
            $sth->execute(array(
                ':vehiculo' => $vehiculo,
                ':seguro' => $seguro,
                ':fecha' => $fecha,
                ':repuestos' => $repuestos,
                ':observacion' => $observacion,
            ));
            return $this->listarOrdenes();
        } catch (Exception $e) {
            $this->logger->warning('insertarorden() - ', [$e->getMessage()]);
            return 500;
        }
    }

    public function eliminarOrden($idOrden)
    {
        $sql = "DELETE FROM `orden` WHERE `orden`.`idorden` = :idOrden;";
        try {
            $sth = $this->conn->prepare($sql);
            $sth->execute(array(
                ':idOrden' => $idOrden,
            ));
            return $this->listarOrdenes();
        } catch (Exception $e) {
            $this->logger->warning('eliminar Orden() - ', [$e->getMessage()]);
            return 500;
        }
    }

}
