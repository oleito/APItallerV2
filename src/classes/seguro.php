<?php

class Seguro
{
    private $conn;
    private $logger;

    public function __construct($monolog_OBJ)
    {
        $this->logger = $monolog_OBJ;
        $pdoMysql = new pdoMysql($this->logger);
        $this->conn = $pdoMysql->conectar();
    }

    public function listarSeguros()
    {
        $sql = "SELECT idseguro AS Id, seguro AS Seguro FROM seguro;";
        try {
            $sth = $this->conn->prepare($sql);
            $sth->execute();
            return $sth->fetchAll();
        } catch (Exception $e) {
            $this->logger->warning('listarSeguros() - ', [$e->getMessage()]);
            return 500;
        }
    }

    public function insertarSeguro($seguro)
    {
        $sql = "INSERT INTO `seguro` (`idseguro`, `seguro`) VALUES (NULL, :seguro);";
        try {
            $sth = $this->conn->prepare($sql);
            $sth->execute(array(
                ':seguro' => $seguro,
            ));
            return $this->listarSeguros();
        } catch (Exception $e) {
            $this->logger->warning('insertarSeguro() - ', [$e->getMessage()]);
            return 500;
        }
    }

    public function eliminarSeguro($idSeguro)
    {
        $sql = "DELETE FROM seguro WHERE seguro.idseguro = :idSeguro ;";
        try {
            $sth = $this->conn->prepare($sql);
            $sth->execute(array(
                ':idSeguro' => $idSeguro,
            ));
            return $this->listarSeguros();
        } catch (Exception $e) {
            $this->logger->warning('eliminar Seguro() - ', [$e->getMessage()]);
            return 500;
        }
    }

}
