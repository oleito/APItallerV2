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
            $res = $sth->fetchAll();
            $bodyOut = [];

            foreach ($res as $r) {
                $r['Img'] = "http://" . $_SERVER["SERVER_NAME"] . dirname($_SERVER['PHP_SELF'], 2) . '/recursos/' . $r['Img'];
                array_push($bodyOut, $r);
            }

            return $bodyOut;
        } catch (Exception $e) {
            $this->logger->warning('listarTipos() - ', [$e->getMessage()]);
            return 500;
        }
    }

    public function insertarTipo($tipo, $img, $imgAll)
    {
        $sql = "INSERT
                INTO vhTipo
                (idvhTipo, vhTipo, vhTipo_img, vhTipo_img_all)
                VALUES (
                    NULL,
                    :tipo,
                    :img,
                    :imgAll
                );";
        try {
            $sth = $this->conn->prepare($sql);
            $sth->execute(array(
                ':tipo' => $tipo,
                ':img' => $img,
                ':imgAll' => $imgAll,
            ));
            return $this->listarTipos();
        } catch (Exception $e) {
            $this->logger->warning('insertarTipo() - ', [$e->getMessage()]);
            return 500;
        }
    }

    public function eliminarTipo($idTipo)
    {
        $sql = "DELETE FROM vhTipo WHERE vhTipo.idvhTipo = :idTipo;";
        try {
            $sth = $this->conn->prepare($sql);
            $sth->execute(array(
                ':idTipo' => $idTipo,
            ));
            return $this->listarTipos();
        } catch (Exception $e) {
            $this->logger->warning('eliminar tipo() - ', [$e->getMessage()]);
            return 500;
        }
    }

}
