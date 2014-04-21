<?php
	require_once(dirname(__FILE__) . '/lib/config.php');
	require_once(dirname(__FILE__) . '/lib/function.php');
?>
<!DOCTYPE html>
<html lang="<?php echo MainSetting::Language ?>">
	<head>
		<?php echo Html_Header() ?>
	</head>
	<body>
		<?php echo Html_BodyHeader() ?>
		<div class="container">
			<div class="row">
				<div class="col-xs-12">
					<?php echo menu('home', null) ?>
					<h1>ProKreedz Plus</h1>

					<div class="page-header">
						<h2>Welcome</h2>
					</div>
					<p>TEST</p>
					
					<div class="page-header">
						<h2>Server Infomation</h2>
					</div>
					<table class="table table-hover">
						<thead>
							<tr><th>#</th><th>ServerName</th><th>Map</th><th>Player</th></tr>
						</thead>
						<tbody>
							<tr><td>1</td><td>テーブルセル</td><td>de_dust2</td><td>00/00</td></tr>
							<tr><td>2</td><td>テーブルセル</td><td>de_dust2</td><td>00/00</td></tr>
							<tr><td>3</td><td>テーブルセル</td><td>de_dust2</td><td>00/00</td></tr>
							<tr><td>4</td><td>テーブルセル</td><td>de_dust2</td><td>00/00</td></tr>
							<tr><td>5</td><td>テーブルセル</td><td>de_dust2</td><td>00/00</td></tr>
							<tr><td>6</td><td>テーブルセル</td><td>de_dust2</td><td>00/00</td></tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>

		<?php echo Html_BodyFooter() ?>
		<?php echo Html_Footer() ?>
	</body>
</html>