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

    public function insertarMarca($marca, $iniciales)
    {

        // $marca->insertarMarca('Citroën','Ci');

        $sql = "INSERT INTO vhMarca
                (idvhMarca, vhMarca, vhIniciales)
                VALUES
                (NULL, :marca, :iniciales);";
        try {
            $sth = $this->conn->prepare($sql);
            $sth->execute(array(
                ':marca' => $marca,
                ':iniciales' => $iniciales,
            ));
            return $sth->fetchAll();
        } catch (Exception $e) {
            $this->logger->warning('vigil-diaria() - ', [$e->getMessage()]);
            return 500;
        }
    }

}
