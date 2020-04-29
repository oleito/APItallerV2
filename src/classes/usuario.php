<?php

require_once './../src/config/db.php';

class usuario
{

    /** MONOLOG */
    protected $logger;

    /* DEL USUARIO */
    private $usr_id;
    private $usr_nombre;
    private $usr_apellido;

    // metodos
    public function __construct($monolog_OBJ)
    {

        $this->logger = $monolog_OBJ;

    }

    public function login($username, $password)
    {
        $database = new pdoMysql($this->logger);

        $sql = 'SELECT
                idusuario AS id,
                usuario_nombre AS nombre,
                usuario_apellido AS apellido,
                usuario_password AS clave
                FROM usuario
                WHERE usuario_username = :usuario
                LIMIT 1;';

        $data = array(':usuario' => $username,);
        $res = $database->querySQL($sql, $data);

        if (count($res) > 0) {
            if (password_verify($password, $res[0]['clave'])) {
                $this->usr_id = $res[0]['id'];
                $this->usr_nombre = $res[0]['nombre'];
                $this->usr_apellido = $res[0]['apellido'];
                return true;
            }
        }
        return false;
    }

    public function getUsuario()
    {
        $datosUsuario = [
            'Id' => $this->usr_id,
            'Nombre' => $this->usr_nombre,
            'Apellido' => $this->usr_apellido,
        ];
        return $datosUsuario;
    }

}
