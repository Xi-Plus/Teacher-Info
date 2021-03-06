<!DOCTYPE html>
<?php
require('config/config.php');
$step = $_POST["step"] ?? "0";
$schoolid = $_POST["schoolid"] ?? "";
if ($schoolid !== false) {
	$sth = $G["db"]->prepare("SELECT * FROM `school_list` WHERE `id` = :id");
	$sth->bindValue(":id", $schoolid);
	$sth->execute();
	$school = $sth->fetch(PDO::FETCH_ASSOC);
}
?>
<html lang="zh-Hant-TW">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css" integrity="sha384-rwoIResjU2yc3z8GV/NPeZWAv56rSmLldC3R/AZzGRnGxQQKnKkoFVhFQhNUwEyJ" crossorigin="anonymous">
<title><?=$C["titlename"]?>/學校填報</title>

<style type="text/css">
body {
	padding-top: 4.5rem;
}
</style>
<?php
if ($C["CAPTCHAuseschool"]) {
	?><script src='https://www.google.com/recaptcha/api.js'></script><?php
}
?>
</head>
<body>
<?php
require("header.php");
if ($step == 0) {
	$step ++;
} else if ($step == 1) {
	$_POST["schoolid2"] = strtoupper($_POST["schoolid2"]);
	if ($school === false) {
		?>
		<div class="alert alert-danger alert-dismissible" role="alert">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			查無此學校
		</div>
		<?php
	} else if ($_POST["schoolid"] !== $_POST["schoolid2"]) {
		?>
		<div class="alert alert-danger alert-dismissible" role="alert">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			學校編號錯誤
		</div>
		<?php
	} else {
		$step++;
	}
} else if ($step == 2) {
	$captcha = true;
	if ($C["CAPTCHAuseschool"]) {
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
	if (!$captcha) {
		$step++;
		?>
		<div class="alert alert-danger alert-dismissible" role="alert">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			驗證碼失敗，<a href="" onclick="history.back()">回上一頁</a>
		</div>
		<?php
	} else {
		$hash = md5(uniqid(rand(),true));
		$sth = $G["db"]->prepare("INSERT INTO `school_data` (`id`, `teacher_count`, `year`, `hash`) VALUES (:id, :teacher_count, :year, :hash);");
		$sth->bindValue(":id", $schoolid);
		$sth->bindValue(":teacher_count", json_encode($_POST["teachercnt"], JSON_NUMERIC_CHECK));
		$sth->bindValue(":year", $_POST["schoolyear"]);
		$sth->bindValue(":hash", $hash);
		$sth->execute();
		$step++;
		?>
		<div class="alert alert-success alert-dismissible" role="alert">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			已收到資料
		</div>
		<?php
	}
}
?>
<script type="text/javascript">
function filter(){
	console.log(filter_name.value);
	if (filter_name.value != "") {
		for (var i = 1; i < schoolid.children.length; i++) {
			if (schoolid.children[i].innerText.search(filter_name.value) == -1) {
				schoolid.children[i].hidden = true;
			} else {
				schoolid.children[i].hidden = false;
			}
		}
	} else {
		for (var i = 1; i < schoolid.children.length; i++) {
			schoolid.children[i].hidden = false;
		}
	}
}
</script>
<div class="container">
	<h2>學校填報 步驟<?=$step?></h2>
	<?php
	if ($step == 1) {
	?>
	<form method="post">
		<input type="hidden" name="step" value="<?=$step?>">
		<div class="row">
			<label class="col-sm-3 col-md-2 form-control-label"><i class="fa fa-header itemicon" aria-hidden="true"></i> 學校名稱</label>
			<div class="col-sm-9 col-md-10">
				<input type="text" class="form-control" id="filter_name" oninput="filter()" placeholder="輸入文字以篩選">
				<select name="schoolid" id="schoolid" class="form-control" required>
					<option hidden value="">請選取</option>
					<?php
					$sth = $G["db"]->prepare("SELECT * FROM `school_list`");
					$sth->execute();
					$schools = $sth->fetchAll(PDO::FETCH_ASSOC);
					foreach ($schools as $school) {
						?>
						<option value="<?=$school['id']?>"><?=htmlentities($school['name'])?></option>
						<?php
					}
					?>
				</select>
			</div>
		</div>
		<div class="row">
			<label class="col-sm-3 col-md-2 form-control-label"><i class="fa fa-hashtag itemicon" aria-hidden="true"></i> 學校代碼</label>
			<div class="col-sm-9 col-md-10">
				<input type="text" class="form-control" name="schoolid2" required>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-9 offset-sm-3 col-md-10 offset-md-2">
				<button type="submit" class="btn btn-success"><i class="fa fa-check" aria-hidden="true"></i> 送出</button>
			</div>
		</div>
	</form>
	<?php
	} else if ($step == 2) {
	?>
	<form method="post">
		<input type="hidden" name="step" value="<?=$step?>">
		<input type="hidden" name="schoolid" value="<?=$schoolid?>">
		<div class="row">
			<label class="col-sm-3 col-md-2 form-control-label"><i class="fa fa-graduation-cap itemicon" aria-hidden="true"></i> 學校</label>
			<label class="col-sm-9 col-md-10 form-control-label">
				<?=$school["id"]?> <?=htmlentities($school["name"])?>
			</label>
		</div>
		<div class="row">
			<label class="col-sm-3 col-md-2 form-control-label"><i class="fa fa-calendar" aria-hidden="true"></i> 學年度</label>
			<div class="col-sm-9 col-md-10 form-inline">
				<input type="number" class="form-control" name="schoolyear" value="<?=$G["schoolyear"]?>">
			</div>
		</div>
		<div class="row">
			<label class="col-sm-3 col-md-2 form-control-label" for="schoolid2"><i class="fa fa-user itemicon" aria-hidden="true"></i> 教師人數</label>
			<div class="col-sm-9 col-md-10 form-inline">
				<div class="row">
					<?php
					require("func/teachertype.php");
					foreach ($D['teachertype'] as $id => $teachertype) {
						?>
						<div class="col-sm-6 col-md-4">
							<?=htmlentities($teachertype["name"])?>：<br>
							<input type="number" class="form-control" name="teachercnt[<?=$id?>]" min="0" required>
						</div>
						<?php
					}
					?>
				</div>
			</div>
		</div>
		<?php
		if ($C["CAPTCHAuseschool"]) {
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
			<div class="col-sm-9 offset-sm-3 col-md-10 offset-md-2">
				<button type="submit" id="action" class="btn btn-success" disabled><i class="fa fa-check" aria-hidden="true"></i> 送出</button>
			</div>
		</div>
	</form>
	<?php
	}
	?>
</div>

<?php
require("footer.php");
?>
<script src="https://code.jquery.com/jquery-3.1.1.slim.min.js" integrity="sha384-A7FZj7v+d/sdmMqp/nOQwliLvUsJfDHW+k9Omg/a/EheAdgtzNs3hpfag6Ed950n" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/tether/1.4.0/js/tether.min.js" integrity="sha384-DztdAPBWPRXSA/3eYEEUWrWCy7G5KFbe8fFjk5JAIxUYHKkDx6Qin1DkWx51bBrb" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/js/bootstrap.min.js" integrity="sha384-vBWWzlZJ8ea9aCX4pEW3rVHjgjt7zpkNpZk+02D9phzyeVkE+jo0ieGizqPLForn" crossorigin="anonymous"></script>
<script src="https://use.fontawesome.com/4c0a12abc0.js"></script>
<script type="text/javascript">
$(function () {
	$('[data-toggle="tooltip"]').tooltip()
})
</script>
</body>
</html>
