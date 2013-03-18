<?php
	// prepare fields
	$dbgjson = $csp_debugger->getJsonOutput();
	$dbgstored = $csp_debugger->getStoredVariables();
	$dbglog = \caspar\core\Logging::getEntries();
	$dbgpartials = $csp_debugger->getPartials();

	if (\b2db\Core::isConnected()) {
		$dbgqueries = \b2db\Core::getSQLHits();
		$dbgquerytime = \b2db\Core::getSQLTiming();
		$dbgquerycount = \b2db\Core::getSQLCount();
	} 
?>

			<div class="csp-dbg-entry-row" id="csp-dbg-row-<?php echo $cspdbgrow; ?>">
				<?php if ($csp_debugger->isAjaxRequest()): ?>AJAX Request<?php else: ?>This page<?php endif; ?> - <?php echo $csp_debugger->getRouting()->getCurrentRouteName(); ?>
				<span class="csp-dbg-entry-timestamp">
					&nbsp;&nbsp;<?php echo date('H:i:s (d-m-Y)'); ?>
				</span>
				<div class="csp-dbg-entry-open" id="csp-dbg-row-<?php echo $cspdbgrow; ?>-open" onClick="cspexpandEntry(<?php echo $cspdbgrow; ?>);">
					&#x25BC;
				</div>
				<div class="csp-dbg-entry-close" id="csp-dbg-row-<?php echo $cspdbgrow; ?>-close" style="display: none;" onClick="cspcollapseEntry(<?php echo $cspdbgrow; ?>);">
					&#x25B2;
				</div>
			</div>
			<div class="csp-dbg-entry-row-content" id="csp-dbg-row-<?php echo $cspdbgrow; ?>-content" style="display: none;">
				<div class="csp-dbg-tab-bar">
					<ul id="csp-dbg-row-<?php echo $cspdbgrow; ?>1-content-tab">
						<li id="csp-dbg-row-<?php echo $cspdbgrow; ?>-content-tab-1" class="csp-dbg-row-content-tab-selected" onClick="cspchangeDebuggerTab(<?php echo $cspdbgrow; ?>,1);">Summary</li>
						<li id="csp-dbg-row-<?php echo $cspdbgrow; ?>-content-tab-2" onClick="cspchangeDebuggerTab(<?php echo $cspdbgrow; ?>,2);">JSON Output (<?php echo count($dbgjson); ?>)</li>
						<li id="csp-dbg-row-<?php echo $cspdbgrow; ?>-content-tab-3" onClick="cspchangeDebuggerTab(<?php echo $cspdbgrow; ?>,3);">Timings (<?php echo count($dbgpartials); ?>)</li>
						<li id="csp-dbg-row-<?php echo $cspdbgrow; ?>-content-tab-4" onClick="cspchangeDebuggerTab(<?php echo $cspdbgrow; ?>,4);">Caspar log (<?php echo count($dbglog); ?>)</li>
						<li id="csp-dbg-row-<?php echo $cspdbgrow; ?>-content-tab-5" onClick="cspchangeDebuggerTab(<?php echo $cspdbgrow; ?>,5);">Database queries (<?php if (\b2db\Core::isConnected()): echo $dbgquerycount-1; else: echo 'not connected'; endif; ?>)</li>
						<li id="csp-dbg-row-<?php echo $cspdbgrow; ?>-content-tab-6" onClick="cspchangeDebuggerTab(<?php echo $cspdbgrow; ?>,6);">Stored variables (<?php echo count($dbgstored); ?>)</li>
						<li id="csp-dbg-row-<?php echo $cspdbgrow; ?>-content-tab-7" onClick="cspchangeDebuggerTab(<?php echo $cspdbgrow; ?>,7);">Backtrace</li>
					</ul>
				</div>
				<div class="csp-dbg-tab-panels">
					<div id="csp-dbg-row-<?php echo $cspdbgrow; ?>-content-tab-1-panel" class="csp-dbg-tab-panel">
						<ul>
							<li><b>Route:</b> [<?php echo $csp_debugger->getRouting()->getCurrentRouteName(); ?>] <?php echo $csp_debugger->getRouting()->getCurrentRouteModule(); ?> / <?php echo $csp_debugger->getRouting()->getCurrentRouteAction(); ?></li>
							<li><b>Execution time:</b> <?php echo $csp_debugger->getExecutionTime(); ?> seconds</li>
							<li><b>AJAX query?</b> <?php if ($csp_debugger->isAjaxRequest()): ?>Yes<?php else: ?>No<?php endif; ?></li>
						</ul>
						<b>SQL details:</b>
<?php if (\b2db\Core::isInitialized()): ?>
						<ul>
							<li><b>Driver:</b> <?php echo \b2db\Core::getDBtype(); ?></li>
							<li><b>DSN:</b> <?php echo \b2db\Core::getDSN(); ?>
								<ul>
									<li><b>Hostname:</b> <?php echo \b2db\Core::getHost(); ?>:<?php echo \b2db\Core::getPort(); ?></li>
									<li><b>Database:</b> <?php echo \b2db\Core::getDBname(); ?></li>
								</ul>
							</li>
							<li><b>Username:</b> <?php echo \b2db\Core::getUname(); ?></li>
							<li><b>Prefix:</b> <?php echo \b2db\Core::getTablePrefix(); ?></li>
							<li><b>Connection status:</b> <?php echo (\b2db\Core::isConnected()) ? 'Connected' : 'Not connected'; ?></li>
<?php if (\b2db\Core::isConnected()): ?>
							<li><b>Total query time:</b> <?php if(empty($dbgquerytime)): echo '0 ms'; else: echo ($dbgquerytime > 1) ? round($dbgquerytime, 2) . 's' : round($dbgquerytime * 1000, 1) . 'ms'; endif; ?></li>
<?php endif; ?>
						</ul>
<?php else: ?>
						<i>B2DB not initialised</i>
<?php endif; ?>
					</div>
					<div id="csp-dbg-row-<?php echo $cspdbgrow; ?>-content-tab-2-panel" class="csp-dbg-tab-panel" style="display: none;">
						<?php if (!$csp_debugger->isAjaxRequest()): ?>
							<p><i>This tab only applies to AJAX requests.</i></p>
						<?php else: ?>
							<p><i>This is a list of all items in any JSON output from the call. If this list is empty, the AJAX request has not used JSON.</i></p>
							<?php if (is_array($dbgjson)): ?>
								<?php foreach ($dbgjson as $field => $value): ?>
								<div class="csp-dbg-json-field"><?php echo $field; ?></div>
								<pre class="csp-dbg-json-value"><?php echo (is_array($value)) ? 'array' : $value; ?></pre>
								<?php endforeach; ?>
							<?php endif; ?>
						<?php endif; ?>
					</div>
					<div id="csp-dbg-row-<?php echo $cspdbgrow; ?>-content-tab-3-panel" class="csp-dbg-tab-panel" style="display: none;">
						<p><i>This list shows how long actions and templates in this request take to load.</i></p>
						<?php foreach ($dbgpartials as $partial => $data): ?>
							<span class="csp-dbg-timing-id"><?php echo $partial; ?></span> <span class="csp-dbg-timing-time">Accessed <?php echo $data['count']; ?> time(s), totalling <?php echo ($data['time'] >= 1) ? round($data['time'], 2) . ' seconds' : round($data['time'] * 1000, 1) . 'ms'; ?>, averaging <?php echo (($data['time']/$data['count']) >= 1) ? round(($data['time']/$data['count']), 2) . ' seconds' : round(($data['time']/$data['count']) * 1000, 1) . 'ms'; ?></span><br />
						<?php endforeach; ?>
					</div>
					<div id="csp-dbg-row-<?php echo $cspdbgrow; ?>-content-tab-4-panel" class="csp-dbg-tab-panel" style="display: none;">
						<p><i>Additional items can be added to this list using the <code>log</code> method in the Logging class - see the documentation for details.</i></p>
						<div>
						<?php foreach ($dbglog as $entry): ?>
							<?php $color = \caspar\core\Logging::getCategoryColor($entry['category']); ?>
							<?php $lname = \caspar\core\Logging::getLevelName($entry['level']); ?>
							<span class="csp-dbg-log-level"><?php echo $lname; ?></span> <span class="csp-dbg-log-source" style="color: #<?php echo $color; ?>">[<?php echo $entry['category']; ?>]</span> <span class="csp-dbg-log-time"><?php echo $entry['time']; ?></span>&nbsp;&nbsp;<span class="csp-dbg-log-msg"><?php echo $entry['message']; ?></span><br />
						<?php endforeach; ?>
						</div>
					</div>
					<div id="csp-dbg-row-<?php echo $cspdbgrow; ?>-content-tab-5-panel" class="csp-dbg-tab-panel" style="display: none;">
<?php if (\b2db\Core::isConnected()): ?>
						<p><i>This list shows all the SQL queries that have been made, and how long each took.</i></p>
						<?php $i = 0; ?>
						<?php foreach ($dbgqueries as $entry): ?>
							<?php $i++; ?>
							<div class="csp-dbg-query-title">
								<span class="csp-dbg-query-title-id">Query <?php echo $i; ?></span> <span class="csp-dbg-query-title-time"><?php echo ($entry['time'] >= 1) ? round($entry['time'], 2) . ' seconds' : round($entry['time'] * 1000, 1) . 'ms'; ?></span> <span class="csp-dbg-query-title-file"> - <?php echo $entry['filename']; ?>:<?php echo $entry['line']; ?></span>
							</div>
						<?php endforeach; ?>
<?php else: ?>
						<p><i>Not connected to the database. Ensure the connection parameters are correct on the Summary tab, and run <code>\b2db\Core::doConnect();</code> to connect to the database.</i></p>
<?php endif; ?>
					</div>
					<div id="csp-dbg-row-<?php echo $cspdbgrow; ?>-content-tab-6-panel" class="csp-dbg-tab-panel" style="display: none;">
						<p><i>You can store a variable for inspection here using the <code>storeVariable()</code> method on your Debugger instance - see the documentation for details.</i></p>
						<?php foreach ($dbgstored as $field => $data): ?>
						<div class="csp-dbg-variable-title"><span class="csp-dbg-variable-title-id"><?php echo $field; ?></span> <span class="csp-dbg-variable-title-file"> - <?php echo $data['file'].':'.$data['line']; ?></span></div>
						<pre class="csp-dbg-variable-content"><?php var_dump($data['value']); ?></pre>
						<?php endforeach; ?>
					</div>
					<div id="csp-dbg-row-<?php echo $cspdbgrow; ?>-content-tab-7-panel" class="csp-dbg-tab-panel" style="display: none;">
						<ul>
							<?php $trace = debug_backtrace(); ?>
							<?php foreach ($trace as $trace_element): ?>
								<?php if (array_key_exists('class', $trace_element) && $trace_element['class'] == 'caspar\core\Caspar' && array_key_exists('function', $trace_element) && $trace_element['function'] == 'errorHandler') continue; ?>
								<li>
								<?php if (array_key_exists('class', $trace_element)): ?>
									<?php if ($trace_element['class'] != 'caspar\core\Caspar' && in_array($trace_element['function'], array('errorHandler', 'exceptionHandler'))): continue; endif; ?>
									<span class="csp-dbg-trace-function"><?php echo $trace_element['class'].$trace_element['type'].$trace_element['function']; ?>()</span>
								<?php elseif (array_key_exists('function', $trace_element)): ?>
									<span class="csp-dbg-trace-function"><?php echo $trace_element['function']; ?>()</span>
								<?php else: ?>
									<span class="csp-dbg-trace-function">unknown function</span>
								<?php endif; ?>
								<br>
								<?php if (array_key_exists('file', $trace_element)): ?>
									<span class="csp-dbg-trace-file"><?php echo $trace_element['file']; ?></span>, line <?php echo $trace_element['line']; ?>
								<?php else: ?>
									<span class="csp-dbg-trace-file csp-dbg-trace-file-unknown">unknown file</span>
								<?php endif; ?>
								<?php if (array_key_exists('args', $trace_element) && count($trace_element['args']) > 0): ?>
									<br /><i>Arguments</i>
									<ol>
										<?php
										foreach($trace_element['args'] as $varname => $arg) {
											echo '<li>';
											switch (true) {
												case is_object($arg):
													echo "Object of class ". get_class($arg);
													break;
												case is_bool($arg):
													echo ($arg) ? 'true' : 'false';
													break;
												case is_array($arg):
													echo 'keys: '.join(', ', array_keys($arg));
													break;
												default:
													echo $arg;
											}
											echo '</li>';
										}
										?>
									</ol>
								<?php endif; ?>
								</li>
							<?php endforeach; ?>
						</ul>
					</div>
				</div>
			</div>

