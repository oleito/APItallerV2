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
