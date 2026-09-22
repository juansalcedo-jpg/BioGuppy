<?php

namespace BioGuppy\Lib;

use PDO;
use PDOException;

class Connection{
    // Unica instancia de la clase (patron Singleton): garantiza que toda la
    // aplicacion comparta la misma conexion PDO durante el request, en vez
    // de abrir una conexion nueva cada vez que un Model necesita la BD.
    private static $instance = null;

    private $server;
    private $user;
    private $password;
    private $database;
    private $port;
    private $link;

    // Constructor privado: evita que se haga "new Connection()" desde fuera
    // de la clase. La unica forma de obtener la conexion es Connection::getInstance().
    private function __construct(){
        $this->setConnection();
        $this->connect();
    }

    // Evita clonar la instancia (lo que crearia una segunda conexion).
    private function __clone(){}

    // Evita reconstruir la instancia al deserializarla (mismo motivo que __clone).
    public function __wakeup(){
        throw new \Exception("No se puede deserializar un Singleton.");
    }

    public static function getInstance(){
        if(self::$instance === null){
            self::$instance = new self();
        }
        return self::$instance;
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

            // Se fija explicitamente UTF-8 para esta conexion: sin esto, en Windows
            // PHP y PostgreSQL pueden no coincidir en como interpretan tildes y
            // caracteres especiales (ej. "Inspección", "José"), causando que
            // comparaciones y textos con acentos fallen o se guarden mal.
            $this->link->exec("SET client_encoding TO 'UTF8'");
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
