<?php
    class DB 
    {
        private static $servername = "localhost";
        private static $username = "root";
        private static $password = "";
        private static $dbname = "villathemedb";

        private static $instance = null;
        public static function getInstance() {
            if(!isset(self::$instance)) {
                self::$instance = new mysqli(self::$servername, self::$username, self::$password, self::$dbname);
                if(self::$instance->connect_error){
                    die("Connection failed: ". self::$instance->connect_error);
                }
            }
            return self::$instance;
        }
    }
?>