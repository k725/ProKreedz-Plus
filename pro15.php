<?php
	require_once(dirname(__FILE__) . '/lib/config.php');
	require_once(dirname(__FILE__) . '/lib/function.php');

	function Html_Body()
	{
		$pdo = new PDO(MySQL_PDO_Connect(), MySQL_Base::UserName, MySQL_Base::PassWord);
		$tmp = menu('pro15', null);

		if(!empty($_GET['map'])) {
			$tmp .= '					<table class="table table-condensed table-hover">
						<thead><tr><th>#</th><th>Name</th><th>AuthID</th><th>Time</th><th>Date</th><th>Weapon</th></tr></thead>
						<tbody>' . "\n";
			
			$sth = $pdo->prepare('SELECT * FROM `' . MySQL_Base::RankPro . '` WHERE ' . MySQL_Column_Pro::MapName . ' = :map ORDER BY ' . MySQL_Column_Pro::Time . ', ' . MySQL_Column_Pro::Name . ' LIMIT 15');
			$sth->bindValue(':map', h($_GET['map']), PDO::PARAM_STR);
			$sth->execute();

			$i = 0;
			foreach ($sth->fetchAll() as $key => $a) {
				$i++;

				if ($i == 1 OR $i == 2 OR $i == 3) {
					$r_rank = '<img src="./img/cups/' . $i . '.gif">';
				} else {
					$r_rank = $i;
				}

				$r_authid = $a[MySQL_Column_Pro::AuthID];
				$iMin     = floor(floor($a[MySQL_Column_Pro::Time])/60);
				$iSec     = $a[MySQL_Column_Pro::Time] - (60 * $iMin);
				$r_time   = sprintf('%02d:%s%.2f', $iMin, $iSec < 10 ? '0': '', $iSec);
				$r_name   = h($a[MySQL_Column_Pro::Name]);
				$r_flag   = strtolower(empty($a[MySQL_Column_Pro::Country]) ? 'err' : $a[MySQL_Column_Pro::Country]);
				$r_weapon = $a[MySQL_Column_Pro::Weapon];
				$r_date   = $a[MySQL_Column_Pro::Date];

				$tmp .= '							<tr>
								<td>' . $r_rank . '</td>
								<td><img src="./img/flags/' . $r_flag . '.gif"> <a href="player.php?authid=' . $r_authid . '">' . $r_name . '</a></td>
								<td>' . $r_authid . '</td>
								<td>' . $r_time . '</td>
								<td>' . $r_date . '</td>
								<td><img src="./img/weapons/' . $r_weapon . '.gif"></td>
							</tr>' . "\n";
			}
			
			
			$pdo = null;
			$tmp .= '						</tbody>
					</table>' . "\n";

			return $tmp;
		}
	}
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
					<?php echo Html_Body() ?>
				</div>
			</div>
		</div>
		<?php echo Html_BodyFooter() ?>
		<?php echo Html_Footer() ?>
	</body>
</html>