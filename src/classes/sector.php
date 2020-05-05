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
            $sql = "SELECT idchSector AS Id, chSector AS Sector FROM chSector;";
            $sth = $this->conn->prepare($sql);
            $sth->execute();
            $sectores = $sth->fetchAll();

            $sql = "SELECT
                    orden.idorden AS Orden,
                    orden.orden_entrega_pactada AS  FechaEntrega,
                    seguro.seguro AS  Seguro,
                    orden.orden_repuestos AS  EsperaRepuestos,
                    vehiculo.idvehiculo AS IdVehiculo,
                    vehiculo.vehiculo_patente AS  Patente,
                    vehiculo.vehiculo_vin AS  Vin,
                    vhMarca.vhMarca AS  Marca,
                    vhModelo.vhModelo AS  Modelo,
                    vehiculo.vehiculo_color AS  Color,
                        (SELECT chSector.idchSector FROM movimiento JOIN chSector ON chSector.idchSector=movimiento.chSector_idchSector
                        WHERE movimiento.orden_idorden=orden.idorden ORDER
                        BY movimiento.idmovimiento DESC LIMIT 1) AS  IdSector,
                        (SELECT chSector.chSector FROM movimiento JOIN chSector ON chSector.idchSector=movimiento.chSector_idchSector
                        WHERE movimiento.orden_idorden=orden.idorden ORDER
                        BY movimiento.idmovimiento DESC LIMIT 1) AS  Sector,
                    orden.orden_observaciones AS  Observaciones
                    FROM orden
                    JOIN vehiculo ON vehiculo.idvehiculo=orden.vehiculo_idvehiculo
                    JOIN vhModelo ON vehiculo.vhModelo_idvhModelo= vhModelo.idvhModelo
                    JOIN vhMarca ON vhModelo.vhMarca_idvhMarca= vhMarca.idvhMarca
                    JOIN seguro ON orden.seguro_idseguro=seguro.idseguro;";

            $sth = $this->conn->prepare($sql);
            $sth->execute();
            $ordenes = $sth->fetchAll();

            foreach ($sectores as $sector) {
                $tmp = [];
                foreach ($ordenes as $orden) {

                    if ($sector['Id'] === $orden['IdSector']) {
                        array_push($tmp, $orden);
                    }
                }
                $data = array('Sector' => $sector['Sector'],
                    'vehiculos' => $tmp);
                array_push($bodyOut, $data);
            }

            return $bodyOut;
        } catch (Exception $e) {
            $this->logger->warning('listarSectores() - ', [$e->getMessage()]);
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
