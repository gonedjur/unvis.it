<?php

class DBHandler {
	protected $pdo;
	
	function __construct(){
		
		$this->pdo = new PDO('mysql:host=localhost;dbname=unvisit_korgen;charset=utf8', "user","password");

	}
	
	function read($hash){
		$stmt = $this->pdo->prepare('SELECT body FROM cached WHERE hash = :hash');
		$stmt->execute(array('hash' => $hash));
		foreach ($stmt as $row) {
		    return $row[0];
		}
		return null;
	}
	
	function cache($hash, $body){
		$stmt = $this->pdo->prepare("INSERT INTO cached (hash, body) VALUES (:hash, :body)");
		$stmt->bindParam(':hash', $hash);
		$stmt->bindParam(':body', $body);
		$stmt->execute();
	}

}
