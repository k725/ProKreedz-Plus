<?php
	require_once(dirname(__FILE__).'/config.php');
	
	function MySQL_PDO_Connect()
	{
		return 'mysql:host='.MySQL_Base::HostName.'; dbname='.MySQL_Base::DataBase.'; charset='.MySQL_Base::Charset.';';
	}

	function h($str, $encoding='UTF-8')
	{
		return htmlspecialchars($str, ENT_QUOTES, $encoding);
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
					<button class="navbar-toggle" type="button" data-toggle="collapse" data-target=".bs-navbar-collapse">
						<span class="sr-only">Toggle navigation</span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button>
					<a class="navbar-brand navbar-brand_fix mfont" href="/">ProKreedz Plus</a>
				</div>
				<nav class="collapse navbar-collapse bs-navbar-collapse" role="navigation">
					<ul class="nav navbar-nav">
						<li class="dropdown"><a href="#" class="dropdown-toggle" data-toggle="dropdown">Server<b class="caret"></b></a>
							<ul class="dropdown-menu">
								<li><a href="#">PlayerStatic</a></li>
								<li><a href="#">MapStatic</a></li>
								<li><a href="#">LastPro</a></li>
							</ul>
						</li>
					</ul>
				</nav>
			</div>
		</header>' . "\n";
	}

	function Html_BodyFooter()
	{
		return 'ProKreedz Plus by <a href="//hoshinoa.me">k725</a> / AMXX Plugin by <a href="//forums.alliedmods.net/showthread.php?t=130417">nucLeaR</a>' . "\n";
	}

	function menu($page, $value)
	{
		$tmp = '';

		if ( $page == 'pro15' || $page == 'all15' )
		{
			$tmp .= $_GET['map'] . '<br>'
				 . '<br>WORLDRECORD: ' . $value;
		}

		if ( $page == 'nub15' ) {
			$tmp .= $_GET['map'] . '<br>';
		}
		if ( $page == 'map') {
			$tmp .= '<p>Maps <span class="badge">' . $value . '</span></p>' . "\n";
		}
		if ( $page == 'lastpro' ) {
			$tmp .= 'LastPro<br>';
		}
		
		$tmp .='					<div style="text-align: right;">
						<div class="btn-group">
							<a href="'.MainSetting::MainPage.'" class="btn btn-default">Home</a>
							<a href="players.php" class="btn btn-default">Player statistic</a>
							<a href="map.php" class="btn btn-default active">Map statistic</a>
							<a href="lastpro.php" class="btn btn-default">LastPro</a>
						</div>
					</div>' . "\n";
		
		if ( $page == 'all15' ||  $page == 'nub15' || $page == 'pro15' )
		{
			$tmp .= '<a href="pro15.php?&map=' . $_GET['map'] . '">pro15</a>
				 <a href="nub15.php?&map=' . $_GET['map'] . '">nub15</a>
				 <a href="pronub15.php?&map=' . $_GET['map'] . '">pro15 & nub15</a>';
		}

		return $tmp;
	}
