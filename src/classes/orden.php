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
            $this->logger->warning('listarOrdenes() - ', [$e->getMessage()]);
            return 500;
        }
    }

    public function detalleOrden($idReferencia)
    {
        try {
            $sql = "SELECT
                        idreferencia AS referencia,
                        idorden AS orden,
                        orden_siniestro AS siniestro,
                        seguro.seguro AS seguro,
                        vehiculo_idvehiculo AS idvehiculo,
                        vehiculo_patente AS patente,
                        vehiculo_vin AS vin,
                        vehiculo_color AS color
                    FROM `orden`
                    LEFT JOIN seguro ON seguro.idseguro = orden.seguro_idseguro
                        LEFT JOIN vehiculo ON vehiculo.idvehiculo=orden.vehiculo_idvehiculo
                    WHERE
                        idreferencia = :idReferencia";

            $sth = $this->conn->prepare($sql);
            $sth->execute(
                array(
                    'idReferencia' => $idReferencia,
                ));
            return $sth->fetch();
        } catch (Exception $e) {
            $this->logger->warning('listarOrdenes() - ', [$e->getMessage()]);
            return 500;
        }
    }

    public function insertarOrden($idOrden, $vehiculo, $seguro, $fecha, $repuestos, $observacion)
    {
        $sql = "INSERT
                INTO `orden`
                (`idorden`, `vehiculo_idvehiculo`, `seguro_idseguro`, `orden_entrega_pactada`, `orden_repuestos`, `orden_observaciones`)
                VALUES
                (:idOrden, :vehiculo, :seguro, :fecha, :repuestos, :observacion);";
        try {
            $sth = $this->conn->prepare($sql);
            $sth->execute(array(
                ':idOrden' => $idOrden,
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

    public function insertarReferencia($idReferencia)
    {
        try {
            // crear un vehiculo antes de insertar la orden
            /** INSERTA LA ORDEN */
            $sql = "INSERT
                INTO
                    `vehiculo`
                VALUES
                    (NULL, NULL, '', '', '', NULL);

                INSERT INTO `orden`
                ( `idreferencia`, `vehiculo_idvehiculo`)
                VALUES
                ( :idReferencia, LAST_INSERT_ID() );";

            $sth = $this->conn->prepare($sql);
            $sth->execute(array(
                ':idReferencia' => $idReferencia,
            ));

            $sql = "SELECT
                    idorden AS orden,
                    idreferencia AS referencia,
                    orden_siniestro AS siniestro,
                    vehiculo_idvehiculo AS idvehiculo,
                    orden_entrega_pactada AS fecha_entrega,
                    orden_observaciones AS observaciones,
                    seguro_idseguro AS seguro,
                    orden_activo AS activo

                    FROM orden WHERE orden.idreferencia = :idReferencia;

                    INSERT INTO `movimiento`
                    ( `movimiento_fecha`, `usuario_idusuario`, `chSector_idchSector`,  `orden_idreferencia`)
                    VALUES
                    (:fechahora , '1', '1', :idReferencia);";

            $sth = $this->conn->prepare($sql);
            $sth->execute(array(
                ':idReferencia' => $idReferencia,
                ':fechahora' => date("Y-m-d H:i:s"),
            ));
            return $sth->fetch();

        } catch (Exception $e) {
            $this->logger->warning('insertarorden() - ', [$e->getMessage()]);
            return 500;
        }
    }

    public function insertarSeguroEnReferencia($idReferencia, $idSeguro)
    {
        try {
            /** INSERTA LA ORDEN */
            $sql = "UPDATE `orden`
                    SET `seguro_idseguro` = :idSeguro
                    WHERE `orden`.`idreferencia` = :idReferencia;";

            $sth = $this->conn->prepare($sql);

            $sth->execute(array(
                ':idReferencia' => $idReferencia,
                ':idSeguro' => $idSeguro,
            ));

            return $this->detalleOrden($idReferencia);

        } catch (Exception $e) {
            $this->logger->warning('insertarSeguroEnReferencia() - ', [$e->getMessage()]);
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
