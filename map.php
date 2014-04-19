<?php
	require_once(dirname(__FILE__) . '/lib/config.php');
	require_once(dirname(__FILE__) . '/lib/function.php');

	function Html_Body() {
		$start = isset($_GET['s']) ? $_GET['s'] : 0;
		$pdo = new PDO(MySQL_PDO_Connect(), MySQL_Base::UserName, MySQL_Base::PassWord);
		
		foreach ($pdo->query('SELECT DISTINCT ' . MySQL_Column_Pro::MapName . ' FROM ' . MySQL_Base::RankPro) as $row) {
			$tab1[]	= $row[MySQL_Column_Pro::MapName];
		}

		foreach ($pdo->query('SELECT DISTINCT ' . MySQL_Column_Noob::MapName . ' FROM ' . MySQL_Base::RankNoob) as $row) {
			$tab2[]	= $row[MySQL_Column_Noob::MapName];
		}
		
		for ($i = 0; $i < count($tab2); $i++) {
			if (!in_array($tab2[$i], $tab1)) {
				$tab1[] = $tab2[$i];
			}
		}
		
		sort($tab1);

		$map_count = count($tab1);
		$tmp       = menu('map', $map_count);

		$tmp .= '					<table class="table table-condensed table-hover">
						<thead><tr><th>#</th><th>Maps</th><th>Name</th><th>Time</th><th>Date</th><th>Weapon</th><th>Pro15</th><th>Nub15</th></tr></thead>
						<tbody>' . "\n";

		$i = 0;

		for ($ir = $start; $ir < ($start + MainSetting::MapOnPage); $ir++) {
			if (isset($tab1[$ir])) {
				$i++;

				$sth = $pdo->prepare('SELECT * FROM `'.MySQL_Base::RankPro.'` WHERE mapname = :map ORDER BY time, name LIMIT 1');
				$sth->bindValue(':map', $tab1[$ir], PDO::PARAM_STR);
				$sth->execute();
				$id = $sth->fetch();

				if (count($id) > 0) {
					$iMin      = floor(floor($id['time']) / 60);
					$iSec      = $id['time'] - (60 * $iMin);
					$r_date    = $id['date'];
					$r_mapname = $id['mapname'];
					$r_authid  = $id['authid'];
					$r_weapon  = $id['weapon'];
					$r_name    = $id['name'];
					$r_flag    = strtolower(empty($id['country']) ? 'err' : $id['country']);
					$r_time    = sprintf('%02d:%s%.2f', $iMin, $iSec < 10 ? '0': '', $iSec);

					$tmp .= '							<tr>
								<td>' . ($start + $i) . '</td>
								<td><a href="pronub15.php?&map=' . $r_mapname . '">' . $r_mapname . '</a></td>
								<td><img src="./img/flags/' . $r_flag . '.gif"> <a href="player.php?authid=' . $r_authid . '">' . h($r_name) . '</a></td>
								<td>' . $r_time . '</td>
								<td>' . $r_date . '</td>
								<td><img src="./img/weapons/' . $r_weapon . '.gif"></td>
								<td><a href="pro15.php?&map=' . $r_mapname . '">-link-</a></td>
								<td><a href="nub15.php?map=' . $r_mapname . '">-link-</a></td>
							</tr>' . "\n";
				} else {
					$sth = $pdo->prepare('SELECT * FROM `'.MySQL_Base::RankNoob.'` WHERE mapname = :map ORDER BY time, name LIMIT 1');
					$sth->bindValue(':map', $tab1[$ir], PDO::PARAM_STR);
					$sth->execute();
					$id = $sth->fetch();

					$iMin      = floor(floor($id['time']) / 60);
					$iSec      = $id['time'] - (60 * $iMin);
					$r_date    = $id['date'];
					$r_mapname = $id['mapname'];
					$r_authid  = $id['authid'];
					$r_weapon  = $id['weapon'];
					$r_name    = $id['name'];
					$r_flag    = strtolower(empty($id['country']) ? 'err' : $id['country']);
					$r_time    = sprintf('%02d:%s%.2f', $iMin, $iSec < 10 ? '0': '', $iSec);

					$tmp .= '							<tr>
								<td>' . ($start + $i) . '</td>
								<td><a href="pronub15.php?&map=' . $r_mapname . '">' . $r_mapname . '</a></td>
								<td><img src="./img/flags/' . $r_flag . '.gif"><a href="player.php?authid=' . $r_authid . '">' . h($r_name) . '</a> [ NOOB RECORD ]</td>
								<td>' . $r_time . '</td>
								<td>' . $r_date . '</td>
								<td><img src="./img/weapons/' . $r_weapon . '.gif"></td>
								<td><a href="pro15.php?&map=' . $r_mapname . '">-link-</a></td>
								<td><a href="nub15.php?map=' . $r_mapname . '">-link-</a></td>
							</tr>' . "\n";
				}
			}
		}

		$tmp .= '						</tbody>
					</table>' . "\n";
		$tmp .= PageIndex ($map_count, $start);
		$pdo  = null;

		return $tmp;
	}

	function PageIndex($map_count, $start) {
		$tmp = '<div class="btn-group">';
		
		for($i = 0; $i <= floor($map_count / MainSetting::MapOnPage); $i++) {
			$s = MainSetting::MapOnPage * $i;

			if ($s == $start) {
				$tmp .= '<a href="map.php?s=' . $s . '" class="btn btn-default active">' . ($i + 1) . '</a> ';
			} else {
				$tmp .= '<a href="map.php?s=' . $s . '" class="btn btn-default">' . ($i + 1) . '</a> ';
			}
		}

		$tmp .='</div>' . "\n";

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