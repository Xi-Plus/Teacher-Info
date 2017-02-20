<!DOCTYPE html>
<?php
require('config/config.php');
require("func/school_list.php");
?>
<html lang="zh-Hant-TW">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css" integrity="sha384-rwoIResjU2yc3z8GV/NPeZWAv56rSmLldC3R/AZzGRnGxQQKnKkoFVhFQhNUwEyJ" crossorigin="anonymous">
<title><?=$C["titlename"]?>/教師資料</title>

<style type="text/css">
body {
	padding-top: 4.5rem;
}
</style>
</head>
<body>
<?php
require("header.php");
require("func/teachertype.php");
require("func/emailtype.php");
$schoolid = $_POST["school"] ?? "";
?>
<div class="container-fluid">
	<h2>教師資料</h2>
	<form action="" method="post" id="form">
		<select name="school" class="form-control" required onchange="form.submit();">
			<option value="" hidden>請選擇</option>
			<option value="all" <?=($schoolid=="all"?"selected":"")?>>全部學校</option>
			<?php
			foreach ($D['school_list'] as $school) {
				?>
				<option value="<?=$school["id"]?>" <?=($schoolid==$school["id"]?"selected":"")?>><?=$school["name"]?></option>
				<?php
			}
			?>
		</select>
	</form>
	<?php
	if (isset($_POST["school"])) {
		if ($_POST["school"] == "all") {
			$sth = $G["db"]->prepare("SELECT * FROM `teacher_data` `d1` WHERE `updatetime` = (
				SELECT MAX(`d2`.`updatetime`) FROM `teacher_data` `d2` WHERE `d1`.`school_id` = `d2`.`school_id` AND `d1`.`name` = `d2`.`name`) ORDER BY `school_id` ASC");
		} else {
			$sth = $G["db"]->prepare("SELECT * FROM `teacher_data` `d1` WHERE `updatetime` = (
				SELECT MAX(`d2`.`updatetime`) FROM `teacher_data` `d2` WHERE `d1`.`school_id` = `d2`.`school_id` AND `d1`.`name` = `d2`.`name`) AND `school_id` = :school_id ORDER BY `school_id` ASC");
			$sth->bindValue(":school_id", $_POST["school"]);
		}
		$sth->execute();
		$row = $sth->fetchAll(PDO::FETCH_ASSOC);
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
					<td><?=$D['school_list'][$data["school_id"]]["name"]?></td>
					<td><?=$data["name"]?></td>
					<td><?=$D['teachertypeall'][$data["teacher_type"]]["name"]?></td>
					<td><?=$data["phone"]?></td>
					<td><?=$data["mobile"]?></td>
					<td><?=$data["email"]?></td>
					<td><?php
					foreach (json_decode($data["email_type"]) as $id) {
						echo $D['emailtypeall'][$id]["name"]." ";
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
