<?php
	require_once(dirname(__FILE__) . '/lib/config.php');
	require_once(dirname(__FILE__) . '/lib/function.php');

	function Html_Body()
	{
		$pdo  = new PDO(MySQL_PDO_Connect(), MySQL_Base::UserName, MySQL_Base::PassWord);
		$sth  = $pdo->query('SELECT * FROM `' . MySQL_Base::RankPro . '` ORDER BY ' . MySQL_Column_Pro::Date . ' DESC');

		$tmp = '					<table class="table table-condensed table-hover">
						<thead><tr><th>Maps</th><th>#</th><th>Name</th><th>Time</th><th>Date</th><th>Weapon</th><th>Pro15</th><th>Nub15</th></tr></thead>
						<tbody>' . "\n";

		$enter = 0;
		foreach ($sth->fetchAll() as $key => $a) {
			if ($enter > 15) {
				break;
			}

			$sth = $pdo->prepare('SELECT * FROM `' . MySQL_Base::RankPro . '` WHERE ' . MySQL_Column_Pro::MapName . '= :map ORDER BY ' . MySQL_Column_Pro::Time . ' LIMIT 15');
			$sth->bindValue(':map', $a[MySQL_Column_Pro::MapName], PDO::PARAM_STR);
			$sth->execute();
			
			$num = 0;
			while($id = $sth->fetch()) {
				$num++;
				if ($a[MySQL_Column_Pro::AuthID] == $id[MySQL_Column_Pro::AuthID]) {
					if ($num == 1 || $num == 2 || $num == 3) {
						$rank = '<img src="' . MainSetting::CupImages . $num . '.gif">';
					} else {
						$rank = $num;
					}

					$iMin      = floor(floor($a[MySQL_Column_Pro::Time]) / 60);
					$iSec      = $a[MySQL_Column_Pro::Time] - (60 * $iMin);
					$r_mapname = $a[MySQL_Column_Pro::MapName];
					$r_flag    = strtolower(empty($a[MySQL_Column_Pro::Country]) ? 'err' : $a[MySQL_Column_Pro::Country]);
					$r_authid  = $a[MySQL_Column_Pro::AuthID];
					$r_name    = h($a[MySQL_Column_Pro::Name]);
					$r_date    = $a[MySQL_Column_Pro::Date];
					$r_time    = sprintf('%02d:%s%.2f', $iMin, $iSec < 10 ? '0': '', $iSec);
					$r_weapon  = $a[MySQL_Column_Pro::Weapon];

					$tmp .= '							<tr>
								<td>'. $r_mapname .'</td>
								<td>'.$rank.'</td>
								<td><img src="' . MainSetting::FlagImages . $r_flag . '.gif"><a href="player.php?authid=' . $r_authid . '"> ' . $r_name . '</a></td>
								<td>' . $r_time . '</td>
								<td>' . $r_date . '</td>
								<td><img src="' . MainSetting::WeaponImages . $r_weapon . '"></td>
								<td><a href="pro15.php?&map=' . $r_mapname . '">-link-</a></td>
								<td><a href="nub15.php?map=' . $r_mapname . '">-link-</a></td>
							</tr>' . "\n";
					$enter++;
				}
			}
		}
		$tmp .= '						</tbody>
					</table>' . "\n";
		$pdo  = null;

		return $tmp;
	}
?>
<!DOCTYPE html>
<html lang="<?php echo MainSetting::Language ?>">
	<head>
		<?php echo Html_Header() ?>
	</head>
	<body>
		<?php echo Html_BodyHeader('lastpro') ?>
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