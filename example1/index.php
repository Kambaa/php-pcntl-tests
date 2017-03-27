<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

$pidArr=[];

$start =microtime(true);
for($i=1;$i<10;$i++){
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
		echo "[CHILD] hello from child.".PHP_EOL;
		sleep(2);
		exit("[CHILD] child process finished!".PHP_EOL);
	}
}

foreach($pidArr as $pid){
	pcntl_wait($pid); //Protect against Zombie children
}

echo "[PARENT] all child processes has been completed. Execution time: ".(microtime(true)-$start).PHP_EOL;
