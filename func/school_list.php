<?php

$sth = $G["db"]->prepare('SELECT * FROM `school_list`');
$sth->execute();
$row = $sth->fetchAll(PDO::FETCH_ASSOC);
$D['school_list'] = array();
foreach ($row as $temp) {
	$D['school_list'][$temp['id']] = $temp;
}

?>
