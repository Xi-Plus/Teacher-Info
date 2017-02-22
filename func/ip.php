<?php

$U["ip"] = "";
foreach ($C["ipserverkey"] as $ipkey) {
	if (!empty($_SERVER[$ipkey])) {
		$U["ip"] = $_SERVER[$ipkey];
	}
}

?>
