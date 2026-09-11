<?php

namespace BioGuppy\Lib;

use PDO;
use PDOException;

class Connection{
    private $server;
    private $user;
    private $password;
    private $database;
    private $port;
    private $link;

    function __construct(){
        $this->setConnection();
        $this->connect();
    }

    private function setConnection(){
        // __DIR__ hace que esta ruta funcione sin importar desde dónde se
        // ejecute PHP (Apache en web/, PHPUnit desde la raíz del proyecto, etc.)
        require __DIR__ . '/../../lib/conf/conf.php';
        $this->server = $server;
        $this->user = $user;
        $this->password = $password;
        $this->database = $database;
        $this->port = $port;
    }

    private function connect(){
        try{
            $dsn = "pgsql:host={$this->server};port={$this->port};dbname={$this->database}";
            $this->link = new PDO($dsn, $this->user, $this->password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]);
        }catch(PDOException $e){
            die("Error de conexion: " . $e->getMessage());
        }
    }

    public function getConnection(){
        return $this->link;
    }

    public function close(){
        $this->link = null;
    }
}
