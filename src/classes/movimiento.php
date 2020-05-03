<?php

class Movimiento
{
    private $conn;
    private $logger;

    public function __construct($monolog_OBJ)
    {
        $this->logger = $monolog_OBJ;
        $pdoMysql = new pdoMysql($this->logger);
        $this->conn = $pdoMysql->conectar();
    }

    public function listarMovimientos($idOrden)
    {
        $sql = "SELECT * FROM `movimiento` WHERE orden_idorden = :idOrden;";
        try {
            $sth = $this->conn->prepare($sql);
            $sth->execute(array(
                ':idOrden'=>$idOrden
            ));
            return $sth->fetchAll();
        } catch (Exception $e) {
            $this->logger->warning('listarMovimientos() - ', [$e->getMessage()]);
            return 500;
        }
    }

    public function insertarMovimiento($orden, $fecha, $usuario, $sector)
    {

        $sql = "INSERT
                INTO `movimiento`
                (`idmovimiento`, `orden_idorden`, `movimiento_fecha`, `usuario_idusuario`, `chSector_idchSector`)
                VALUES
                (NULL, :orden, :fecha, :usuario, :sector);";
        try {
            $sth = $this->conn->prepare($sql);
            $sth->execute(array(
                ':orden' => $orden,
                ':fecha' => $fecha,
                ':usuario' => $usuario,
                ':sector' => $sector,
            ));
            return $this->listarMovimientos($orden);
        } catch (Exception $e) {
            $this->logger->warning('insertarMovimiento() - ', [$e->getMessage()]);
            return 500;
        }
    }

    public function eliminarMovimiento($idMovimiento, $idOrden)
    {
        $sql = "DELETE FROM `movimiento` WHERE `movimiento`.`idmovimiento` = :idMovimiento;";
        try {
            $sth = $this->conn->prepare($sql);
            $sth->execute(array(
                ':idMovimiento' => $idMovimiento,
            ));
            return $this->listarMovimientos($idOrden);
        } catch (Exception $e) {
            $this->logger->warning('eliminar Movimiento() - ', [$e->getMessage()]);
            return 500;
        }
    }

}
