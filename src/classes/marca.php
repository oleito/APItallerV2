<?php

class Marca
{
    private $conn;
    private $logger;

    public function __construct($monolog_OBJ)
    {
        $this->logger = $monolog_OBJ;
        $pdoMysql = new pdoMysql($this->logger);
        $this->conn = $pdoMysql->conectar();
        $this->logger->info('Marca() - ', [$this->conn]);
    }

    public function listarMarcas()
    {
        $sql = "SELECT * FROM vhMarca";
        try {
            $sth = $this->conn->prepare($sql);
            $sth->execute();
            return $sth->fetchAll();
        } catch (Exception $e) {
            $this->logger->warning('vigil-diaria() - ', [$e->getMessage()]);
            return 500;
        }
    }

}
