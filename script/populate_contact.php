<?php
require_once('../config/config.php');

$emails = array('test@kronos-web.com', 'test2@kronos-web.com', 'test3@kronos-web.com', 'test4@kronos-web.com');
$first_names = array('Gertrude', 'Gontrande', 'Zephyrin', 'Luc');
$last_names = array('Tremblay', 'Leduc', 'Campeau', 'Leboeuf');
$adresses = array('67 rue du poisson', '54 avenue des patates', '12 rue du ruisseau', '98 potato street');
$postal_codes = array('g6v 3d2', 'g9j 1m0', 'j9d 4m1', 'l0a 1k8');
$cities = array('Saint-Louis du Ha Ha!', 'Levis', 'Sept-Iles', 'Victoriaville');
$provinces = array('Quebec', 'QC', 'PQ', 'Québec');

$pdo = new PDO('mysql:host='.MYSQL_DBHOST.';dbname='.MYSQL_DBNAME, MYSQL_DBUSER, MYSQL_DBPASSWORD);

for($i = 0; $i < 100; $i++) {
	$values = array($emails[rand(0, 3)], $first_names[rand(0, 3)], $last_names[rand(0, 3)], $adresses[rand(0, 3)], 
			$postal_codes[rand(0, 3)], $cities[rand(0, 3)], $provinces[rand(0, 3)]);

	$stmt = $pdo->prepare('INSERT INTO contact(email, first_name, last_name, address, 
		postal_code, city, province, modified_at) VALUES(?, ?, ?, ?, ?, ?, ?, NOW())');
	$stmt->execute($values);
}
