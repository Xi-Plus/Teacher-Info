<?php
require('config/config.php');
require("func/school_list.php");
require("func/teachertype.php");
require("func/emailtype.php");
$showform = true;
if (!$U["islogin"]) {
	?>
	<div class="alert alert-danger alert-dismissible" role="alert">
		<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		此功能需要驗證帳號，請<a href="<?=$C["path"]?>/login/">登入</a>
	</div>
	<?php
	$showform = false;
} else {
	$schoolid = $_POST["school"] ?? "";
	if (isset($_POST["school"])) {
		if ($_POST["school"] == "all") {
			$sth = $G["db"]->prepare("SELECT * FROM `teacher_data` `d1` WHERE `updatetime` = (
				SELECT MAX(`d2`.`updatetime`) FROM `teacher_data` `d2` WHERE `d1`.`school_id` = `d2`.`school_id` AND `d1`.`name` = `d2`.`name` AND `d1`.`confirm` <= '1') ORDER BY `school_id` ASC");
		} else {
			$sth = $G["db"]->prepare("SELECT * FROM `teacher_data` `d1` WHERE `updatetime` = (
				SELECT MAX(`d2`.`updatetime`) FROM `teacher_data` `d2` WHERE `d1`.`school_id` = `d2`.`school_id` AND `d1`.`name` = `d2`.`name` AND `d1`.`confirm` <= '1') AND `school_id` = :school_id ORDER BY `school_id` ASC");
			$sth->bindValue(":school_id", $_POST["school"]);
		}
		$sth->execute();
		$row = $sth->fetchAll(PDO::FETCH_ASSOC);
	}
	if (isset($_POST["download"])) {
		header('Content-Description: File Transfer');
		header('Content-Type: text/csv; charset=utf-8');
		header('Content-Disposition: attachment; filename="'.$C["sitename"]."-教師資料-".($_POST["school"] == "all"?"全部學校":$D['school_list'][$schoolid]["name"])."-".date("YmdHis").'.csv');
		header('Expires: 0');
		header('Cache-Control: must-revalidate');
		header('Pragma: public');
		echo chr(239).chr(187).chr(191);
		echo "學校名稱,姓名,教師類型,電話,手機,Email,電子報,學年度,確認,更新時間\n";
		foreach ($row as $data) {
			echo $D['school_list'][$data["school_id"]]["name"].",";
			echo $data["name"].",";
			echo $D['teachertypeall'][$data["teacher_type"]]["name"].",";
			echo $data["phone"].",";
			echo $data["mobile"].",";
			echo $data["email"].",";
			foreach (json_decode($data["email_type"]) as $id) {
				echo $D['emailtypeall'][$id]["name"]." ";
			}
			echo ",";
			echo $data["year"].",";
			echo $G["confirm"][$data["confirm"]].",";
			echo $data["updatetime"];
			echo "\n";
		}
		exit;
	}
}
?>
<!DOCTYPE html>
<html lang="zh-Hant-TW">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css" integrity="sha384-rwoIResjU2yc3z8GV/NPeZWAv56rSmLldC3R/AZzGRnGxQQKnKkoFVhFQhNUwEyJ" crossorigin="anonymous">
<title><?=$C["titlename"]?>/匯出教師資料</title>

<style type="text/css">
body {
	padding-top: 4.5rem;
}
</style>
</head>
<body>
<?php
require("header.php");
if ($showform) {
?>
<div class="container-fluid">
	<h2>匯出教師資料</h2>
	<form action="" method="post">
		<select name="school" class="form-control" required>
			<option value="" hidden>請選擇</option>
			<option value="all" <?=($schoolid=="all"?"selected":"")?>>全部學校</option>
			<?php
			foreach ($D['school_list'] as $school) {
				?>
				<option value="<?=$school["id"]?>" <?=($schoolid==$school["id"]?"selected":"")?>><?=htmlentities($school["name"])?></option>
				<?php
			}
			?>
		</select>
		<button class="btn btn-default" type="submit" name="view"><i class="fa fa-eye" aria-hidden="true"></i> 檢視</button>
		<button class="btn btn-success" type="submit" name="download"><i class="fa fa-download" aria-hidden="true"></i> 下載</button>
	</form>
	<?php
	if (isset($_POST["view"])) {
	?>
	<div class="table-responsive">
		<table class="table">
			<tr>
				<th>學校名稱</th>
				<th>姓名</th>
				<th>教師類型</th>
				<th>電話</th>
				<th>手機</th>
				<th>Email</th>
				<th>電子報</th>
				<th>學年度</th>
				<th>確認</th>
				<th>更新時間</th>
			</tr>
			<?php
			foreach ($row as $data) {
				?>
				<tr>
					<td><?=htmlentities($D['school_list'][$data["school_id"]]["name"])?></td>
					<td><?=htmlentities($data["name"])?></td>
					<td><?=htmlentities($D['teachertypeall'][$data["teacher_type"]]["name"])?></td>
					<td><?=$data["phone"]?></td>
					<td><?=$data["mobile"]?></td>
					<td><?=htmlentities($data["email"])?></td>
					<td><?php
					foreach (json_decode($data["email_type"]) as $id) {
						echo htmlentities($D['emailtypeall'][$id]["name"])." ";
					}
					?></td>
					<td><?=$data["year"]?></td>
					<td><?=$G["confirm"][$data["confirm"]]?></td>
					<td><?=$data["updatetime"]?></td>
				</tr>
				<?php
			}
			?>
		</table>
	</div>
	<?php
	}
	?>
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
