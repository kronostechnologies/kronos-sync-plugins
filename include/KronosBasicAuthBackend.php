<?php

class KronosBasicAuthBackend extends Sabre_DAV_Auth_Backend_AbstractBasic{
	
	/**
	 * @var PDO 
	 */
	protected $pdo;
	
	public function __construct(PDO $pdo){
		$this->pdo = $pdo;
	}
	
	public function validateUserPass($username, $password){
		$stmt = $this->pdo->prepare('SELECT email, password FROM kronos_users WHERE email = ? AND SHA1(?) = password');
		$stmt->execute(array($username, $password));
		return $stmt->fetch();
	}
	
}
