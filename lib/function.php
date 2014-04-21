<?php
	require_once(dirname(__FILE__) . '/config.php');
	
	function MySQL_PDO_Connect()
	{
		return 'mysql:host='.MySQL_Base::HostName.'; dbname='.MySQL_Base::DataBase.'; charset='.MySQL_Base::Charset.';';
	}

	function h($str, $encoding='UTF-8')
	{
		return htmlspecialchars($str, ENT_QUOTES, $encoding);
	}

	function valid_steam($authid)
	{
		if ($authid == '4294967295'
		|| $authid == 'STEAM_666:88:666'
		|| $authid == 'STEAM_154:88:666'
		|| $authid == 'unknown'
		|| $authid == 'HLTV'
		|| $authid == 'STEAM_ID_LAN'
		|| $authid == 'VALVE_ID_LAN'
		|| $authid == 'VALVE_ID_PENDING'
		|| $authid == 'STEAM_ID_PENDING'
		|| $authid == '') return 0;
		else return 1;
	}

	function country_eng($short)
	{
		$short = strtoupper($short);
		$table = Array(
		'ERR' => 'Unknown',
		'AE' => 'United Arab Emirates',
		'AF' => 'Afghanistan',
		'AL' => 'Albania',
		'DZ' => 'Algeria',
		'AD' => 'Andorra',
		'AO' => 'Angola',
		'AI' => 'Anguilla',
		'AQ' => 'Antarctica',
		'AG' => 'Antigua and Barbuda',
		'SA' => 'Saudi Arabia',
		'AR' => 'Argentina',
		'AM' => 'Armenia',
		'AW' => 'Aruba',
		'AU' => 'Australia',
		'AT' => 'Austria',
		'AZ' => 'Azerbaijan',
		'BS' => 'Bahamas',
		'BH' => 'Bahrain',
		'BD' => 'Bangladesh',
		'BB' => 'Barbados',
		'BE' => 'Belgium',
		'BZ' => 'Belize',
		'BJ' => 'Benin',
		'BM' => 'Bermuda',
		'BT' => 'Bhutan',
		'BY' => 'Belarus',
		'MM' => 'Myanmar',
		'BO' => 'Bolivia, Plurinational State of',
		'BQ' => 'Bonaire, Saint Eustatius and Saba',
		'BA' => 'Bosnia and Herzegovina',
		'BW' => 'Botswana',
		'BR' => 'Brazil',
		'BN' => 'Brunei Darussalam',
		'IO' => 'British Indian Ocean Territory',
		'VG' => 'Virgin Islands, British',
		'BG' => 'Bulgaria',
		'BF' => 'Burkina Faso',
		'BI' => 'Burundi',
		'CL' => 'Chile',
		'CN' => 'China',
		'HR' => 'Croatia',
		'CW' => 'Curaçao',
		'CY' => 'Cyprus',
		'TD' => 'Chad',
		'ME' => 'Montenegro',
		'CZ' => 'Czech Republic',
		'UM' => 'United States Minor Outlying Islands',
		'DK' => 'Denmark',
		'CD' => 'Congo, the Democratic Republic of the',
		'DM' => 'Dominica',
		'DO' => 'Dominican Republic',
		'DJ' => 'Djibouti',
		'EG' => 'Egypt',
		'EC' => 'Ecuador',
		'ER' => 'Eritrea',
		'EE' => 'Estonia',
		'ET' => 'Ethiopia',
		'FK' => 'Falkland Islands (Malvinas)',
		'FJ' => 'Fiji',
		'PH' => 'Philippines',
		'FI' => 'Finland',
		'FR' => 'France',
		'TF' => 'French Southern Territories',
		'GA' => 'Gabon',
		'GM' => 'Gambia',
		'GS' => 'South Georgia and the South Sandwich Islands',
		'GH' => 'Ghana',
		'GI' => 'Gibraltar',
		'GR' => 'Greece',
		'GD' => 'Grenada',
		'GL' => 'Greenland',
		'GE' => 'Georgia',
		'GU' => 'Guam',
		'GG' => 'Guernsey',
		'GF' => 'French Guiana',
		'GY' => 'Guyana',
		'GP' => 'Guadeloupe',
		'GT' => 'Guatemala',
		'GW' => 'Guinea-Bissau',
		'GQ' => 'Equatorial Guinea',
		'GN' => 'Guinea',
		'HT' => 'Haiti',
		'ES' => 'Spain',
		'NL' => 'Netherlands',
		'HN' => 'Honduras',
		'HK' => 'Hong Kong',
		'IN' => 'India',
		'ID' => 'Indonesia',
		'IQ' => 'Iraq',
		'IR' => 'Iran, Islamic Republic of',
		'IE' => 'Ireland',
		'IS' => 'Iceland',
		'IL' => 'Israel',
		'JM' => 'Jamaica',
		'JP' => 'Japan',
		'YE' => 'Yemen',
		'JE' => 'Jersey',
		'JO' => 'Jordan',
		'KY' => 'Cayman Islands',
		'KH' => 'Cambodia',
		'CM' => 'Cameroon',
		'CA' => 'Canada',
		'QA' => 'Qatar',
		'KZ' => 'Kazakhstan',
		'KE' => 'Kenya',
		'KG' => 'Kyrgyzstan',
		'KI' => 'Kiribati',
		'CO' => 'Colombia',
		'KM' => 'Comoros',
		'CG' => 'Congo',
		'KR' => 'Korea, Republic of',
		'KP' => 'Korea, Democratic People\'s Republic of',
		'CR' => 'Costa Rica',
		'CU' => 'Cuba',
		'KW' => 'Kuwait',
		'LA' => 'Lao People\'s Democratic Republic',
		'LS' => 'Lesotho',
		'LB' => 'Lebanon',
		'LR' => 'Liberia',
		'LY' => 'Libyan Arab Jamahiriya',
		'LI' => 'Liechtenstein',
		'LT' => 'Lithuania',
		'LU' => 'Luxembourg',
		'LV' => 'Latvia',
		'MK' => 'Macedonia, the former Yugoslav Republic of',
		'MG' => 'Madagascar',
		'YT' => 'Mayotte',
		'MO' => 'Macao',
		'MW' => 'Malawi',
		'MV' => 'Maldives',
		'MY' => 'Malaysia',
		'ML' => 'Mali',
		'MT' => 'Malta',
		'MP' => 'Northern Mariana Islands',
		'MA' => 'Morocco',
		'MQ' => 'Martinique',
		'MR' => 'Mauritania',
		'MU' => 'Mauritius',
		'MX' => 'Mexico',
		'FM' => 'Micronesia, Federated States of',
		'MD' => 'Moldova, Republic of',
		'MC' => 'Monaco',
		'MN' => 'Mongolia',
		'MS' => 'Montserrat',
		'MZ' => 'Mozambique',
		'NA' => 'Namibia',
		'NR' => 'Nauru',
		'NP' => 'Nepal',
		'DE' => 'Germany',
		'NE' => 'Niger',
		'NG' => 'Nigeria',
		'NI' => 'Nicaragua',
		'NU' => 'Niue',
		'NF' => 'Norfolk Island',
		'NO' => 'Norway',
		'NC' => 'New Caledonia',
		'NZ' => 'New Zealand',
		'OM' => 'Oman',
		'PK' => 'Pakistan',
		'PW' => 'Palau',
		'PS' => 'Palestinian Territory, Occupied',
		'PA' => 'Panama',
		'PG' => 'Papua New Guinea',
		'PY' => 'Paraguay',
		'PE' => 'Peru',
		'PN' => 'Pitcairn',
		'PF' => 'French Polynesia',
		'PL' => 'Poland',
		'PR' => 'Puerto Rico',
		'PT' => 'Portugal',
		'TW' => 'Taiwan, Province of China',
		'ZA' => 'South Africa',
		'CF' => 'Central African Republic',
		'CV' => 'Cape Verde',
		'RE' => 'Réunion',
		'RU' => 'Russian Federation',
		'RO' => 'Romania',
		'RW' => 'Rwanda',
		'EH' => 'Western Sahara',
		'KN' => 'Saint Kitts and Nevis',
		'LC' => 'Saint Lucia',
		'VC' => 'Saint Vincent and the Grenadines',
		'BL' => 'Saint Barthélemy',
		'MF' => 'Saint Martin (French part)',
		'PM' => 'Saint Pierre and Miquelon',
		'SV' => 'El Salvador',
		'AS' => 'American Samoa',
		'WS' => 'Samoa',
		'SM' => 'San Marino',
		'SN' => 'Senegal',
		'RS' => 'Serbia',
		'SC' => 'Seychelles',
		'SL' => 'Sierra Leone',
		'SG' => 'Singapore',
		'SX' => 'Sint Maarten (Dutch part)',
		'SK' => 'Slovakia',
		'SI' => 'Slovenia',
		'SO' => 'Somalia',
		'LK' => 'Sri Lanka',
		'US' => 'United States',
		'SZ' => 'Swaziland',
		'SD' => 'Sudan',
		'SR' => 'Suriname',
		'SJ' => 'Svalbard and Jan Mayen',
		'SY' => 'Syrian Arab Republic',
		'CH' => 'Switzerland',
		'SE' => 'Sweden',
		'TJ' => 'Tajikistan',
		'TH' => 'Thailand',
		'TZ' => 'Tanzania, United Republic of',
		'TL' => 'Timor-Leste',
		'TG' => 'Togo',
		'TK' => 'Tokelau',
		'TO' => 'Tonga',
		'TT' => 'Trinidad and Tobago',
		'TN' => 'Tunisia',
		'TR' => 'Turkey',
		'TM' => 'Turkmenistan',
		'TC' => 'Turks and Caicos Islands',
		'TV' => 'Tuvalu',
		'UG' => 'Uganda',
		'UA' => 'Ukraine',
		'UY' => 'Uruguay',
		'UZ' => 'Uzbekistan',
		'VU' => 'Vanuatu',
		'WF' => 'Wallis and Futuna',
		'VA' => 'Holy See (Vatican City State)',
		'VE' => 'Venezuela, Bolivarian Republic of',
		'HU' => 'Hungary',
		'GB' => 'United Kingdom',
		'VN' => 'Viet Nam',
		'IT' => 'Italy',
		'CI' => 'Côte d\'Ivoire',
		'BV' => 'Bouvet Island',
		'CX' => 'Christmas Island',
		'IM' => 'Isle of Man',
		'SH' => 'Saint Helena, Ascension and Tristan da Cunha',
		'AX' => 'Aland Islands',
		'CK' => 'Cook Islands',
		'VI' => 'Virgin Islands, U.S.',
		'HM' => 'Heard Island and McDonald Islands',
		'CC' => 'Cocos (Keeling) Islands',
		'MH' => 'Marshall Islands',
		'FO' => 'Faroe Islands',
		'SB' => 'Solomon Islands',
		'ST' => 'Sao Tome and Principe',
		'ZM' => 'Zambia',
		'ZW' => 'Zimbabwe');
		
		return strtr($short,$table);
	}

	function Html_Header ()
	{
		return '<meta charset="UTF-8">
		<meta name="robots" content="noindex,nofollow">
		<link rel="stylesheet" href="' . MainSetting::BootStrapCSS . '">
		<link rel="stylesheet" href="' . MainSetting::CSS . '">
		<title>' . MainSetting::PageTitle . '</title>' . "\n";
	}

	function Html_Footer()
	{
		return '<script src="' . MainSetting::jQuery . '"></script>
		<script src="' . MainSetting::BootStrapJS . '"></script>' . "\n";
	}

	function Html_BodyHeader()
	{
		return '<header class="navbar navbar-inverse navbar-fixed-top" role="banner">
			<div class="container">
				<div class="navbar-header">
					<a class="navbar-brand navbar-brand_fix mfont" href="./">ProKreedz Plus</a>
				</div>
			</div>
		</header>' . "\n";
	}

	function Html_BodyFooter()
	{
		return '<div id="footer">ProKreedz Plus by <a href="//hoshinoa.me">k725</a> / Original Author by <a href="mailto:stiscorpion@gmail.com">+ScorpioN+</a> / AMXX Plugin by <a href="//forums.alliedmods.net/showthread.php?t=130417">nucLeaR</a></div>' . "\n";
	}

	function menu($page, $value)
	{
		$tmp = '';

		if ($page == 'pro15' || $page == 'nub15') {
			$tmp .= $_GET['map'];
		}

		$flag_1 = $page == 'home'    ? ' active' : '';
		$flag_2 = $page == 'players' ? ' active' : '';
		$flag_3 = $page == 'map'     ? ' active' : '';
		$flag_4 = $page == 'lastpro' ? ' active' : '';
		
		$tmp .='<div style="text-align: right;">
						<div class="btn-group">
							<a href="./" class="btn btn-default' . $flag_1 . '">Home</a>
							<a href="players.php" class="btn btn-default' . $flag_2 . '">Player statistic</a>
							<a href="map.php" class="btn btn-default' . $flag_3 . '">Map statistic</a>
							<a href="lastpro.php" class="btn btn-default' . $flag_4 . '">LastPro</a>
						</div>
					' . "\n";
		
		if ($page == 'nub15' || $page == 'pro15') {
			$flag_5 = $page == 'pro15' ? ' active' : '';
			$flag_6 = $page == 'nub15' ? ' active' : '';

			$tmp .= '						<div class="btn-group">
							<a href="pro15.php?&map=' . $_GET['map'] . '" class="btn btn-default' . $flag_5 . '">pro15</a>
							<a href="nub15.php?&map=' . $_GET['map'] . '" class="btn btn-default' . $flag_6 . '">nub15</a>
						</div>' . "\n";
		}

		$tmp .= '					</div>' . "\n";

		return $tmp;
	}
