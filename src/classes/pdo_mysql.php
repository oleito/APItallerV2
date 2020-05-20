<?php
require_once './../src/config/db.php';

class pdoMysql
{
    /* LA CONEXION */
    private $conn;

    /** MONOLOG */
    protected $logger;

    /* DE LA BASE DE DATOS */
    private $dbHost;
    private $dbUser;
    private $dbPass;
    private $dbSchema;

    /* DEL USUARIO */
    private $userData;

    // metodos
    public function __construct($monolog_OBJ)
    {
        $this->dbHost = MySQL_DB_HOST;
        $this->dbUser = MySQL_DB_USER;
        $this->dbPass = MySQL_DB_PASS;
        $this->dbSchema = MySQL_DB_SCHEMA;

        $this->logger = $monolog_OBJ;

    }
    public function conectar()
    {
        try {
            $this->conn = new PDO(
                "mysql:host=$this->dbHost;dbname=$this->dbSchema",
                $this->dbUser,
                $this->dbPass
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        } catch (Exception $e) {
            $this->logger->warning('PDO - mysql->conectar() - ', [$e->getMessage()]);
            return null;
        }
        return $this->conn;
    }

    public function querySQL($sql, $data)
    {
        $this->conectar();
        try {
            $sth = $this->conn->prepare($sql);
            $sth->execute($data);

            return $sth->fetchAll();

        } catch (Exception $e) {
            $this->logger->warning('PDO - MySQL->querySQL() - ', [$e->getMessage()]);
            return [''];
        } finally {
            $this->desconectar();
        }
    }

    public function desconectar()
    {
        unset($this->conn);
    }
    public function __destruct()
    {
        $this->desconectar();
    }

}
