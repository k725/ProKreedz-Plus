<?php
	require_once(dirname(__FILE__).'/lib/config.php');
	require_once(dirname(__FILE__).'/lib/function.php');

	function Html_Body()
	{
		$pdo = new PDO(MySQL_PDO_Connect(), MySQL_Base::UserName, MySQL_Base::PassWord);
		$tmp = menu('players', null);

		foreach ($pdo->query('SELECT DISTINCT ' . MySQL_Column_Pro::MapName . ' FROM ' . MySQL_Base::RankPro) as $row) {
			$maplist[] = $row[MySQL_Column_Pro::MapName];
		}

		for($i = 0; $i < count($maplist); $i++) {
			$sth = $pdo->prepare('SELECT * FROM kz_pro15 WHERE mapname = :map ORDER BY time LIMIT 15');
			$sth->bindValue(':map', $maplist[$i], PDO::PARAM_STR);
			$sth->execute();

			$num = 0;
			while($id = $sth->fetch())
			{
				if (valid_steam($id['authid'])) {
					$num++;

					if (empty($player[$id['authid']])) {
						$player[$id['authid']] = null;
					}

					if (empty($player_info[$id['authid']]['records'])) {
						$player_info[$id['authid']]['records'] = null;
					}

					if (empty($player_info[$id['authid']]['prorecords'])) {
						$player_info[$id['authid']]['prorecords'] = null;
					}

					$player[$id['authid']]                 = $player[$id['authid']] + (16 - $num);
					$player_info[$id['authid']]['name']    = $id['name'];
					$player_info[$id['authid']]['country'] = $id['country'];
					$player_info[$id['authid']]['name']    = $id['name'];
					$player_info[$id['authid']]['authid']  = $id['authid'];
					$player_info[$id['authid']]['records']++;

					if ($num == 1 ) {
						$player_info[$id['authid']]['prorecords']++;
					}
				}
			}
		}

		arsort($player);

		$tmp .= '					<table class="table table-condensed table-hover">
						<thead><tr><th>#</th><th>Name</th><th>#1st</th><th>Record</th><th>Point</th><th>Country</th></tr></thead>
						<tbody>' . "\n";

		$ranks = 0;
		$num = 0;
		$point_back = null;
		foreach ($player as $sid => $point) {
			$ranks++;
			$num++;
			
			if ($point == $point_back) {
				$rank = '';
				$num--;
			} else {
				if ($num == 1 || $num == 2 || $num == 3) {
					$rank = '<img src="./img/cups/' . $num . '.gif">';
				} else {
					$rank = $num.':';
				}
			}

			$fshow = strtolower(empty($player_info[$sid]['country']) ? 'err' : $player_info[$sid]['country']);

			if (empty($player_info[$sid]['prorecords'])) {
				$player_info[$sid]['prorecords'] = 0;
			}

			$tmp .= '							<tr>
								<td>' . $rank . '</td>
								<td><a href="player.php?authid=' . $player_info[$sid]['authid'] . '">' . $player_info[$sid]['name'] . '</a></td>
								<td>' . $player_info[$sid]['prorecords'] . '</td>
								<td>' . $player_info[$sid]['records'] . '</td>
								<td>' . $player[$sid] . '</td>
								<td><img src="./img/flags/' . $fshow . '.gif"> ' . country_eng($fshow) . '</td>
							</tr>' . "\n";

			$point_back = $point;
		}

		$tmp .= '						</tbody>
					</table>' . "\n";
		$pdo = null;

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