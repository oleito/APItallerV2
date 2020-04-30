<?php

class Tipo
{
    private $conn;
    private $logger;

    public function __construct($monolog_OBJ)
    {
        $this->logger = $monolog_OBJ;
        $pdoMysql = new pdoMysql($this->logger);
        $this->conn = $pdoMysql->conectar();
    }

    public function listarTipos()
    {
        $sql = "SELECT 
                idvhTipo AS Id, 
                vhTipo AS Tipo, 
                vhTipo_img AS Img, 
                vhTipo_img_all AS Img_all 
                FROM vhTipo;";
        try {
            $sth = $this->conn->prepare($sql);
            $sth->execute();
            return $sth->fetchAll();
        } catch (Exception $e) {
            $this->logger->warning('listarMarcas() - ', [$e->getMessage()]);
            return 500;
        }
    }

    public function insertarTipo($marca, $iniciales)
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

    public function eliminarTipo($idTipo)
    {
        $sql = "DELETE FROM vhMarca WHERE vhMarca.idvhMarca = :idTipo ;";
        try {
            $sth = $this->conn->prepare($sql);
            $sth->execute(array(
                ':idTipo' => $idTipo,
            ));
            return $this->listarMarcas();
        } catch (Exception $e) {
            $this->logger->warning('eliminar marca() - ', [$e->getMessage()]);
            return 500;
        }
    }

}

