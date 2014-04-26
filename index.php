<?php
	require_once(dirname(__FILE__) . '/lib/config.php');
	require_once(dirname(__FILE__) . '/lib/function.php');
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
					<h1><?php echo MainSetting::PageTitle ?></h1>

					<div class="page-header">
						<h2>Welcome</h2>
					</div>
					<?php echo MainSetting::Message ?>
				</div>
			</div>
		</div>

		<?php echo Html_BodyFooter() ?>
		<?php echo Html_Footer() ?>
	</body>
</html>