<!DOCTYPE html>
<?php
require('config/config.php');
require("func/emailtype.php");
?>
<html lang="zh-Hant-TW">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css" integrity="sha384-rwoIResjU2yc3z8GV/NPeZWAv56rSmLldC3R/AZzGRnGxQQKnKkoFVhFQhNUwEyJ" crossorigin="anonymous">
<title><?=$C["titlename"]?>/管理電子報</title>

<style type="text/css">
body {
	padding-top: 4.5rem;
}
</style>
</head>
<body>
<?php
if (isset($_POST["inuse"])) {
	$sth = $G["db"]->prepare("UPDATE `email_type` SET `inuse`=(1-`inuse`) WHERE `id`=:id");
	$sth->bindValue(":id", $_POST["inuse"]);
	$sth->execute();
	$D['emailtypeall'][$_POST["inuse"]]["inuse"] = 1-$D['emailtypeall'][$_POST["inuse"]]["inuse"];
	?>
	<div class="alert alert-success alert-dismissible" role="alert">
		<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		已將 <?=$D['emailtypeall'][$_POST["inuse"]]["name"]?> <?=$G["inuse"][$D['emailtypeall'][$_POST["inuse"]]["inuse"]]?>
	</div>
	<?php
} else if (isset($_POST["edit"])) {
	if ($_POST["emailtype"] === "new") {
		$sth = $G["db"]->prepare("INSERT INTO `email_type` (`name`) VALUES (:name)");
		$sth->bindValue(":name", $_POST["name"]);
		$sth->execute();
		?>
		<div class="alert alert-success alert-dismissible" role="alert">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			已新增 <?=$_POST["name"]?>
		</div>
		<?php
	} else {
		$sth = $G["db"]->prepare("UPDATE `email_type` SET `name` = :name WHERE `id` = :id");
		$sth->bindValue(":name", $_POST["name"]);
		$sth->bindValue(":id", $_POST["emailtype"]);
		$sth->execute();
		?>
		<div class="alert alert-success alert-dismissible" role="alert">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			已修改為 <?=$_POST["name"]?>
		</div>
		<?php
	}
}

require("header.php");
require("func/emailtype.php");
?>
<div class="container">
	<h2>管理電子報類別</h2>
	<form action="" method="post">
		<div class="table-responsive">
			<table class="table">
				<tr>
					<th>編號</th>
					<th>電子報類別</th>
					<th>使用中</th>
				</tr>
				<?php
				foreach ($D['emailtypeall'] as $id => $type) {
					?>
					<tr>
						<td><?=$id?></td>
						<td><?=$type["name"]?></td>
						<td>
							<?=$G["inuse"][$type["inuse"]]?>
							<button type="submit" name="inuse" value="<?=$id?>" class="btn btn-default btn-sm"><i class="fa fa-eye<?=($type["inuse"]?"-slash":"")?>" aria-hidden="true"></i> <?=$G["inuse"][1-$type["inuse"]]?></button>
						</td>
					</tr>
					<?php
				}
				?>
			</table>
		</div>
	</form>
	<h3>新增/修改</h3>
	<form action="" method="post">
		<div class="row">
			<label class="col-sm-2 form-control-label"><i class="fa fa-header itemicon" aria-hidden="true"></i> 電子報類別</label>
			<div class="col-sm-10">
				<select class="form-control" name="emailtype" required>
					<option value="">請選取</option>
					<option value="new">+ 新增電子報類別</option>
					<?php
					foreach ($D['emailtypeall'] as $id => $type) {
						?>
						<option value="<?=$id?>"><?=$type["name"]?></option>
						<?php
					}
					?>
				</select>
			</div>
		</div>
		<div class="row">
			<label class="col-sm-2 form-control-label"><i class="fa fa-header itemicon" aria-hidden="true"></i> 新名稱</label>
			<div class="col-sm-10">
				<input class="form-control" type="text" name="name" value="" required>
			</div>
		</div>
		<div class="row">
			<div class="col-sm-10 offset-sm-2">
				<button type="submit" name="edit" value="" class="btn btn-success"><i class="fa fa-pencil" aria-hidden="true"></i> 新增/修改</button>
			</div>
		</div>
	</form>
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
