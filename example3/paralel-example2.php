<?php
ini_set('display_errors', 1);
error_reporting(E_ALL);

require_once 'Database.php';
require_once 'SingletonDbFactory.php';


$user= [
'created_by'=>"test",
'pid'=>null,
'name'=>"test"
];

DbConnectionFactory::getFactory()->getConnection("localhost","test","test","test");
// $lastId=DbConnectionFactory::getFactory()->getConnection()->insert("users",$user);
// var_dump($lastId);

// exit();


$pidArr=[];

$start =microtime(true);
for($i=1;$i<5;$i++){
	$pid = pcntl_fork();
	array_push($pidArr, $pid);
	if ($pid == -1) {
		die('could not fork');
	} else if ($pid) {
 		// we are the parent
		echo "[PARENT] a child process with $pid process id created".PHP_EOL;
		continue;
	} else {
	// we are the child
		$db=DbConnectionFactory::getFactory()->getConnection("localhost","test","test","test");
		$currPid= getmypid();
		echo "[CHILD-$currPid] data insertion started.".PHP_EOL;
		$user= [
		'created_by'=>"child",
		'pid'=>$currPid,
		'name'=>"Child Entry"
		];

		$lastId=$db->insert("users",$user);
		exit("[CHILD-$currPid] child process finished! inserted id is : $lastId".PHP_EOL);
	}
}

foreach($pidArr as $pid){
	pcntl_wait($pid); //Protect against Zombie children
}

echo "[PARENT] all child processes has been completed. Execution time: ".(microtime(true)-$start).PHP_EOL;



