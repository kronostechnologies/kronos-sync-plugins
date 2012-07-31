<?php
require_once('../config/config.php');
$kronos_users_id = array();

$pdo = new PDO('mysql:host='.MYSQL_DBHOST.';dbname='.MYSQL_DBNAME, MYSQL_DBUSER, MYSQL_DBPASSWORD);
$sql = 'SELECT id FROM kronos_users';
$stmt = $pdo->prepare($sql);
$stmt->execute(array());

while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
	$kronos_users_id[] = $row['id'];
}
if(empty($kronos_users_id)) throw new Exception('To run this script, you need at least one kronos_users_id');

$total_row_inserted = 0;
foreach($kronos_users_id as $kronos_user_id){
	$notes = 'Lorem Ipsum Dolored Intra Supra Mega Intergalactic Kironos Agenda';
	$type = array('Uncategorized', 'Biils', 'Food', 'Dining', 'Bar', 'Programming');
	$subject = array('Maladie cent un', 'Les etoiles et nous', 'Bob et bobette', 'Gens d\'ici');
	$max = rand(1,25);
	$total_row_inserted += $max;
	for($i = 0; $i < $max; $i++){
		$created = date('Y-m-d H:i:s');
		$time_start = time() - (rand(1,7) * rand(1,24) * rand(1,60) * rand(0,60));
		$time_end = time() + (rand(1,7) * rand(1,24) * rand(1,60) * rand(0,60));
		$sql = 'INSERT INTO kronos_agenda(fk_kronos_users, created, modified, notes, type, subject, private, time_start, time_end, uri) VALUES(?, ?, ?, ?, ?, ?, ?, ?, ?, ?)';
		$stmt = $pdo->prepare($sql);
		if(!$stmt) {
			echo 'Error info:'."\n";
			echo print_r($pdo->errorInfo(), 1);
			throw new Exception('did not work');
		}
		$stmt->execute(array($kronos_user_id, 
		                     $created, 
	        	             null, 
	                	     $notes, 
		                     $type[rand(0,count($type))], 
		                     $subject[rand(0,count($subject))],
		                     (rand(0,1) == 1 ? 'Y' : 'N'),
	                	     date('Y-m-d H:i:s' ,$time_start),
		                     date('Y-m-d H:i:s' ,$time_end),
		                     null)
		);
		
		$agenda_id = $pdo->lastInsertId();

		$sql = 'UPDATE kronos_agenda SET uri = ? WHERE id = ?';
		$stmt = $pdo->prepare($sql);
		$stmt->execute(array($agenda_id.'-'.$created.'.ics', $agenda_id));
	}
}

echo $total_row_inserted." rows inserted.\n";

