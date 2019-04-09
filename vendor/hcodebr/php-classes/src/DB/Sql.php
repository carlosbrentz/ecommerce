<?php 

namespace Hcode\DB;

class Sql {

	const HOSTNAME = "127.0.0.1";
	const USERNAME = "root";
	const PASSWORD = "";
	const DBNAME = "db_ecommerce";

	private $conn;

	public function __construct()
	{
        $opcoes = array(\PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION);
		$this->conn = new \PDO(
			"mysql:dbname=".Sql::DBNAME.";host=".Sql::HOSTNAME, 
			Sql::USERNAME,
			Sql::PASSWORD,
			$opcoes
		);
		
        $this->conn->setAttribute(\PDO::ATTR_EMULATE_PREPARES, false); 
	}

	private function setParams($statement, $parameters = array())
	{

		foreach ($parameters as $key => $value) {
			
			$this->bindParam($statement, $key, $value);

		}

	}

	private function bindParam($statement, $key, $value)
	{
		$statement->bindParam($key, $value);

	}

	public function query($rawQuery, $params = array())
	{

		$stmt = $this->conn->prepare($rawQuery);

		$this->setParams($stmt, $params);

	try{
		$stmt->execute();
            //  print_r($stmt->errorInfo());
    } catch(Exception $e){

    	throw new \Exception("Erro na execução de comando no BD: ".$e->getMessage());
    }


	}

	public function select($rawQuery, $params = array()):array
	{

		$stmt = $this->conn->prepare($rawQuery);

		$this->setParams($stmt, $params);

      try{

		$stmt->execute();

       } catch(Exception $e){

    	    throw new \Exception("Erro na execução de comando no BD: ".$e->getMessage());
       }
		return $stmt->fetchAll(\PDO::FETCH_ASSOC);

	}

}

 ?>