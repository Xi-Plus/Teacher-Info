<!DOCTYPE html>
<?php
require('config/config.php');
require("func/teachertype.php");
require("func/school_list.php");
require("func/emailtype.php");
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
<title><?=$C["titlename"]?>/教師填報</title>

<style type="text/css">
body {
	padding-top: 4.5rem;
}
</style>
</head>
<body>
<?php
require("header.php");
if ($step == 0) {
	$step ++;
} else if ($step == 1) {
	if ($school === false) {
		?>
		<div class="alert alert-danger alert-dismissible" role="alert">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			查無此學校
		</div>
		<?php
	} else {
		$sth = $G["db"]->prepare("SELECT *  FROM `teacher_data` WHERE `school_id` = :school_id AND `name` = :name ORDER BY `updatetime` DESC LIMIT 1");
		$sth->bindValue(":school_id", $schoolid);
		$sth->bindValue(":name", $_POST["teachername"]);
		$sth->execute();
		$old = $sth->fetch(PDO::FETCH_ASSOC);
		if ($old !== false) {
			?>
			<div class="alert alert-info alert-dismissible" role="alert">
				<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				發現舊資料，可以選擇不修改
			</div>
			<?php
		}
		$step++;
	}
} else if ($step == 2) {
	$hash = md5(uniqid(rand(),true));
	$phone = "";
	if ($_POST["phone1"] !== "" && $_POST["phone2"] !== "") {
		$phone .= $_POST["phone1"]."-".$_POST["phone2"];
		if ($_POST["phone3"] !== "") {
			$phone .= "#".$_POST["phone3"];
		}
	}
	$sth = $G["db"]->prepare("SELECT *  FROM `teacher_data` WHERE `school_id` = :school_id AND `name` = :name ORDER BY `updatetime` DESC LIMIT 1");
	$sth->bindValue(":school_id", $schoolid);
	$sth->bindValue(":name", $_POST["teachername"]);
	$sth->execute();
	$old = $sth->fetch(PDO::FETCH_ASSOC);
	$useoldlist = array();
	if ($_POST["teachertype"] == "useold") {
		$_POST["teachertype"] = $old["teacher_type"];
		$useoldlist[]= "教師分類";
	}
	if (isset($_POST["old_phone"])) {
		$phone = $old["phone"];
		$useoldlist[]= "電話";
	}
	if (isset($_POST["old_mobile"])) {
		$_POST["mobile"] = $old["mobile"];
		$useoldlist[]= "手機";
	}
	if (isset($_POST["old_email"])) {
		$_POST["email"] = $old["email"];
		$useoldlist[]= "Email";
	}
	if (isset($_POST["old_emailtype"])) {
		$_POST["emailtype"] = $old["email_type"];
		$useoldlist[]= "電子報";
	} else {
		$_POST["emailtype"] = json_encode($_POST["emailtype"] ?? array());
	}
	$sth = $G["db"]->prepare("INSERT INTO `teacher_data` (`school_id`, `name`, `teacher_type`, `phone`, `mobile`, `email`, `email_type`, `year`, `hash`) VALUES (:school_id, :name, :teacher_type, :phone, :mobile, :email, :email_type, :year, :hash)");
	$sth->bindValue(":school_id", $_POST["schoolid"]);
	$sth->bindValue(":name", $_POST["teachername"]);
	$sth->bindValue(":teacher_type", $_POST["teachertype"]);
	$sth->bindValue(":phone", $phone);
	$sth->bindValue(":mobile", $_POST["mobile"]);
	$sth->bindValue(":email", $_POST["email"]);
	$sth->bindValue(":email_type", $_POST["emailtype"]);
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
	if (count($useoldlist)) {
		?>
		<div class="alert alert-info alert-dismissible" role="alert">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			以下不修改：<?=implode("、", $useoldlist)?>
		</div>
		<?php
	}
	if ($_POST["email"] !== "") {
		$mail = array(
			"sitename"=>$C["sitename"],
			"url"=>$C["domain"].$C["path"],
			"school"=>$D['school_list'][$_POST["schoolid"]]["name"],
			"name"=>$_POST["teachername"],
			"teachertype"=>$D['teachertypeall'][$_POST["teachertype"]]["name"],
			"phone"=>$phone,
			"mobile"=>$_POST["mobile"],
			"email"=>$_POST["email"],
			"emailtype"=>"",
			"year"=>$_POST["schoolyear"],
			"updatetime"=>date("Y-m-d H:i:s"),
			"hash"=>$hash
		);
		foreach (json_decode($_POST["emailtype"], true) as $id) {
			$mail["emailtype"] .= $D['emailtypeall'][$id]["name"]." ";
		}
		ob_start();
		require("mail.html");
		$mailtext = ob_get_contents();
		ob_end_clean();
		$subject = $C["sitename"]." 確認信";
		$headers = "MIME-Version: 1.0\r\n".
					"Content-type: text/html; charset=UTF-8\r\n".
					"From: ".$C["mail"];
		$mail_sent = mail($mail["email"], $subject, $mailtext, $headers);
		if ($mail_sent) {
			?>
			<div class="alert alert-info alert-dismissible" role="alert">
				<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				已發送確認信
			</div>
			<?php
		} else {
			?>
			<div class="alert alert-warning alert-dismissible" role="alert">
				<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				確認信發送失敗
			</div>
			<?php
		}
	}
}
?>
<div class="container">
	<h2>教師填報 步驟<?=$step?></h2>
	<?php
	if ($step == 1) {
	?>
	<form method="post">
		<input type="hidden" name="step" value="<?=$step?>">
		<div class="row">
			<label class="col-sm-3 col-md-2 form-control-label"><i class="fa fa-graduation-cap itemicon" aria-hidden="true"></i> 學校名稱</label>
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
						<option value="<?=$school['id']?>"><?=$school['name']?></option>
						<?php
					}
					?>
				</select>
			</div>
		</div>
		<div class="row">
			<label class="col-sm-3 col-md-2 form-control-label"><i class="fa fa-user itemicon" aria-hidden="true"></i> 教師姓名</label>
			<div class="col-sm-9 col-md-10">
				<input type="text" class="form-control" name="teachername" required autocomplete="name">
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
		<div class="row">
			<label class="col-sm-3 col-md-2 form-control-label"><i class="fa fa-graduation-cap itemicon" aria-hidden="true"></i> 學校</label>
			<label class="col-sm-9 col-md-10 form-control-label">
				<?=$school["name"]?>
			</label>
			<input type="hidden" name="schoolid" value="<?=$schoolid?>">
		</div>
		<div class="row">
			<label class="col-sm-3 col-md-2 form-control-label"><i class="fa fa-user itemicon" aria-hidden="true"></i> 教師姓名</label>
			<label class="col-sm-9 col-md-10 form-control-label">
				<?=$_POST["teachername"]?>
			</label>
			<input type="hidden" name="teachername" value="<?=$_POST["teachername"]?>">
		</div>
		<div class="row">
			<label class="col-sm-3 col-md-2 form-control-label"><i class="fa fa-calendar" aria-hidden="true"></i> 學年度</label>
			<div class="col-sm-9 col-md-10 form-inline">
				<input type="number" class="form-control" name="schoolyear" value="<?=$G["schoolyear"]?>" required>
			</div>
		</div>
		<div class="row">
			<label class="col-sm-3 col-md-2 form-control-label" for="schoolid2"><i class="fa fa-bookmark itemicon" aria-hidden="true"></i> 教師分類</label>
			<div class="col-sm-9 col-md-10 form-inline">
				<select name="teachertype" id="teachertype" class="form-control" required>
					<option hidden value="">請選取</option>
					<?php
					if ($old) {
						?>
						<option value="useold" selected>不修改</option>
						<?php
					}
					?>
					<?php
					foreach ($D['teachertype'] as $id => $teachertype) {
						?>
						<option value="<?=$id?>"><?=$teachertype["name"]?></option>
						<?php
					}
					?>
				</select>
			</div>
		</div>
		<div class="row">
			<label class="col-sm-3 col-md-2 form-control-label"><i class="fa fa-phone itemicon" aria-hidden="true"></i> 電話</label>
			<div class="col-sm-9 col-md-10 form-inline">
				<?php
				if ($old) {
					?>
					<label>
						<input type="checkbox" name="old_phone" checked> 
						不修改 
					</label>
					<?php
				}
				?>
				<input type="text" class="form-control" name="phone1" id="phone1" size="4" maxlength="3" placeholder="區碼" autocomplete="off">-
				<input type="text" class="form-control" name="phone2" id="phone2" size="8" maxlength="8" placeholder="號碼" autocomplete="off">#
				<input type="text" class="form-control" name="phone3" id="phone3" size="4" maxlength="3" placeholder="分機" autocomplete="off">
			</div>
		</div>
		<div class="row">
			<label class="col-sm-3 col-md-2 form-control-label"><i class="fa fa-mobile itemicon" aria-hidden="true"></i> 手機</label>
			<div class="col-sm-9 col-md-10 form-inline">
				<?php
				if ($old) {
					?>
					<label>
						<input type="checkbox" name="old_mobile" checked> 
						不修改 
					</label>
					<?php
				}
				?>
				<input type="text" class="form-control" name="mobile" id="mobile" size="15" maxlength="15" placeholder="手機" autocomplete="tel">
			</div>
		</div>
		<div class="row">
			<label class="col-sm-3 col-md-2 form-control-label"><i class="fa fa-envelope itemicon" aria-hidden="true"></i> Email</label>
			<div class="col-sm-9 col-md-10">
				<?php
				if ($old) {
					?>
					<label>
						<input type="checkbox" name="old_email" checked> 
						不修改 
					</label>
					<?php
				}
				?>
				<input type="email" class="form-control" id="email" name="email" autocomplete="email">
			</div>
		</div>
		<div class="row">
			<label class="col-sm-3 col-md-2 form-control-label"><i class="fa fa-commenting-o itemicon" aria-hidden="true"></i> 電子報</label>
			<div class="col-sm-9 col-md-10">
				<?php
				if ($old) {
					?>
					<label>
						<input type="checkbox" name="old_emailtype" checked> 
						不修改 
					</label>
					<?php
				}
				?>
				<div class="checkbox">
					<?php
					foreach ($D['emailtype'] as $id => $emailtype) {
						?><label class="checkbox-inline"">
							<input type="checkbox" name="emailtype[]" id="emailtype" value="<?=$id?>" checked><?=htmlentities($emailtype["name"])?>
						</label> <?php
					}
					?>
				</div>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-9 offset-sm-3 col-md-10 offset-md-2">
				<button type="submit" class="btn btn-success"><i class="fa fa-check" aria-hidden="true"></i> 送出</button>
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
