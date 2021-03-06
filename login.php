<!DOCTYPE html>
<?php
require('config/config.php');
?>
<html lang="zh-Hant-TW">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css" integrity="sha384-rwoIResjU2yc3z8GV/NPeZWAv56rSmLldC3R/AZzGRnGxQQKnKkoFVhFQhNUwEyJ" crossorigin="anonymous">
<title><?=$C["titlename"]?>/管理帳號</title>

<style type="text/css">
body {
	padding-top: 4.5rem;
}
</style>
<?php
if ($C["CAPTCHAuselogin"]) {
	?><script src='https://www.google.com/recaptcha/api.js'></script><?php
}
?>
</head>
<body>
<?php

$showform = true;
if ($_GET["action"] === "login") {
	if ($U["islogin"]) {
		?>
		<div class="alert alert-info alert-dismissible" role="alert">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			已經登入了
		</div>
		<?php
		$showform = false;
	} else if (isset($_POST["account"])) {
		$captcha = true;
		if ($C["CAPTCHAuselogin"]) {
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, "https://www.google.com/recaptcha/api/siteverify");
			curl_setopt($ch, CURLOPT_POST, true);
			curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query( array( "secret"=>$C["CAPTCHAsecretkey"], "response"=>$_POST['g-recaptcha-response']) ));
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			$result = curl_exec($ch);
			curl_close($ch);
			$result = json_decode($result, true);
			$captcha = $result["success"];
		}
		$sth = $G["db"]->prepare('SELECT * FROM `account` WHERE `account` = :account');
		$sth->bindValue(":account", $_POST["account"]);
		$sth->execute();
		$account = $sth->fetch(PDO::FETCH_ASSOC);
		if ($captcha && $account !== false && password_verify($_POST["password"], $account["password"])) {
			$cookie = md5(uniqid(rand(),true));
			$sth = $G["db"]->prepare('INSERT INTO `login_session` (`account`, `cookie`) VALUES (:account, :cookie)');
			$sth->bindValue(":account", $_POST["account"]);
			$sth->bindValue(":cookie", $cookie);
			$sth->execute();
			setcookie($C["cookiename"], $cookie, time()+$C["cookieexpire"], $C["path"]);
			?>
			<div class="alert alert-success alert-dismissible" role="alert">
				<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				登入成功
			</div>
			<?php
			$U["data"] = $account;
			$U["islogin"] = true;
			$showform = false;
		} else {
			?>
			<div class="alert alert-danger alert-dismissible" role="alert">
				<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				登入失敗，<?=($captcha?"帳號或密碼錯誤":"驗證碼失敗")?>
			</div>
			<?php
		}
	}
} else if ($_GET["action"] === "logout") {
	if ($U["islogin"]) {
		$sth = $G["db"]->prepare('DELETE FROM `login_session` WHERE `cookie` = :cookie');
		$sth->bindValue(":cookie", $_COOKIE[$C["cookiename"]]);
		$sth->execute();
		setcookie($C["cookiename"], "", time(), $C["path"]);
	}
	?>
	<div class="alert alert-success alert-dismissible" role="alert">
		<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		已登出
	</div>
	<?php
	$U["islogin"] = false;
	$showform = false;
}
require("header.php");
if ($showform) {
?>
<div class="container">
	<h2>登入</h2>
	<form action="" method="post">
		<div class="row">
			<label class="col-sm-2 form-control-label"><i class="fa fa-user" aria-hidden="true"></i> 帳號</label>
			<div class="col-sm-10">
				<input class="form-control" type="text" name="account" required>
			</div>
		</div>
		<div class="row">
			<label class="col-sm-2 form-control-label"><i class="fa fa-hashtag" aria-hidden="true"></i> 密碼</label>
			<div class="col-sm-10">
				<input class="form-control" type="password" name="password" required>
			</div>
		</div>
		<?php
		if ($C["CAPTCHAuselogin"]) {
			?>
			<div class="row">
				<label class="col-sm-2 form-control-label"><i class="fa fa-hashtag" aria-hidden="true"></i> 驗證碼</label>
				<div class="col-sm-10">
					<div class="g-recaptcha" data-callback="capchaok" data-expired-callback="capchaexpire" data-sitekey="<?=$C["CAPTCHAsitekey"]?>"></div>
				</div>
			</div>
			<script type="text/javascript">
				function capchaok(){
					action.disabled = false;
				}
				function capchaexpire(){
					action.disabled = true;
				}
			</script>
			<?php
		}
		?>
		<div class="row">
			<div class="col-sm-10 offset-sm-2">
				<button type="submit" class="btn btn-success" id="action" name="action" value="new" disabled><i class="fa fa-sign-in" aria-hidden="true"></i> 登入</button>
			</div>
		</div>
	</form>
</div>

<?php
}
require("footer.php");
?>
<script src="https://code.jquery.com/jquery-3.1.1.slim.min.js" integrity="sha384-A7FZj7v+d/sdmMqp/nOQwliLvUsJfDHW+k9Omg/a/EheAdgtzNs3hpfag6Ed950n" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.4.0/js/tether.min.js" integrity="sha384-DzthAPBWPRXSA/3eYEEUWrWCy7G5KFbe8fFjk5JAIxUYHKkDx6Qin1DkWx51bBrb" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/js/bootstrap.min.js" integrity="sha384-vBWWzlZJ8ea9aCX4pEW3rVHjgjt7zpkNpZk+02D9phzyeVkE+jo0ieGizqPLForn" crossorigin="anonymous"></script>
<script src="https://use.fontawesome.com/4c0a12abc0.js"></script>
<script type="text/javascript">
$(function () {
	$('[data-toggle="tooltip"]').tooltip()
})
</script>
</body>
</html>
