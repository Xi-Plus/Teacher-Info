<!DOCTYPE html>
<?php
require("config/config.php");
?>
<html lang="zh-Hant-TW">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css" integrity="sha384-rwoIResjU2yc3z8GV/NPeZWAv56rSmLldC3R/AZzGRnGxQQKnKkoFVhFQhNUwEyJ" crossorigin="anonymous">
<title><?=$C["titlename"]?></title>

<style type="text/css">
body {
	padding-top: 4.5rem;
}
</style>

</head>
<body>

<?php
require("header.php");
?>
<div class="container">
	<div class="jumbotron">
		<h1><?=$C["sitename"]?></h1>
		<p class="lead"></p>
		<p>
			<a class="btn btn-lg btn-success" href="<?=$C["path"]?>/school/" role="button">
				<i class="fa fa-graduation-cap" aria-hidden="true"></i>
				學校填報
				<i class="fa fa-list" aria-hidden="true"></i>
			</a>
			<a class="btn btn-lg btn-success" href="<?=$C["path"]?>/teacher/" role="button">
				<i class="fa fa-user" aria-hidden="true"></i>
				教師填報
				<i class="fa fa-list" aria-hidden="true"></i>
			</a>
		</p>
		<p>
			<a class="btn btn-lg btn-primary" href="<?=$C["path"]?>/export/school/" role="button">
				<i class="fa fa-graduation-cap" aria-hidden="true"></i>
				匯出學校資料
				<i class="fa fa-download" aria-hidden="true"></i>
			</a>
			<a class="btn btn-lg btn-primary" href="<?=$C["path"]?>/export/teacher/" role="button">
				<i class="fa fa-user" aria-hidden="true"></i>
				匯出教師資料
				<i class="fa fa-download" aria-hidden="true"></i>
			</a>
			<a class="btn btn-lg btn-primary" href="<?=$C["path"]?>/export/schoollist/" role="button">
				<i class="fa fa-graduation-cap" aria-hidden="true"></i>
				匯出學校列表
				<i class="fa fa-download" aria-hidden="true"></i>
			</a>
		</p>
		<p>
			<a class="btn btn-lg btn-primary" href="<?=$C["path"]?>/manage/schoollist/" role="button">
				<i class="fa fa-graduation-cap" aria-hidden="true"></i>
				學校管理
				<i class="fa fa-pencil" aria-hidden="true"></i>
			</a>
			<a class="btn btn-lg btn-primary" href="<?=$C["path"]?>/manage/teachertype/" role="button">
				<i class="fa fa-user" aria-hidden="true"></i>
				教師管理
				<i class="fa fa-pencil" aria-hidden="true"></i>
			</a>
			<a class="btn btn-lg btn-primary" href="<?=$C["path"]?>/manage/emailtype/" role="button">
				<i class="fa fa-envelope" aria-hidden="true"></i>
				電子報管理
				<i class="fa fa-pencil" aria-hidden="true"></i>
			</a>
		</p>
	</div>
</div>

<script src="https://code.jquery.com/jquery-3.1.1.slim.min.js" integrity="sha384-A7FZj7v+d/sdmMqp/nOQwliLvUsJfDHW+k9Omg/a/EheAdgtzNs3hpfag6Ed950n" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.4.0/js/tether.min.js" integrity="sha384-DztdAPBWPRXSA/3eYEEUWrWCy7G5KFbe8fFjk5JAIxUYHKkDx6Qin1DkWx51bBrb" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/js/bootstrap.min.js" integrity="sha384-vBWWzlZJ8ea9aCX4pEW3rVHjgjt7zpkNpZk+02D9phzyeVkE+jo0ieGizqPLForn" crossorigin="anonymous"></script>
<script src="https://use.fontawesome.com/4c0a12abc0.js"></script>
</body>
</html>
