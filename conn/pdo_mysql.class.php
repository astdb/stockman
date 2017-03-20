<?php
    // error_reporting(0);
    class PDO_MYSQL {
       var $PDO_HOST        = PDO_C_DB_HOST;       // Hostname of our MySQL server
       var $PDO_DB          = PDO_C_DB_NAME;       // Logical database name on that server
       var $PDO_DBUSER      = PDO_C_DB_USER;       // Database user
       var $PDO_DBPW        = PDO_C_DB_PASS;       // Database user's password

       function connect() {
         $connstr = 'mysql:host=' . $this->PDO_HOST . ';dbname=' . $this->PDO_DB;
         $dbh     = new PDO($connstr, $this->PDO_DBUSER, $this->PDO_DBPW);
         return $dbh;
       }
    }
?>
