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
            $sql = "SELECT idchSector AS Id, chSector AS sector FROM chSector;";
            $sth = $this->conn->prepare($sql);
            $sth->execute();
            $sectores = $sth->fetchAll();

            $sql = "SELECT *, (SELECT chSector.idchSector FROM movimiento JOIN chSector ON chSector.idchSector=movimiento.chSector_idchSector
                        WHERE movimiento.orden_idreferencia =orden.idreferencia ORDER
                        BY movimiento.idmovimiento DESC LIMIT 1) AS  idSector,
                        (SELECT chSector.chSector FROM movimiento JOIN chSector ON chSector.idchSector=movimiento.chSector_idchSector
                        WHERE movimiento.orden_idreferencia =orden.idreferencia ORDER
                        BY movimiento.idmovimiento DESC LIMIT 1) AS  sector
                    FROM orden";

            $sql = "SELECT 
                        idreferencia AS referencia, 
                        (SELECT chSector.idchSector FROM movimiento JOIN chSector ON chSector.idchSector=movimiento.chSector_idchSector WHERE movimiento.orden_idreferencia =orden.idreferencia ORDER BY movimiento.idmovimiento DESC LIMIT 1) AS idSector, 
                        (SELECT chSector.chSector FROM movimiento JOIN chSector ON chSector.idchSector=movimiento.chSector_idchSector WHERE movimiento.orden_idreferencia =orden.idreferencia ORDER BY movimiento.idmovimiento DESC LIMIT 1) AS sector 
                    FROM orden 
                    WHERE orden_activo=1";

            $sth = $this->conn->prepare($sql);
            $sth->execute();
            $ordenes = $sth->fetchAll();

            foreach ($sectores as $sector) {
                $tmp = [];
                foreach ($ordenes as $orden) {

                    if ($sector['Id'] === $orden['idSector']) {
                        array_push($tmp, $orden);
                    }
                }
                $data = array('sector' => $sector['sector'],
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
