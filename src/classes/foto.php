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
                    $value['foto_thumb'] = "http://" . $_SERVER["SERVER_NAME"] . dirname($_SERVER['PHP_SELF'], 2) . '/imagenes/thumbnail/' . $value['foto_url'];
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
        define('UPLOAD_DIR_TH', '../imagenes/thumbnail/');
        define('RELACION_ASPECTO', 1.7777);

        try {
            $sth = $this->conn->prepare($sql);

            foreach ($fotos as $foto) {

                $base64Img = str_replace('data:image/jpeg;base64,', '', $foto);
                $base64Img = str_replace(' ', '+', $base64Img);
                $decodedImg = base64_decode($base64Img);
                $idName = $idReferencia . '-' . uniqid();
                $fileName = UPLOAD_DIR . $idName . '.jpg';
                $success = file_put_contents($fileName, $decodedImg);

                /** MINIATURAS */
                // abrir foto
                $original = imagecreatefromjpeg($fileName);
                $ancho_o = imagesx($original);
                $alto_o = imagesy($original);
                $thumbnail = imagecreatetruecolor(160, 90);
                if (($ancho_o / $alto_o) > RELACION_ASPECTO) {
                    //es mas ancho
                    $ratio = $ancho_o / $alto_o;
                    $src_y = 0;
                    $src_h = $alto_o;

                    $new_w = 160 * $ratio;
                    $src_x = ($ancho_o - $new_w) / 2;
                    $src_w = $src_x + $new_w;
                } else {
                    //es mas alto
                    $ratio = $ancho_o / $alto_o;
                    $src_x = 0;
                    $src_w = $ancho_o;

                    $new_h = 90 * $ratio;
                    $src_y = ($alto_o - $new_h) / 2;
                    $src_h = $src_y + $new_h;
                }
                // thumbnailr
                imagecopyresampled($thumbnail, $original, 0, 0, $src_x, $src_y, 160, 90, $src_w, $src_h);

                imagejpeg($thumbnail, UPLOAD_DIR_TH . $idName . '.jpg', 100);
                // https://www.youtube.com/watch?v=XzMNrOiIbpA
                /** */

                $sth->execute(array(
                    ':archivo' => $idName . '.jpg',
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
