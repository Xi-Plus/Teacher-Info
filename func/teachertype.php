<?php

$sth = $G["db"]->prepare('SELECT * FROM `teacher_type`');
$sth->execute();
$row = $sth->fetchAll(PDO::FETCH_ASSOC);
$D['teachertype'] = array();
$D['teachertypeall'] = array();
foreach ($row as $temp) {
	$D['teachertype'][$temp['id']] = $temp;
	if ($temp['inuse'] == 1) {
		$D['teachertypeall'][$temp['id']] = $temp;
	}
}

?>
