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

    public function listarPiezas($idReferencia)
    {

        $sql = "SELECT
                    pieza_nombre AS pieza, 
                    acciones.accion AS accion, 
                    pieza_codigo AS codigo, 
                    pieza_proveedor AS proveedor, 
                    pEstado.estado_pedido AS estado ,
                    CASE
                        WHEN pieza.tipo_cargo= '2' THEN 'ext'
                        WHEN pieza.tipo_cargo= '1' THEN 'int'
                    END AS modo
                FROM `pieza`
                JOIN pEstado
                    ON pEstado.idpEstado = pieza.pEstado_idpEstado
                    JOIN acciones ON pieza.acciones_idaccion=acciones.idaccion
                WHERE orden_idreferencia = :idReferencia;";
        try {
            $sth = $this->conn->prepare($sql);
            $sth->execute(array(
                ':idReferencia' => $idReferencia,
            ));
            return $sth->fetchAll();
        } catch (Exception $e) {
            $this->logger->warning('listarPiezas() - ', [$e->getMessage()]);
            return 500;
        }
    }

    public function insertarPieza($referencia, $piezas)
    {
        $sql = "INSERT
                INTO `pieza`
                    (`idpieza`, `pieza_nombre`, `pieza_codigo`, `pieza_proveedor`, `acciones_idaccion`, `pEstado_idpEstado`, `tipo_cargo`, `orden_idorden`, `orden_idreferencia`)
                VALUES
                    (NULL, :pieza, :codigo, :proveed, :id_accion, '1', :cargo, NULL, :id_referencia);";

        try {
            $sth = $this->conn->prepare($sql);
            foreach ($piezas as $pieza) {
                $sth->execute(array(
                    ':pieza' => $pieza['pieza'],
                    ':id_accion' => $pieza['accion'],
                    ':cargo' => $pieza['modo'],
                    ':codigo' => $pieza['codigo'],
                    ':proveed' => $pieza['proveedor'],
                    ':id_referencia' => $referencia,
                ));
            }

            return $this->listarPiezas($referencia);
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
