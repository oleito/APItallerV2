<?php

class Pieza
{
    private $conn;
    private $logger;

    public function __construct($monolog_OBJ)
    {
        $this->logger = $monolog_OBJ;
        $pdoMysql = new pdoMysql($this->logger);
        $this->conn = $pdoMysql->conectar();
    }

    public function listarPiezas($idOrden)
    {
        $sql = "SELECT * FROM `pieza` WHERE orden_idorden = :idOrden;";
        try {
            $sth = $this->conn->prepare($sql);
            $sth->execute(array(
                ':idOrden' => $idOrden,
            ));
            return $sth->fetchAll();
        } catch (Exception $e) {
            $this->logger->warning('listarPiezas() - ', [$e->getMessage()]);
            return 500;
        }
    }

    public function insertarPieza($pieza, $orden, $accion)
    {
        $sql = "INSERT
                INTO `pieza`
                (`idpieza`, `pieza_nombre`, `orden_idorden`, `acciones_idaccion`)
                VALUES
                (NULL, :pieza, :orden, :accion);";

        try {
            $sth = $this->conn->prepare($sql);
            $sth->execute(array(
                ':pieza' => $pieza,
                ':orden' => $orden,
                ':accion' => $accion
            ));
            return $this->listarPiezas($orden);
        } catch (Exception $e) {
            $this->logger->warning('insertarPieza() - ', [$e->getMessage()]);
            return 500;
        }
    }

    public function eliminarPieza($idPieza, $idOrden)
    {
        $sql = "DELETE FROM `pieza` WHERE `pieza`.`idpieza` = :idPieza;";
        try {
            $sth = $this->conn->prepare($sql);
            $sth->execute(array(
                ':idPieza' => $idPieza,
            ));
            return $this->listarPiezas($idOrden);
        } catch (Exception $e) {
            $this->logger->warning('eliminar Pieza() - ', [$e->getMessage()]);
            return 500;
        }
    }

}
