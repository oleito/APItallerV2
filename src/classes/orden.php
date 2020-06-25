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

    public function listarOrdenesConPedidos()
    {

        $sql = "SELECT 
                    DISTINCT orden_idreferencia AS referencia, 
                    vehiculo.vehiculo_patente AS patente, 
                    vehiculo.vehiculo_vin AS vin, 
                    vhMarca.vhMarca AS marca, 
                    vhModelo.vhModelo AS modelo
                FROM pieza
                JOIN orden ON pieza.orden_idreferencia = orden.idreferencia
                LEFT JOIN vehiculo ON vehiculo.idvehiculo = orden.vehiculo_idvehiculo
                LEFT JOIN vhModelo ON vhModelo.idvhModelo =  vehiculo.vhModelo_idvhModelo
                LEFT JOIN vhMarca ON vhMarca.idvhMarca = vhModelo.vhMarca_idvhMarca
                WHERE pieza.acciones_idaccion = 3 AND pieza.tipo_cargo = 1";
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
                        orden_observaciones AS observaciones,
                        orden_siniestro AS siniestro,
                        orden_entrega_pactada AS fecha_entrega,
                        seguro.seguro AS seguro,
                        vehiculo_idvehiculo AS idvehiculo,
                        vehiculo_patente AS patente,
                        vehiculo_vin AS vin,
                        vehiculo_color AS color,
                        vhMarca.vhMarca AS marca,
                        vhModelo.vhModelo AS modelo
                    FROM `orden`
                    LEFT JOIN seguro
                        ON seguro.idseguro = orden.seguro_idseguro
                    LEFT JOIN vehiculo
                        ON vehiculo.idvehiculo = orden.vehiculo_idvehiculo
                    LEFT JOIN vhModelo
                        ON vhModelo.idvhModelo = vehiculo.vhModelo_idvhModelo
                    LEFT JOIN vhMarca
                        ON vhMarca.idvhMarca = vhModelo.vhMarca_idvhMarca
                    WHERE
                        idreferencia = :idReferencia";

            $sth = $this->conn->prepare($sql);
            $sth->execute(
                array(
                    'idReferencia' => $idReferencia,
                ));
            $tmp = $sth->fetch();
            if ($tmp['fecha_entrega'] != null) {
                $tmp['fecha_entrega'] = $this->cambiarFormatoAEspanol($tmp['fecha_entrega']);
            }
            return $tmp;
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
    public function insertarObsEnReferencia($idReferencia, $observacion)
    {
        try {
            /** INSERTA LA ORDEN */
            $sql = "UPDATE `orden`
                    SET `orden_observaciones` = :observacion
                    WHERE `orden`.`idreferencia` = :idReferencia;";

            $sth = $this->conn->prepare($sql);

            $sth->execute(array(
                ':idReferencia' => $idReferencia,
                ':observacion' => $observacion,
            ));

            return $this->detalleOrden($idReferencia);

        } catch (Exception $e) {
            $this->logger->warning('insertarObsEnReferencia() - ', [$e->getMessage()]);
            return 500;
        }

    }

    public function insertarFechaEnReferencia($idReferencia, $fecha)
    {
        try {

            $fecha_entrega = $this->cambiarFormatoAMysql($fecha);

            /** INSERTA LA ORDEN */
            $sql = "UPDATE `orden`
                    SET `orden_entrega_pactada` = :fecha_entrega
                    WHERE `orden`.`idreferencia` = :idReferencia;";

            $sth = $this->conn->prepare($sql);

            $sth->execute(array(
                ':idReferencia' => $idReferencia,
                ':fecha_entrega' => $fecha_entrega,
            ));

            return $this->detalleOrden($idReferencia);

        } catch (Exception $e) {
            $this->logger->warning('insertarFechaEnReferencia() - ', [$e->getMessage()]);
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

    public function cambiarFormatoAMysql($fecha)
    {
        preg_match('/([0-9]{1,2})\/([0-9]{1,2})\/([0-9]{2,4})/', $fecha, $mifecha);
        $lafecha = $mifecha[3] . "-" . $mifecha[2] . "-" . $mifecha[1];
        return $lafecha;
    }
    public function cambiarFormatoAEspanol($fecha)
    {
        preg_match('/([0-9]{2,4})-([0-9]{1,2})-([0-9]{1,2})/', $fecha, $mifecha);
        $lafecha = $mifecha[3] . "/" . $mifecha[2] . "/" . $mifecha[1];
        return $lafecha;
    }

}
