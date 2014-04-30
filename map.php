<?php
	require_once(dirname(__FILE__) . '/lib/config.php');
	require_once(dirname(__FILE__) . '/lib/function.php');

	function Html_Body()
	{
		$start = isset($_GET['s']) ? $_GET['s'] : 0;
		$pdo = new PDO(MySQL_PDO_Connect(), MySQL_Base::UserName, MySQL_Base::PassWord);
		
		foreach ($pdo->query('SELECT DISTINCT ' . MySQL_Column_Pro::MapName . ' FROM ' . MySQL_Base::RankPro) as $row) {
			$tab1[] = $row[MySQL_Column_Pro::MapName];
		}

		foreach ($pdo->query('SELECT DISTINCT ' . MySQL_Column_Noob::MapName . ' FROM ' . MySQL_Base::RankNoob) as $row) {
			$tab2[] = $row[MySQL_Column_Noob::MapName];
		}
		
		for ($i = 0; $i < count($tab2); $i++) {
			if (!in_array($tab2[$i], $tab1)) {
				$tab1[] = $tab2[$i];
			}
		}
		
		sort($tab1);

		$tmp = '					<table class="table table-condensed table-hover">
						<thead><tr><th>#</th><th>Maps</th><th>Name</th><th>Time</th><th>Date</th><th>Weapon</th><th>Pro15</th><th>Nub15</th></tr></thead>
						<tbody>' . "\n";

		$i = 0;

		for ($ir = $start; $ir < ($start + MainSetting::MapOnPage); $ir++) {
			if (isset($tab1[$ir])) {
				$i++;

				$sth = $pdo->prepare('SELECT * FROM `' . MySQL_Base::RankPro . '` WHERE ' . MySQL_Column_Pro::MapName . ' = :map ORDER BY ' . MySQL_Column_Pro::Time . ', ' . MySQL_Column_Pro::Name . ' LIMIT 1');
				$sth->bindValue(':map', $tab1[$ir], PDO::PARAM_STR);
				$sth->execute();
				$id = $sth->fetch();
				$idnum = $sth->rowCount();

				if ($idnum > 0)  {
					$iMin      = floor(floor($id[MySQL_Column_Pro::Time]) / 60);
					$iSec      = $id[MySQL_Column_Pro::Time] - (60 * $iMin);
					$r_mapname = $id[MySQL_Column_Pro::MapName];
					$r_flag    = strtolower(empty($id[MySQL_Column_Pro::Country]) ? 'err' : $id[MySQL_Column_Pro::Country]);
					$r_authid  = $id[MySQL_Column_Pro::AuthID];
					$r_name    = h($id[MySQL_Column_Pro::Name]);
					$r_time    = sprintf('%02d:%s%.2f', $iMin, $iSec < 10 ? '0': '', $iSec);
					$r_date    = $id[MySQL_Column_Pro::Date];
					$r_weapon  = $id[MySQL_Column_Pro::Weapon];

					$tmp .= '							<tr>
								<td>' . ($start + $i) . '</td>
								<td>' . $r_mapname . '</td>
								<td><img src="' . MainSetting::FlagImages . $r_flag . '.gif"> <a href="player.php?authid=' . $r_authid . '">' . $r_name . '</a></td>
								<td>' . $r_time . '</td>
								<td>' . $r_date . '</td>
								<td><img src="' . MainSetting::WeaponImages . $r_weapon . '"></td>
								<td><a href="pro15.php?&map=' . $r_mapname . '">-link-</a></td>
								<td><a href="nub15.php?map=' . $r_mapname . '">-link-</a></td>
							</tr>' . "\n";
				} else {
					$sth = $pdo->prepare('SELECT * FROM `' . MySQL_Base::RankNoob . '` WHERE ' . MySQL_Column_Noob::MapName . ' = :map ORDER BY ' . MySQL_Column_Noob::Time . ', ' . MySQL_Column_Noob::Name . ' LIMIT 1');
					$sth->bindValue(':map', $tab1[$ir], PDO::PARAM_STR);
					$sth->execute();
					$id = $sth->fetch();

					$idnum = $sth->rowCount();
					$iMin      = floor(floor($id[MySQL_Column_Noob::Time]) / 60);
					$iSec      = $id[MySQL_Column_Noob::Time] - (60 * $iMin);
					$r_mapname = $id[MySQL_Column_Noob::MapName];
					$r_flag    = strtolower(empty($id[MySQL_Column_Noob::Country]) ? 'err' : $id[MySQL_Column_Noob::Country]);
					$r_authid  = $id[MySQL_Column_Noob::AuthID];
					$r_name    = $id[MySQL_Column_Noob::Name];
					$r_time    = sprintf('%02d:%s%.2f', $iMin, $iSec < 10 ? '0': '', $iSec);
					$r_date    = $id[MySQL_Column_Noob::Date];
					$r_weapon  = $id[MySQL_Column_Noob::Weapon];

					$tmp .= '							<tr>
								<td>' . ($start + $i) . '</td>
								<td>' . $r_mapname . '</td>
								<td><img src="' . MainSetting::FlagImages . $r_flag . '.gif"><a href="player.php?authid=' . $r_authid . '">' . h($r_name) . '</a> [ NOOB RECORD ]</td>
								<td>' . $r_time . '</td>
								<td>' . $r_date . '</td>
								<td><img src="' . MainSetting::WeaponImages . $r_weapon . '"></td>
								<td><a href="pro15.php?&map=' . $r_mapname . '">-link-</a></td>
								<td><a href="nub15.php?map=' . $r_mapname . '">-link-</a></td>
							</tr>' . "\n";
				}
			}
		}

		$tmp .= '						</tbody>
					</table>' . "\n";
		$tmp .= PageIndex (count($tab1), $start);
		$pdo  = null;

		return $tmp;
	}

	function PageIndex($count, $start)
	{
		$tmp = '<div id="pageindex"><div class="btn-group">';
		
		for($i = 0; $i <= floor($count / MainSetting::MapOnPage); $i++) {
			$s = MainSetting::MapOnPage * $i;

			if ($s == $start) {
				$tmp .= '<a href="map.php?s=' . $s . '" class="btn btn-default active">' . ($i + 1) . '</a> ';
			} else {
				$tmp .= '<a href="map.php?s=' . $s . '" class="btn btn-default">' . ($i + 1) . '</a> ';
			}
		}

		$tmp .='</div></div>' . "\n";

		return $tmp;
	}
?>
<!DOCTYPE html>
<html lang="<?php echo MainSetting::Language ?>">
	<head>
		<?php echo Html_Header() ?>
	</head>
	<body>
		<?php echo Html_BodyHeader('map') ?>
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