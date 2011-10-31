<?php

	use \caspar\core\Caspar;

?>
<!DOCTYPE html>
<html>
<head>
<style>
@import url("http://fonts.googleapis.com/css?family=Open+Sans:400italic,700italic,400,700&subset=latin,cyrillic,greek");
@import url("http://fonts.googleapis.com/css?family=Droid+Sans+Mono&subset=latin,cyrillic,greek");

body, td, th { padding: 0px; margin: 0px; background-color: #DFDFDF; font-family: 'Open Sans', sans-serif; font-style: normal; font-weight: normal; text-align: left; font-size: 13px; line-height: 1.3; color: #222;}
h1 { margin: 5px 0 0 0; font-size: 19px; }
h2 { margin: 0 0 15px 0; font-size: 16px; }
h3 { margin: 15px 0 0 0; font-size: 14px; }
input[type=\"text"], input[type=\"password"] { float: left; margin-right: 15px; }
label { float: left; font-weight: bold; margin-right: 5px; display: block; width: 150px; }
label span { font-weight: normal; color: #888; }
.rounded_box {background: transparent; margin:0px;}
.rounded_box h4 { margin-bottom: 0px; margin-top: 7px; font-size: 14px; }
.xtop, .xbottom {display:block; background:transparent; font-size:1px;}
.xb1, .xb2, .xb3, .xb4 {display:block; overflow:hidden;}
.xb1, .xb2, .xb3 {height:1px;}
.xb2, .xb3, .xb4 {background:#F9F9F9; border-left:1px solid #CCC; border-right:1px solid #CCC;}
.xb1 {margin:0 5px; background:#CCC;}
.xb2 {margin:0 3px; border-width:0 2px;}
.xb3 {margin:0 2px;}
.xb4 {height:2px; margin:0 1px;}
.xboxcontent {display:block; background:#F9F9F9; border:0 solid #CCC; border-width:0 1px; padding: 0 5px 0 5px;}
.xboxcontent table td.description { padding: 3px 3px 3px 0;}
.white .xb2, .white .xb3, .white .xb4 { background: #FFF; border-color: #CCC; }
.white .xb1 { background: #CCC; }
.white .xboxcontent { background: #FFF; border-color: #CCC; }
pre { overflow: scroll; padding: 5px; }
</style>
<!--[if IE]>
<style>
body { background-color: #DFDFDF; font-family: sans-serif; font-size: 13px; }
</style>
<![endif]-->
</head>
<body>
<div class=\"rounded_box white" style=\"margin: 30px auto 0 auto; width: 700px;\">
	<b class=\"xtop\"><b class=\"xb1\"></b><b class=\"xb2\"></b><b class=\"xb3\"></b><b class=\"xb4\"></b></b>
	<div class=\"xboxcontent" style=\"vertical-align: middle; padding: 10px 10px 10px 15px;\">
	<h1>An error occured in Caspar</h1>
	<h2><?php echo $title; ?></h2>
	<?php
	$report_description = null;
	if ($exception instanceof \Exception)
	{
		if ($exception instanceof ActionNotFoundException)
		{
			echo "<h3>Could not find the specified action</h3>";
			$report_description = "Could not find the specified action";
		}
		elseif ($exception instanceof TemplateNotFoundException)
		{
			echo "<h3>Could not find the template file for the specified action</h3>";
			$report_description = "Could not find the template file for the specified action";
		}
		elseif ($exception instanceof \b2db\Exception)
		{
			echo "<h3>An exception was thrown in the B2DB framework</h3>";
			$report_description = "An exception was thrown in the B2DB framework";
		}
		else
		{
			echo "<h3>An unhandled exception occurred:</h3>";
			$report_description = "An unhandled exception occurred";
		}
		$report_description .= "\n" . $exception->getMessage();
		echo "<i>".$exception->getMessage()."</i><br>";
		if (class_exists("\\caspar\core\Caspar") && self::isDebugMode())
		{
			echo "<h3>Stack trace:</h3>
			<ul>";
			//echo '<pre>';var_dump($exception->getTrace());die();
			foreach ($exception->getTrace() as $trace_element)
			{
				echo '<li>';
				if (array_key_exists('class', $trace_element))
				{
					echo '<strong>'.$trace_element['class'].$trace_element['type'].$trace_element['function'].'()</strong><br>';
				}
				elseif (array_key_exists('function', $trace_element))
				{
					if (!in_array($trace_element['function'], array('tbg_error_handler', 'tbg_exception')))
						echo '<strong>'.$trace_element['function'].'()</strong><br>';
				}
				else
				{
					echo '<strong>unknown function</strong><br>';
				}
				if (array_key_exists('file', $trace_element))
				{
					echo '<span style=\"color: #55F;\">'.$trace_element['file'].'</span>, line '.$trace_element['line'];
				}
				else
				{
					echo '<span style=\"color: #C95;\">unknown file</span>';
				}
				echo '</li>';
			}
			echo "</ul>";
		}
	}
	else
	{
		echo '<h3>';
		if ($exception['code'] == 8)
		{
			echo 'The following notice has stopped further execution:';
			$report_description = 'The following notice has stopped further execution: ';
		}
		else
		{
			echo 'The following error occured:';
			$report_description = 'The following error occured: ';
		}
		echo '</h3>';
		$report_description .= $title;
		echo "$title</i><br>
		<h3>Error information:</h3>
		<ul>
			<li>";
			echo '<span style=\"color: #55F;\">'.$exception['file'].'</span>, line '.$exception['line'];
		echo "</li>
		</ul>";
		if (class_exists("\\caspar\core\Caspar") && self::isDebugMode())
		{
			echo "<h3>Backtrace:</h3>
			<ol>";
			foreach (debug_backtrace() as $trace_element)
			{
				echo '<li>';
				if (array_key_exists('class', $trace_element))
				{
					echo '<strong>'.$trace_element['class'].$trace_element['type'].$trace_element['function'].'()</strong><br>';
				}
				elseif (array_key_exists('function', $trace_element))
				{
					if (in_array($trace_element['function'], array('tbg_error_handler', 'tbg_exception'))) continue;
					echo '<strong>'.$trace_element['function'].'()</strong><br>';
				}
				else
				{
					echo '<strong>unknown function</strong><br>';
				}
				if (array_key_exists('file', $trace_element))
				{
					echo '<span style=\"color: #55F;\">'.$trace_element['file'].'</span>, line '.$trace_element['line'];
				}
				else
				{
					echo '<span style=\"color: #C95;\">unknown file</span>';
				}
				echo '</li>';
			}
			echo "</ol>";
		}
	}
	if (class_exists("\\caspar\core\Caspar") && class_exists("\\caspar\core\Logging") && self::isDebugMode())
	{
		echo "<h3>Log messages:</h3>";
		foreach (\caspar\core\Logging::getEntries() as $entry)
		{
			$color = \caspar\core\Logging::getCategoryColor($entry['category']);
			$lname = \caspar\core\Logging::getLevelName($entry['level']);
			echo "<div class=\"log_{$entry['category']}\"><strong>{$lname}</strong> <strong style=\"color: #{$color}\">[{$entry['category']}]</strong> <span style=\"color: #555; font-size: 10px; font-style: italic;\">{$entry['time']}</span>&nbsp;&nbsp;{$entry['message']}</div>";
		}
	}
	if (class_exists("\b2db\Core") && self::isDebugMode())
	{
		echo "<h3>SQL queries:</h3>";
		try
		{
			echo "<ol>";
			foreach (\b2db\Core::getSQLHits() as $details)
			{
				echo "<li>
					<b>
					<span class=\"faded_out dark small\">[";
				echo ($details['time'] >= 1) ? round($details['time'], 2) . ' seconds' : round($details['time'] * 1000, 1) . 'ms';
				echo "]</span> </b> from <b>{$details['filename']}, line {$details['line']}</b>:<br>
					<span style=\"font-size: 12px;\">{$details['sql']}</span>
				</li>";
			}
			echo "</ol>";
		}
		catch (Exception $e)
		{
			echo '<span style=\"color: red;\">Could not generate query list (there may be no database connection)</span>';
		}
	}
	echo "</div>
	<b class=\"xbottom\"><b class=\"xb4\"></b><b class=\"xb3\"></b><b class=\"xb2\"></b><b class=\"xb1\"></b></b>
</div>";
echo "
	</div>
</body>
</html>
";
