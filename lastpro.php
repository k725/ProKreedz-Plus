<?php
	require_once(dirname(__FILE__).'/lib/config.php');
	require_once(dirname(__FILE__).'/lib/function.php');

	function Html_Body()
	{
		$pdo  = new PDO(MySQL_PDO_Connect(), MySQL_Base::UserName, MySQL_Base::PassWord);
		$sth  = $pdo->query('SELECT * FROM kz_pro15 ORDER BY date desc');

		$tmp  = menu('lastpro', null);
		$tmp .= '					<table class="table table-condensed table-hover">
						<thead><tr><th>Maps</th><th>#</th><th>Name</th><th>Time</th><th>Date</th><th>Weapon</th><th>Pro15</th><th>Nub15</th></tr></thead>
						<tbody>' . "\n";

		$enter = 0;
		foreach ($sth->fetchAll() as $key => $a) {
			if ($enter > 15) {
				break;
			}

			$sth = $pdo->prepare('SELECT * FROM kz_pro15 WHERE mapname= :map ORDER BY time LIMIT 15');
			$sth->bindValue(':map', $a['mapname'], PDO::PARAM_STR);
			$sth->execute();
			
			$num = 0;
			while($id = $sth->fetch()) {
				$num++;
				if ($a['authid'] == $id['authid']) {
					if ($num == 1 || $num == 2 || $num == 3) {
						$rank = '<img src="./img/cups/'.$num.'.gif">';
					} else {
						$rank = $num;
					}

					$iMin      = floor(floor($a['time']) / 60);
					$iSec      = $a['time'] - (60 * $iMin);
					$r_mapname = $a['mapname'];
					$r_flag    = strtolower(empty($a['country']) ? 'err' : $a['country']);
					$r_authid  = $a['authid'];
					$r_name    = h($a['name']);
					$r_date    = $a['date'];
					$r_time    = sprintf('%02d:%s%.2f', $iMin, $iSec < 10 ? '0': '', $iSec);
					$r_weapon  = $a['weapon'];

					$tmp .= '							<tr>
								<td><a href="pro15.php?&map='. $r_mapname .'">'. $r_mapname .'</a></td>
								<td>'.$rank.'</td>
								<td><img src="./img/flags/' . $r_flag . '.gif"><a href="player.php?authid=' . $r_authid . '"> ' . $r_name . '</a></td>
								<td>' . $r_time . '</td>
								<td>' . $r_date . '</td>
								<td><img src="./img/weapons/' . $r_weapon . '.gif"></td>
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