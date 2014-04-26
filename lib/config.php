<?php
	class MainSetting {
		const Language      = 'en-us'; // <html lang="en-us">
		const PageTitle     = 'ProKreedz Plus @ Japan';
		const Message       = '<p>Welcome!! powered by php and MySQL.</p>';
		const MapOnPage     = 15;
		/* ----------------------------------------- */
		const BootStrapCSS  = '//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.1.1/css/bootstrap.min.css';
		const BootStrapJS   = '//cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.1.1/js/bootstrap.min.js';
		const jQuery        = '//cdnjs.cloudflare.com/ajax/libs/jquery/2.1.0/jquery.min.js';
		const CSS           = './css/style.css';
		const CupImages     = './img/cups/';
		const FlagImages    = './img/flags/';
		const WeaponImages  = './img/weapons/?w=';
		const SteamError    = './img/noneavatar.jpg';
	}

	class MySQL_Base {
		const HostName      = 'localhost';
		const UserName      = 'root';
		const PassWord      = 'testpw';
		const DataBase      = 'testdb';
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
		const CheckPoints   = 'checkpoints';
		const GoCheck       = 'gocheck';
	}