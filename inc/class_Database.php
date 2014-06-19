<?php

class Database {
 
    private $dbh;
    private $error;
    private $stmt;
 
    public function __construct() {

        $options = array(
            PDO::ATTR_PERSISTENT => true,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        );

        try{
            $this->dbh = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";port=" . DB_PORT, DB_USER, DB_PASS, $options);
            $this->dbh->exec("SET NAMES 'utf8'");
        }

        catch(PDOException $e) {
        	echo "<p><strong>Database connection could not be established.</strong><br/>Please check your connection settings.</p>";
            $this->error = $e->getMessage(); // debug; remove from production code
            exit;
        }
    }

    public function query($query) {
	    $this->stmt = $this->dbh->prepare($query);
	}

	public function bind($param, $value, $type = NULL, $length = NULL) {

	    if (is_null($type)) {
	        switch (true) {
	            case is_int($value):
	                $type = PDO::PARAM_INT;
	                break;
	            case is_bool($value):
	                $type = PDO::PARAM_BOOL;
	                break;
	            case is_null($value):
	                $type = PDO::PARAM_NULL;
	                break;
	            default:
	                $type = PDO::PARAM_STR;
	        }
	    }

	    $this->stmt->bindParam($param, $value, $type, $length);

	}

	public function execute() {
	    return $this->stmt->execute();
	}

	public function single() {
	    $this->execute();
	    return $this->stmt->fetch(PDO::FETCH_ASSOC);
	}

	public function all(){
		$this->execute();
		return $this->stmt->fetchAll(PDO::FETCH_ASSOC);
	}

	public function lastInsertId() {
	    return $this->dbh->lastInsertId();
	}

}

?>
