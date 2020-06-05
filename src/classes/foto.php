<?php

class Foto
{
    private $conn;
    private $logger;

    public function __construct($monolog_OBJ)
    {
        $this->logger = $monolog_OBJ;
        $pdoMysql = new pdoMysql($this->logger);
        $this->conn = $pdoMysql->conectar();
    }

    public function listarFotos($idReferencia)
    {
        $sql = "SELECT vhFotos_url AS foto_url, vhFotos_io AS foto_io FROM `vhFotos` WHERE orden_idreferencia = :idReferencia;";
        try {
            $sth = $this->conn->prepare($sql);
            $sth->execute(array(
                ':idReferencia' => $idReferencia,
            ));
            $bodyTemp = $sth->fetchAll();
            if (!empty($bodyTemp) > 0) {
                $bodyOut = [];
                foreach ($bodyTemp as $key => $value) {
                    $value['foto_url'] = "http://" . $_SERVER["SERVER_NAME"] . dirname($_SERVER['PHP_SELF'], 2) . '/imagenes/' . $value['foto_url'];
                    array_push($bodyOut, $value);
                }
                return $bodyOut;
            }

        } catch (Exception $e) {
            $this->logger->warning('listarFotos() - ', [$e->getMessage()]);
            return 500;
        }
    }

    public function insertarFotos($idReferencia, $fotos)
    {
        $sql = "INSERT
        INTO `vhFotos`
        (`idvhFotos`, `vhFotos_url`, `vhFotos_io`, `orden_idorden`)
        VALUES
        (NULL, :archivo, :inout, :idReferencia);";

        $sql = "INSERT
                INTO `vhFotos` (`idvhFotos`, `vhFotos_url`, `vhFotos_io`, `orden_idorden`, `orden_idreferencia`)
                VALUES (NULL, :archivo, :inout, NULL, :idReferencia);";

        define('UPLOAD_DIR', '../imagenes/');

        try {
            $sth = $this->conn->prepare($sql);

            foreach ($fotos as $key => $value) {

                $img = str_replace('data:image/jpeg;base64,', '', $value);
                $img = str_replace(' ', '+', $img);
                $data = base64_decode($img);
                $filename = $idReferencia . '-' . uniqid() . '.jpg';
                $file = UPLOAD_DIR . $filename;
                $success = file_put_contents($file, $data);



                $sth->execute(array(
                    ':archivo' => $filename,
                    ':inout' => 1,
                    ':idReferencia' => $idReferencia,
                ));

            }

            $this->logger->info('insertarFoto() - ', []);
            return $this->listarFotos($idReferencia);
        } catch (Exception $e) {
            $this->logger->warning('insertarFoto() - ', [$e->getMessage()]);
            return 500;
        }
    }

    public function eliminarFoto($idFoto, $idOrden)
    {
        $sql = "DELETE FROM `Foto` WHERE `Foto`.`idFoto` = :idFoto;";
        try {
            $sth = $this->conn->prepare($sql);
            $sth->execute(array(
                ':idFoto' => $idFoto,
            ));
            return $this->listarFotos($idOrden);
        } catch (Exception $e) {
            $this->logger->warning('eliminar Foto() - ', [$e->getMessage()]);
            return 500;
        }
    }

}
