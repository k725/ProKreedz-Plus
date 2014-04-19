<?php
	class MainSetting {
		const Language      = 'ja';
		const MainPage      = 'http://example.com/';
		const PageTitle     = 'ProKreedz Plus @ Japan';
		const MapOnPage     = 15;
		/* ----------------------------------------- */
		const BootStrapCSS  = '//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.1.1/css/bootstrap.min.css';
		const BootStrapJS   = '//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.1.1/js/bootstrap.min.js';
		const jQuery        = '//cdnjs.cloudflare.com/ajax/libs/jquery/2.1.0/jquery.min.js';
		const CSS           = './css/style.css';
		/* ----------------------------------------- */
		static function Server() {
			$item['01'] = '192.168.0.3:27014';
			$item['02'] = '127.0.0.1:27015';
			$item['03'] = '127.0.0.1:27016';
			$item['04'] = '127.0.0.1:27017';
			$item['05'] = '127.0.0.1:27018';
			$item['06'] = '127.0.0.1:27019';
			// L ServerSample
			// $item['03~'] = 'Address:Port';
			return $item;
		}
	}

	class MySQL_Base {
		const HostName      = 'localhost';
		const UserName      = 'root';
		const PassWord      = 'hoge';
		const DataBase      = 'test';
		const Charset       = 'utf8mb4';
		const RankNoob      = 'kz_nub15';
		const RankPro       = 'kz_pro15';
	}

	class MySQL_Column_Pro {
		const MapName       = 'mapname';
		const AuthID        = 'authid';
		const Country       = 'country';
		const Name          = 'name';
		const Time          = 'time';
		const Date          = 'date';
		const Weapon        = 'weapon';
		const Server        = 'server';
	}

	class MySQL_Column_Noob {
		const MapName       = 'mapname';
		const AuthID        = 'authid';
		const Country       = 'country';
		const Name          = 'name';
		const Time          = 'time';
		const Date          = 'date';
		const Weapon        = 'weapon';
		const Server        = 'server';
		const CheckPoitns   = 'checkpoints';
		const GoCheck       = 'gocheck';
	}