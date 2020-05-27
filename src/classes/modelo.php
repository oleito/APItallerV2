<?php

class Modelo
{
    private $conn;
    private $logger;

    public function __construct($monolog_OBJ)
    {
        $this->logger = $monolog_OBJ;
        $pdoMysql = new pdoMysql($this->logger);
        $this->conn = $pdoMysql->conectar();
    }

    public function listarModelos($idMarca)
    {
        $sql = "SELECT
                idvhModelo AS Id,
                vhModelo AS Modelo,
                vhTipo_idvhTipo AS Tipo
                FROM vhModelo
                WHERE vhModelo.vhMarca_idvhMarca = :idMarca ;";
        try {
            $sth = $this->conn->prepare($sql);
            $sth->execute(array(
                ':idMarca'=>$idMarca,
            ));
            return $sth->fetchAll();
        } catch (Exception $e) {
            $this->logger->warning('listarModelos() - ', [$e->getMessage()]);
            return 500;
        }
    }

    public function insertarModelo($modelo, $marca, $tipo)
    {

        $sql = "INSERT INTO `vhModelo` 
                (`idvhModelo`, `vhModelo`, `vhMarca_idvhMarca`, `vhTipo_idvhTipo`) 
                VALUES 
                (NULL, :modelo, :marca, :tipo);"
                ;
        try {
            $sth = $this->conn->prepare($sql);
            $sth->execute(array(
                ':modelo' => $modelo,
                ':marca' => $marca,
                ':tipo' => $tipo,
            ));
            return $this->listarModelos($marca);
        } catch (Exception $e) {
            $this->logger->warning('insertarModelo() - ', [$e->getMessage()]);
            return 500;
        }
    }

    public function eliminarModelo($idMarca, $idModelo)
    {
        $sql = "DELETE FROM vhModelo WHERE vhModelo.idvhModelo = :idModelo ;";
        try {
            $sth = $this->conn->prepare($sql);
            $sth->execute(array(
                ':idModelo' => $idModelo,
            ));
            return $this->listarModelos($idMarca);
        } catch (Exception $e) {
            $this->logger->warning('eliminar Modelo() - ', [$e->getMessage()]);
            return 500;
        }
    }

    public function actualizarModelo($idMarca, $idModelo, $tipo)
    {
        try {
            $sql = "UPDATE `vhModelo` SET 
                    `vhTipo_idvhTipo` = :tipo 
                    WHERE `vhModelo`.`idvhModelo` = :idModelo;";

            $sth = $this->conn->prepare($sql);

            $sth->execute(array(
                ':tipo' => $tipo,
                ':idModelo' => $idModelo,
            ));

            return $this->listarModelos($idMarca);

        } catch (Exception $e) {
            $this->logger->warning('actualizarModelo() - ', [$e->getMessage()]);
            return 500;
        }

    }
    

}
