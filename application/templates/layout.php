<!DOCTYPE html>
<html lang="<?php echo \caspar\core\Caspar::getI18n()->getHTMLLanguage(); ?>">
	<head>
		<meta charset="<?php echo \caspar\core\Caspar::getI18n()->getCharset(); ?>">
		<?php \caspar\core\Event::createNew('core', 'header_begins')->trigger(); ?>
		<title><?php echo strip_tags($csp_response->getTitle()); ?></title>
		<link rel="shortcut icon" href="<?php print $csp_response->getFaviconURL(); ?>">
		<?php foreach ($csp_response->getFeeds() as $feed_url => $feed_title): ?>
			<link rel="alternate" type="application/rss+xml" title="<?php echo str_replace('"', '\'', $feed_title); ?>" href="<?php echo $feed_url; ?>">
		<?php endforeach; ?>
		<?php foreach ($csp_response->getStylesheets() as $css): ?>
			<link rel="stylesheet" href="<?php echo $css; ?>">
		<?php endforeach; ?>

		<?php foreach ($csp_response->getJavascripts() as $js): ?>
			<script type="text/javascript" src="<?php echo $js; ?>"></script>
		<?php endforeach; ?>
		  <!--[if lt IE 9]>
			  <script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
		  <![endif]-->
		<?php \caspar\core\Event::createNew('core', 'header_ends')->trigger(); ?>
	</head>
	<body>
		<?php echo $content; ?>
	</body>
</html>