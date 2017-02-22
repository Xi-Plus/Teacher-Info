<?php
require('config/config.php');
require("func/school_list.php");
require("func/teachertype.php");
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
	$sth = $G["db"]->prepare("SELECT * FROM `school_data` `d1` WHERE `updatetime` = (
		SELECT MAX(`d2`.`updatetime`) FROM `school_data` `d2` WHERE `d1`.`id` = `d2`.`id`)");
	$sth->execute();
	$row = $sth->fetchAll(PDO::FETCH_ASSOC);
	foreach ($row as $data) {
		$D['school_list'][$data["id"]]["data"] = $data;
	}
	if (isset($_POST["download"])) {
		header('Content-Description: File Transfer');
		header('Content-Type: text/csv; charset=utf-8');
		header('Content-Disposition: attachment; filename="'.$C["sitename"]."-學校資料-".date("YmdHis").'.csv');
		header('Expires: 0');
		header('Cache-Control: must-revalidate');
		header('Pragma: public');
		echo chr(239).chr(187).chr(191);
		echo "學校名稱,教師人數,學年度,確認,更新時間\n";
		foreach ($D['school_list'] as $schoolid => $school) {
			echo $school["name"].",";
			if (isset($school["data"])) {
				foreach (json_decode($school["data"]["teacher_count"]) as $id => $cnt) {
					echo $D['teachertypeall'][$id]["name"]."：".$cnt." ";
				}
				echo ",";
				echo $school["data"]["year"].",";
				echo $G["confirm"][$school["data"]["confirm"]].",";
				echo $school["data"]["updatetime"];
			} else {
				echo "未填寫,,,";
			}
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
<title><?=$C["titlename"]?>/學校資料</title>

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
	<h2>學校資料</h2>
	<form action="" method="post">
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
				<th>教師人數</th>
				<th>學年度</th>
				<th>確認</th>
				<th>更新時間</th>
			</tr>
			<?php
			foreach ($D['school_list'] as $schoolid => $school) {
				?>
				<tr>
					<td><?=$school["name"]?></td>
					<?php
					if (isset($school["data"])) {
						?>
						<td><?php
						foreach (json_decode($school["data"]["teacher_count"]) as $id => $cnt) {
							echo $D['teachertypeall'][$id]["name"]."：".$cnt." ";
						}
						?></td>
						<td><?=$school["data"]["year"]?></td>
						<td><?=$G["confirm"][$school["data"]["confirm"]]?></td>
						<td><?=$school["data"]["updatetime"]?></td>
						<?php
					} else {
						?>
						<td colspan="4">未填寫</td>
						<?php
					}
					?>
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
