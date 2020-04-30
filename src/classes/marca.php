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
        /**HAY QUE MEJORAR ESTA RESPUESTA */
        $sql = "SELECT * FROM vhMarca";
        try {
            $sth = $this->conn->prepare($sql);
            $sth->execute();
            return $sth->fetchAll();
        } catch (Exception $e) {
            $this->logger->warning('listarMarcas() - ', [$e->getMessage()]);
            return 500;
        }
    }

    public function insertarMarca($marca, $iniciales)
    {
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
            return $this->listarMarcas();
        } catch (Exception $e) {
            $this->logger->warning('insertarMarca() - ', [$e->getMessage()]);
            return 500;
        }
    }



    public function eliminarMarca($marca)
    {
        $sql = "DELETE FROM vhMarca WHERE vhMarca.idvhMarca = 6;";
        try {
            $sth = $this->conn->prepare($sql);
            $sth->execute(array(
                ':marca' => $marca,
                ':iniciales' => $iniciales,
            ));
            return $this->listarMarcas();
        } catch (Exception $e) {
            $this->logger->warning('insertarMarca() - ', [$e->getMessage()]);
            return 500;
        }
    }

}
