<?php

class Sector
{
    private $conn;
    private $logger;

    public function __construct($monolog_OBJ)
    {
        $this->logger = $monolog_OBJ;
        $pdoMysql = new pdoMysql($this->logger);
        $this->conn = $pdoMysql->conectar();
    }

    public function listarSectores()
    {
        $sql = "SELECT idchSector AS Id, chSector AS Sector FROM chSector;";
        try {
            $sth = $this->conn->prepare($sql);
            $sth->execute();
            return $sth->fetchAll();
        } catch (Exception $e) {
            $this->logger->warning('listarSectores() - ', [$e->getMessage()]);
            return 500;
        }
    }

    public function listarVehiculosEnSectores()
    {
        //return "aca van los vehiculos agrupados en sectores";
        $bodyOut = [];
        try {
            $sql = "SELECT idchSector AS idSector, chSector AS sector FROM chSector;";
            $sth = $this->conn->prepare($sql);
            $sth->execute();
            $sectores = $sth->fetchAll();

            $sql = "SELECT
                        idreferencia AS referencia,
                        (SELECT chSector.idchSector FROM movimiento JOIN chSector ON chSector.idchSector=movimiento.chSector_idchSector WHERE movimiento.orden_idreferencia =orden.idreferencia ORDER BY movimiento.idmovimiento DESC LIMIT 1) AS idSector,
                        (SELECT chSector.chSector FROM movimiento JOIN chSector ON chSector.idchSector=movimiento.chSector_idchSector WHERE movimiento.orden_idreferencia =orden.idreferencia ORDER BY movimiento.idmovimiento DESC LIMIT 1) AS sector ,
                        seguro.seguro AS seguro,
                        vehiculo.vehiculo_patente AS patente,
                        vehiculo.vehiculo_color AS color,
                        vhMarca.vhMarca AS marca,
                        vhModelo.vhModelo AS modelo
                    FROM orden
                    LEFT JOIN seguro ON seguro.idseguro = orden.seguro_idseguro
                    LEFT JOIN vehiculo ON vehiculo.idvehiculo = orden.vehiculo_idvehiculo
                    LEFT JOIN vhModelo ON vhModelo.idvhModelo = vehiculo.vhModelo_idvhModelo
                    LEFT JOIN vhMarca ON vhMarca.idvhMarca = vhModelo.vhMarca_idvhMarca
                    WHERE orden_activo = 1";

            $sth = $this->conn->prepare($sql);
            $sth->execute();
            $ordenes = $sth->fetchAll();

            //Recorre todos los sectores
            foreach ($sectores as $sector) {
                $tmp = [];
                //recorre ordenes
                foreach ($ordenes as $orden) {

                    // busca coincidencia orden en sector actual
                    if ($sector['idSector'] === $orden['idSector']) {
                        // si coincide, inserta en array temporal
                        // array de ordenes del sector actual.
                        array_push($tmp, $orden);
                    }
                }

                // crea el objeto de sector
                // este objeto ya tiene las ref cargadas
                $tmp2 = array(
                    'sector' => $sector['sector'],
                    'idSector' => $sector['idSector'],
                    'vehiculos' => $tmp);
                array_push($bodyOut, $tmp2);
            }

            return $bodyOut;
        } catch (Exception $e) {
            $this->logger->warning('listarSectores() - ', [$e->getMessage()]);
            return 500;
        }
    }

    public function actualizarVehiculosEnSectores($idReferencia, $idNewSector)
    {
        try {
            $now = date("Y-m-d H:i:s");

            $sql = "INSERT
                    INTO `movimiento`
                        (`idmovimiento`, `movimiento_fecha`, `usuario_idusuario`, `chSector_idchSector`, `orden_idorden`, `orden_idreferencia`)
                    VALUES
                        (NULL, :noww, '1', :idNewSector, NULL, :idReferencia);";

            $sth = $this->conn->prepare($sql);
            $sth->execute(array(
                ':idNewSector' => $idNewSector,
                ':idReferencia' => $idReferencia,
                ':noww' => $now
            ));

            return $this->listarVehiculosEnSectores();
        } catch (Exception $e) {
            $this->logger->warning('actualizarVehiculosEnSectores() - ', [$e->getMessage()]);
            return 500;
        }
    }

    public function insertarSector($sector)
    {
        $sql = "INSERT INTO `chSector` (`idchSector`, `chSector`) VALUES (NULL, :sector);";
        try {
            $sth = $this->conn->prepare($sql);
            $sth->execute(array(
                ':sector' => $sector,
            ));
            return $this->listarSectores();
        } catch (Exception $e) {
            $this->logger->warning('insertarSector() - ', [$e->getMessage()]);
            return 500;
        }
    }

    public function eliminarSector($idSector)
    {
        $sql = "DELETE FROM chSector WHERE chSector.idchSector = :idSector ;";
        try {
            $sth = $this->conn->prepare($sql);
            $sth->execute(array(
                ':idSector' => $idSector,
            ));
            return $this->listarSectores();
        } catch (Exception $e) {
            $this->logger->warning('eliminar Sector() - ', [$e->getMessage()]);
            return 500;
        }
    }

}
