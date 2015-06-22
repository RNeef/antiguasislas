<?php

/**
 * Klasse zum Aufbau einer Datenbankverbindung
 *
 * @author Rolf Neef
 */
class Database {
    
    private $user = "root";
    private $password = "";
    private $dsn = 'mysql:dbname=antiguas;host=127.0.0.1';
    
    /**
     * Funktion ist der Konstruktor und legt die Parameter für eine Verbindung
     * fest. Standard ist Local. 
     * 
     * @author Rolf Neef
     * 
     * @param none
     * 
     */
    public function __construct() {

        if(
        	$_SERVER['REMOTE_ADDR'] !== '127.0.0.1' 
        	&& $_SERVER['REMOTE_ADDR'] !=='::1'
		){
            
            $this->user = 'ruheloser_test';
            $this->password = 'Janni1964';
            $this->dsn ='mysql:dbname=ruheloser_test;host=localhost';
            
        }
    }
    
    /**
     * Funktion gibt eine Instanz der PDO Klasse zurück
     *  
     * @author Rolf Neef
     * 
     * @param none
     * @return \PDO
     */
    public function Connect(){
        
        $options  = array
            (
              PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
            );

        $dbh = new PDO($this->dsn, $this->user, $this->password, $options);
        return $dbh;
    }
}