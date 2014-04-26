<?php
	require_once(dirname(__FILE__) . '/lib/config.php');
	require_once(dirname(__FILE__) . '/lib/function.php');
	require_once(dirname(__FILE__) . '/lib/steam_api.php');
	
	function Html_Body()
	{
		$pdo      = new PDO(MySQL_PDO_Connect(), MySQL_Base::UserName, MySQL_Base::PassWord);
		$authid   = h($_GET['authid']);
		$error    = null;
		$errormsg = null;

		foreach ($pdo->query('SELECT * FROM ' . MySQL_Base::RankPro) as $row) {
			if (!empty($maplist)) {
				if (!in_array($row[MySQL_Column_Pro::MapName], $maplist)) {
					$maplist[] = $row[MySQL_Column_Pro::MapName];
				}
			} else {
				$maplist[] = $row[MySQL_Column_Pro::MapName];
			}
		}

		foreach ($maplist as $value) {
			$num = 0;

			$sth = $pdo->prepare('SELECT * FROM `' . MySQL_Base::RankPro . '` WHERE ' . MySQL_Column_Pro::MapName . ' = :map ORDER BY ' . MySQL_Column_Pro::Time . ' LIMIT 15');
			$sth->bindValue(':map', $value, PDO::PARAM_STR);
			$sth->execute();
			while ($id = $sth->fetch()) {
				if (valid_steam($id[MySQL_Column_Pro::AuthID])) {
					$num++;
					$point = 0;
					$point = 16-$num;

					if (!isset($player_info[$id[MySQL_Column_Pro::AuthID]]['records'])) {
						$player_info[$id[MySQL_Column_Pro::AuthID]]['records'] = null;
					}

					if (!isset($player[$id[MySQL_Column_Pro::AuthID]])) {
						$player[$id[MySQL_Column_Pro::AuthID]] = null;
					}

					if (!isset($player_info[$id[MySQL_Column_Pro::AuthID]]['prorecords'])) {
						$player_info[$id[MySQL_Column_Pro::AuthID]]['prorecords'] = null;
					}

					if ($num == 1) {
						$player_info[$id[MySQL_Column_Pro::AuthID]]['prorecords']++;
					}

					$player_info[$id[MySQL_Column_Pro::AuthID]]['records']++;
					$player[$id[MySQL_Column_Pro::AuthID]]                 = $player[$id[MySQL_Column_Pro::AuthID]]+$point;
					$player_info[$id[MySQL_Column_Pro::AuthID]]['name']    = $id['name'];
					$player_info[$id[MySQL_Column_Pro::AuthID]]['country'] = $id['country'];
					$player_info[$id[MySQL_Column_Pro::AuthID]]['authid']  = $id['authid'];
				}
			}
		}
		
		arsort($player);

		$num = 0;
		$point_back = null;

		foreach ($player as $sid => $point) {
			$num++;

			if ($point == $point_back) {
				$num--;
			}

			if ($sid == $authid) {
				$position = $num;
			}

			$point_back = $point;
		}

		$allrank = $num;
		
		// STEAM API
		try {
			$Steam = new SteamAPI();
			$Steam->timeout = 3;

			$profile_data = $Steam->get_profile_data($authid);
			$steam_id     = $Steam->calculate_steamid($Steam->calculate_steamid64($authid));
		} catch (Exception $e) {
			$error .= $e->getMessage();
		}

		if (empty($profile_data['steamID'])) {
			$error .= 'No Profile setting';
		}
		
		if (empty($profile_data['steamID64'])) {
			$profile_view = '---';
		} else {
			$profile_view = ' | <a href="http://steamcommunity.com/profiles/' . $profile_data['steamID64'] . '"> ViewProfile </a>';
		}

		if (empty($profile_data['onlineState'])) {
			$steam_status = '---';
		} else {
			if ($profile_data['onlineState'] == 'offline') {
				$steam_status = '<strong id="offline">Offline</strong>';
			} elseif ($profile_data['onlineState'] == 'online') {
				$steam_status = '<strong id="online">Online</strong>';
			} elseif ($profile_data['onlineState'] == 'in-game') {
				$steam_status = '<strong id="ingame">In-Game</strong>';
			}
		}

		if (empty($error)) {
			if ($profile_data['visibilityState'] != 3) {
				$error .= 'Private Profile';
			}
		}

		if (!isset($position)) {
			$position = '---';
		}

		if (!isset($profile_data['vacBanned'])) {
			$VAC_status = '---';
		} else {
			if ($profile_data['vacBanned'] == 0) {
				$VAC_status = '<strong id="vacok">OK</strong>';
			} else {
				$VAC_status = '<strong id="vacban">BANNED</strong>';
			}
		}

		if (empty($profile_data['steamID'])) {
			$steam_name = '---';
		} else {
			$steam_name = $profile_data['steamID'];
		}

		if (empty($profile_data['avatarFull'])) {
			$avatar_url = MainSetting::SteamError;
		} else {
			$avatar_url = $profile_data['avatarFull'];
		}

		if (empty($player_info[$authid]['name'])) {
			$sth = $pdo->prepare('SELECT * FROM `' . MySQL_Base::RankNoob . '` WHERE ' . MySQL_Column_Noob::AuthID . ' = :authid LIMIT 1');
			$sth->bindValue(':authid', $authid, PDO::PARAM_STR);
			$sth->execute();
			while ($row = $sth->fetch()) {
				$player_info[$authid]['country'] = $row[MySQL_Column_Noob::Country];
			}
		}

		if (empty($player_info[$authid]['country'])) {
			$flag = 'err';
		} else {
			$flag = $player_info[$authid]['country'];
		}

		if (empty($player[$authid])) {
			$player[$authid] = '0';
		}

		if (empty($player_info[$authid]['records'])) {
			$player_info[$authid]['records'] = '0';
		}

		if (empty($player_info[$authid]['prorecords'])) {
			$player_info[$authid]['prorecords'] = '0';
		}

		if (!empty($error)) {
			$errormsg = '<tr class="danger">
										<td >Error</td>
										<td >'.$error.'</td>
									</tr>' . "\n";
		}

		$tmp = '					<div class="row">
						<div class="col-xs-4">
							<img src="' . $avatar_url . '">
						</div>
						<div class="col-xs-8">
							<table class="table table-condensed table-hover">
								<thead>
									<tr><th>Player Status</th><th></th></tr>
								</thead>
								<tbody>
									' . $errormsg . '
									<tr>
										<td>Name</td>
										<td>' . $steam_name . '</td>
									</tr>
									<tr>
										<td>AuthID</td>
										<td>' . $authid . '</td>
									</tr>
									<tr>
										<td>Country</td>
										<td><img src="' . MainSetting::FlagImages . strtolower($flag) . '.gif"> ' . country_eng(strtolower($flag)) . '</td>
									</tr>
									<tr>
										<td>Steam Status</td>
										<td>' . $steam_status . $profile_view . '</td>
									  </tr>
									<tr>
										<td>Rank</td>
										<td>'. $position . ' / ' . $allrank . ' (' . $player[$authid] . 'POINTS)</td>
									</tr>
									<tr>
										<td>1st Pro Record</td>
										<td>' . $player_info[$authid]['prorecords'] . '</td>
									</tr>
									<tr>
										<td>Pro Record</td>
										<td>' . $player_info[$authid]['records'] . '</td>
									</tr>
									<tr>
										<td>VAC</td>
										<td>' . $VAC_status . '</td>
									</tr>
								</tbody>
							</table>
						</div>
					</div>
					<table class="table table-condensed table-hover">
						<thead>
							<tr><th>#</th><th>Map</th><th>Time</th><th>Date</th><th>Weapon</th></tr>
						</thead>
						<tbody>' . "\n";
		
		$sth = $pdo->prepare('SELECT * FROM `' . MySQL_Base::RankPro . '` WHERE ' . MySQL_Column_Pro::AuthID . ' = :id ORDER BY ' . MySQL_Column_Pro::MapName);
		$sth->bindValue(':id', $authid, PDO::PARAM_STR);
		$sth->execute();

		foreach ($sth->fetchAll() as $key => $a) {
			$sth = $pdo->prepare('SELECT * FROM `' . MySQL_Base::RankPro . '` WHERE ' . MySQL_Column_Pro::MapName . ' = :map ORDER BY ' . MySQL_Column_Pro::Time . ' LIMIT 15');
			$sth->bindValue(':map', $a[MySQL_Column_Pro::MapName], PDO::PARAM_STR);
			$sth->execute();

			$num = 0;
			while($id = $sth->fetch()) {
				$num++;
				if ($a[MySQL_Column_Pro::AuthID] == $id[MySQL_Column_Pro::AuthID]) {
					if ($num == 1 || $num == 2 || $num == 3) {
						$r_rank = '<img src="' . MainSetting::CupImages . $num . '.gif">';
					} else {
						$r_rank = $num;
					}

					$r_map    = $id[MySQL_Column_Pro::MapName];
					$iMin     = floor(floor($id[MySQL_Column_Pro::Time]) / 60);
					$iSec     = $id[MySQL_Column_Pro::Time] - (60 * $iMin);
					$r_time   = sprintf('%02d:%s%.2f', $iMin, $iSec < 10 ? '0': '', $iSec);
					$r_date   = $id[MySQL_Column_Pro::Date];
					$r_weapon = $id[MySQL_Column_Pro::Weapon];

					$tmp .= '							<tr>
								<td>' . $r_rank . '</td>
								<td><a href="pro15.php?map=' . $r_map . '"> ' . $r_map . ' </a>
								<td>' . $r_time . '</td>
								<td>' . $r_date . '</td>
								<td><img src="' . MainSetting::WeaponImages . $r_weapon . '"></td>
							</tr>' . "\n";
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
		<?php echo Html_BodyHeader(null) ?>
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