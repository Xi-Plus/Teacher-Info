<?php

$sth = $G["db"]->prepare('SELECT * FROM `email_type`');
$sth->execute();
$row = $sth->fetchAll(PDO::FETCH_ASSOC);
$D['emailtype'] = array();
foreach ($row as $temp) {
	$D['emailtype'][$temp['id']] = $temp;
}

?>
