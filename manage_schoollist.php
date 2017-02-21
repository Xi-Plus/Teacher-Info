<!DOCTYPE html>
<?php
require('config/config.php');
require("func/school_list.php");
$step = $_POST["step"] ?? "0";
?>
<html lang="zh-Hant-TW">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-alpha.6/css/bootstrap.min.css" integrity="sha384-rwoIResjU2yc3z8GV/NPeZWAv56rSmLldC3R/AZzGRnGxQQKnKkoFVhFQhNUwEyJ" crossorigin="anonymous">
<title><?=$C["titlename"]?>/管理學校列表</title>

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
	if (isset($_FILES["file"])) {
		if ($_FILES["file"]["error"] == 0) {
			?>
			<div class="alert alert-success alert-dismissible" role="alert">
				<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
				檔案上傳成功
			</div>
			<?php
			$file = @file_get_contents($_FILES["file"]["tmp_name"]);
			$finfo = finfo_open(FILEINFO_MIME_TYPE);
			$filemime = finfo_file($finfo, $_FILES["file"]["tmp_name"]);
			finfo_close($finfo);
			if ($file === false) {
				?>
				<div class="alert alert-danger alert-dismissible" role="alert">
					<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					檔案讀取失敗
				</div>
				<?php
			} else if ($filemime !== "text/plain") {
				?>
				<div class="alert alert-danger alert-dismissible" role="alert">
					<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					檔案格式可能不是純文字檔
				</div>
				<?php
			} else {
				if (!in_array($_FILES["file"]["type"], $G["csvmime"])) {
					?>
					<div class="alert alert-warning alert-dismissible" role="alert">
						<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						檔案格式可能不是逗號分隔值(CSV)，你的檔案格式是<?=$_FILES["file"]["type"]?>
					</div>
					<?php
				}
				$step++;
				if (substr($file, 0, 3) == chr(239).chr(187).chr(191)) {
					?>
					<div class="alert alert-info alert-dismissible" role="alert">
						<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
						自動處理BOM
					</div>
					<?php
					$file = substr($file, 3);
				}
				$row = explode("\n", $file);
				$newlist = array();
				foreach ($row as $line => $data) {
					$data = str_getcsv($data);
					if (is_null($data[0])) {
						?>
						<div class="alert alert-warning alert-dismissible" role="alert">
							<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
							第<?=($line+1)?>行為空行，略過
						</div>
						<?php
					} else if (count($data) !== 3) {
						?>
						<div class="alert alert-danger alert-dismissible" role="alert">
							<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
							第<?=($line+1)?>行欄位數不為3，略過
						</div>
						<?php
					} else if (!preg_match("/^[A-Za-z0-9]{1,6}$/", $data[0])) {
						?>
						<div class="alert alert-danger alert-dismissible" role="alert">
							<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
							第<?=($line+1)?>行第1欄不合法，不是1~6個英數字，略過
						</div>
						<?php
					}  else if ( $data[2]!== "" && $data[2]!=="0" && $data[2]!=="1") {
						?>
						<div class="alert alert-danger alert-dismissible" role="alert">
							<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
							第<?=($line+1)?>行第3欄不合法，不是空字串或0或1，略過
						</div>
						<?php
					} else {
						$newlist[strtoupper($data[0])] = array("name"=>$data[1], "inuse"=>($data[2]=="0"?"0":"1"));
					}
				}
			}
		} else {
		?>
		<div class="alert alert-danger alert-dismissible" role="alert">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			檔案上傳失敗，錯誤代碼：<?=$_FILES["file"]["error"]?>
		</div>
		<?php
		}
	} else {
		?>
		<div class="alert alert-danger alert-dismissible" role="alert">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			發生未知錯誤
		</div>
		<?php
	}
} else if ($step == 2) {
	if ($_POST["override"]) {
		$G["db"]->exec("DELETE FROM `school_list`");
		?>
		<div class="alert alert-info alert-dismissible" role="alert">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			已清空學校列表
		</div>
		<?php
	}
	foreach ($_POST["type"] as $schoolid => $type) {
		if ($type == "new") {
			$sth = $G["db"]->prepare("INSERT INTO `school_list` (`id`, `name`, `inuse`) VALUES (:id, :name, :inuse)");
			$sth->bindValue(":id", $schoolid);
			$sth->bindValue(":name", $_POST["name"][$schoolid]);
			$sth->bindValue(":inuse", $_POST["inuse"][$schoolid]);
			$sth->execute();
		} else if ($type == "edit") {
			$sth = $G["db"]->prepare("UPDATE `school_list` SET `name` = :name, `inuse` = :inuse WHERE `id` = :id");
			$sth->bindValue(":id", $schoolid);
			$sth->bindValue(":name", $_POST["name"][$schoolid]);
			$sth->bindValue(":inuse", $_POST["inuse"][$schoolid]);
			$sth->execute();
		} else {
		?>
		<div class="alert alert-info alert-dismissible" role="alert">
			<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			發生未知錯誤 (<?=$schoolid?>)
		</div>
		<?php
		}
	}
	?>
	<div class="alert alert-success alert-dismissible" role="alert">
		<button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		已完成
	</div>
	<?php
	$step ++;
}
?>
<div class="container">
	<h2>管理學校列表-步驟<?=$step?></h2>
	<?php
	if ($step == 1) {
		?>
		<form action="" method="post" enctype="multipart/form-data" class="form-inline">
			<input type="hidden" name="step" value="<?=$step?>">
			<label>選擇檔案：<input type="file" name="file" accept=".csv" class="form-control" required></label> 
			<label><input type="checkbox" name="override">清除所有資料後再匯入</label> 
			<button type="submit" class="btn btn-success form-control"><i class="fa fa-upload" aria-hidden="true"></i> 上傳</button>
		</form>
		<?php
	} else if ($step == 2) {
		?>
		模式：<?=(isset($_POST["override"])?"覆寫，清除所有資料後再匯入":"變更，新增學校或修改名稱")?>
		<form action="" method="post">
			<input type="hidden" name="step" value="<?=$step?>">
			<input type="hidden" name="override" value="<?=isset($_POST["override"])?>">
			<div class="table-responsive">
				<table class="table">
					<tr>
						<th>模式</th>
						<th>學校編號</th>
						<th>學校名稱</th>
						<th>使用中</th>
					</tr>
					<?php
					if (isset($_POST["override"])) {
						unset($D["school_list"]);
					}
					$nochange = true;
					foreach ($newlist as $schoolid => $school) {
						if (isset($D["school_list"][$schoolid])) {
							if ($D["school_list"][$schoolid]["name"] == $school["name"] && $D["school_list"][$schoolid]["inuse"] == $school["inuse"]) {
								continue;
							}
							?>
							<tr>
								<input type="hidden" name="type[<?=$schoolid?>]" value="edit">
								<input type="hidden" name="name[<?=$schoolid?>]" value="<?=$school["name"]?>">
								<input type="hidden" name="inuse[<?=$schoolid?>]" value="<?=$school["inuse"]?>">
								<td>變更</td>
								<td><?=$schoolid?></td>
								<td><?php
								if ($D["school_list"][$schoolid]["name"] != $school["name"]) {
									echo "<s>".$D["school_list"][$schoolid]["name"]."</s>→".$school["name"];
								}
								?></td>
								<td><?php
								if ($D["school_list"][$schoolid]["inuse"] != $school["inuse"]) {
									echo "<s>".$G["inuse"][$D["school_list"][$schoolid]["inuse"]]."</s>→".$G["inuse"][$school["inuse"]];
								}
								?></td>
							</tr>
							<?php
							$nochange = false;
						} else {
							?>
							<tr>
								<input type="hidden" name="type[<?=$schoolid?>]" value="new">
								<input type="hidden" name="name[<?=$schoolid?>]" value="<?=$school["name"]?>">
								<input type="hidden" name="inuse[<?=$schoolid?>]" value="<?=$school["inuse"]?>">
								<td>新增</td>
								<td><?=$schoolid?></td>
								<td><?=$school["name"]?></td>
								<td><?=$G["inuse"][$school["inuse"]]?></td>
							</tr>
							<?php
							$nochange = false;
						}
					}
					?>
				</table>
			</div>
			<?php
			if ($nochange) {
				echo "沒有任何變更";
			} else {
				?>
				<button type="submit" class="btn btn-success form-inline"><i class="fa fa-upload" aria-hidden="true"></i> 確認</button> 如果資料有錯誤，請回到上一步驟，修改檔案後重新上傳
				<?php
			}
			?>
		</form>
		<?php
	} else if ($step == 3) {
		?>
		已完成
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
</body>
</html>
