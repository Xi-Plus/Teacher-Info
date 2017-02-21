<?php
require('config/config.php');
require("func/school_list.php");
if (isset($_POST["download"]) || isset($_POST["importable"])) {
	header('Content-Description: File Transfer');
	header('Content-Type: text/csv; charset=utf-8');
	header('Content-Disposition: attachment; filename="'.$C["sitename"]."-學校列表".(isset($_POST["importable"])?"(可匯入)":"")."-".date("YmdHis").'.csv');
	header('Expires: 0');
	header('Cache-Control: must-revalidate');
	header('Pragma: public');
	echo chr(239).chr(187).chr(191);
	if (isset($_POST["download"])) {
		echo "學校編號,學校名稱,使用中\n";
	}
	foreach ($D['school_list'] as $schoolid => $school) {
		echo $school["id"].",";
		echo $school["name"].",";
		if (isset($_POST["download"])) {
			echo $G["inuse"][$school["inuse"]];
		} else {
			echo $school["inuse"];
		}
		echo "\n";
	}
	exit;
}
?>
<!DOCTYPE html>
<html lang="zh-Hant-TW">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css" integrity="sha384-rwoIResjU2yc3z8GV/NPeZWAv56rSmLldC3R/AZzGRnGxQQKnKkoFVhFQhNUwEyJ" crossorigin="anonymous">
<title><?=$C["titlename"]?>/學校列表</title>

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
<div class="container-fluid">
	<h2>學校列表</h2>
	<form action="" method="post">
		<button class="btn btn-default" type="submit" name="view"><i class="fa fa-eye" aria-hidden="true"></i> 檢視</button>
		<button class="btn btn-success" type="submit" name="download"><i class="fa fa-download" aria-hidden="true"></i> 下載</button>
		<button class="btn btn-success" type="submit" name="importable"><i class="fa fa-cloud-download" aria-hidden="true"></i> 下載成可匯入格式</button>
	</form>
	<?php
	if (isset($_POST["view"])) {
	?>
	<div class="table-responsive">
		<table class="table">
			<tr>
				<th>學校編號</th>
				<th>學校名稱</th>
				<th>使用中</th>
			</tr>
			<?php
			foreach ($D['school_list'] as $schoolid => $school) {
				?>
				<tr>
					<td><?=$school["id"]?></td>
					<td><?=$school["name"]?></td>
					<td><?=$G["inuse"][$school["inuse"]]?></td>
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
