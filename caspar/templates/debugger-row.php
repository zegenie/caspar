<?php
	// prepare fields
	$dbgjson = $csp_debugger->getJsonOutput();
	$dbgstored = $csp_debugger->getStoredVariables();
	$dbglog = \caspar\core\Logging::getEntries();
	$dbgpartials = $csp_debugger->getPartials();
?>

			<div class="csp-dbg-entry-row" id="csp-dbg-row-<?php echo $cspdbgrow; ?>">
				<?php if ($csp_debugger->isAjaxRequest()): ?>AJAX Request<?php else: ?>This page<?php endif; ?> - <?php echo $csp_debugger->getRouting()->getCurrentRouteName(); ?>
				<div class="csp-dbg-entry-open" id="csp-dbg-row-<?php echo $cspdbgrow; ?>-open" onClick="cspexpandEntry(<?php echo $cspdbgrow; ?>);">
					&#x25BC;
				</div>
				<div class="csp-dbg-entry-close" id="csp-dbg-row-<?php echo $cspdbgrow; ?>-close" style="display: none;" onClick="cspcollapseEntry(<?php echo $cspdbgrow; ?>);">
					&#x25B2;
				</div>
			</div>
			<div class="csp-dbg-entry-row-content" id="csp-dbg-row-<?php echo $cspdbgrow; ?>-content" style="display: none;">
				<ul class="csp-dbg-tab-bar" id="csp-dbg-row-<?php echo $cspdbgrow; ?>1-content-tab">
					<li id="csp-dbg-row-<?php echo $cspdbgrow; ?>-content-tab-1" class="csp-dbg-row-content-tab-selected" onClick="cspchangeDebuggerTab(<?php echo $cspdbgrow; ?>,1);">Summary</li>
					<li id="csp-dbg-row-<?php echo $cspdbgrow; ?>-content-tab-2" onClick="cspchangeDebuggerTab(<?php echo $cspdbgrow; ?>,2);">JSON Output (<?php echo count($dbgjson); ?>)</li>
					<li id="csp-dbg-row-<?php echo $cspdbgrow; ?>-content-tab-3" onClick="cspchangeDebuggerTab(<?php echo $cspdbgrow; ?>,3);">Timings (<?php echo count($dbgpartials); ?>)</li>
					<li id="csp-dbg-row-<?php echo $cspdbgrow; ?>-content-tab-4" onClick="cspchangeDebuggerTab(<?php echo $cspdbgrow; ?>,4);">Caspar log (<?php echo count($dbglog); ?>)</li>
					<li id="csp-dbg-row-<?php echo $cspdbgrow; ?>-content-tab-5" onClick="cspchangeDebuggerTab(<?php echo $cspdbgrow; ?>,5);">Database queries (0 please implement me)</li>
					<li id="csp-dbg-row-<?php echo $cspdbgrow; ?>-content-tab-6" onClick="cspchangeDebuggerTab(<?php echo $cspdbgrow; ?>,6);">Stored variables (<?php echo count($dbgstored); ?>)</li>
					<li id="csp-dbg-row-<?php echo $cspdbgrow; ?>-content-tab-7" onClick="cspchangeDebuggerTab(<?php echo $cspdbgrow; ?>,7);">Backtrace</li>
				</ul>
				<div class="csp-dbg-tab-panels">
					<div id="csp-dbg-row-<?php echo $cspdbgrow; ?>-content-tab-1-panel" class="csp-dbg-tab-panel">
						<ul>
							<li><b>Route:</b> [<?php echo $csp_debugger->getRouting()->getCurrentRouteName(); ?>] <?php echo $csp_debugger->getRouting()->getCurrentRouteModule(); ?> / <?php echo $csp_debugger->getRouting()->getCurrentRouteAction(); ?></li>
							<li><b>Execution time:</b> <?php echo $csp_debugger->getExecutionTime(); ?> seconds</li>
							<li><b>AJAX query?</b> <?php if ($csp_debugger->isAjaxRequest()): ?>Yes<?php else: ?>No<?php endif; ?></li>
						</ul>
					</div>
					<div id="csp-dbg-row-<?php echo $cspdbgrow; ?>-content-tab-2-panel" class="csp-dbg-tab-panel" style="display: none;">
						<?php if (!$csp_debugger->isAjaxRequest()): ?>
						<p><i>This tab only applies to AJAX requests.</i></p>
						<?php else: ?>
						<p><i>This is a list of all items in the JSON output.</i></p>
						<?php foreach ($dbgjson as $field => $value): ?>
						<div class="csp-dbg-json-field"><?php echo $field; ?></div>
						<pre class="csp-dbg-json-value"><?php echo $value; ?></pre>
						<?php endforeach; ?>
						<?php endif; ?>
					</div>
					<div id="csp-dbg-row-<?php echo $cspdbgrow; ?>-content-tab-3-panel" class="csp-dbg-tab-panel" style="display: none;">
						<p><i>This list shows how long actions and templates in this request take to load.</i></p>
						<?php foreach ($dbgpartials as $partial => $data): ?>
							<span class="csp-dbg-timing-id"><?php echo $partial; ?></span> <span class="csp-dbg-timing-time">Accessed <?php echo $data['count']; ?> time(s), totalling <?php echo ($data['time'] >= 1) ? round($data['time'], 2) . ' seconds' : round($data['time'] * 1000, 1) . 'ms'; ?></span><br />
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
						<p><i>This list shows all the SQL queries that have been made, and how long each took.</i></p>
						<div class="csp-dbg-query-title">
							<span class="csp-dbg-query-title-id">Query 1</span> <span class="csp-dbg-query-title-time">10ms</span> <span class="csp-dbg-query-title-file"> - foo.php:123</span>
						</div>
						<div class="csp-dbg-query-body">
							<code>SELECT * FROM foo;</code>
						</div>
						<div class="csp-dbg-query-title">
							<span class="csp-dbg-query-title-id">Query 2</span> <span class="csp-dbg-query-title-time">10ms</span> <span class="csp-dbg-query-title-file"> - foo.php:123</span>
						</div>
						<div class="csp-dbg-query-body">
							<code>SELECT * FROM foo;</code>
						</div>
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
									<span class="csp-dbg-trace-function"><?php echo $trace_element['class'].$trace_element['type'].$trace_element['function']; ?>()</span>
								<?php elseif (array_key_exists('function', $trace_element) && array_key_exists('class', $trace_element) && $trace_element['class'] != 'caspar\core\Caspar' && !in_array($trace_element['function'], array('errorHandler', 'exceptionHandler'))): ?>
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
										foreach($trace_element['args'] as $arg) {
											echo '<li>'.$arg.'</li>';
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

