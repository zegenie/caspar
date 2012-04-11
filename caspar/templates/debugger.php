<?php if ($csp_debugger instanceof \caspar\core\Debugger): ?>
	<script type="text/javascript" language="javascript"> 
		function cspexpandEntry(row) {
			document.getElementById("csp-dbg-row-" + row + "-close").style.display = "block";
			document.getElementById("csp-dbg-row-" + row + "-open").style.display = "none";
			document.getElementById("csp-dbg-row-" + row + "-content").style.display = "block";
		}

		function cspcollapseEntry(row) {
			document.getElementById("csp-dbg-row-" + row + "-close").style.display = "none";
			document.getElementById("csp-dbg-row-" + row + "-open").style.display = "block";
			document.getElementById("csp-dbg-row-" + row + "-content").style.display = "none";
		}

		function cspcloseDebugger() {
			document.getElementById("csp-dbg-main").style.display = "none";
		}

		function cspopenDebugger() {
			document.getElementById("csp-dbg-main").style.display = "block";
		}

		function cspchangeDebuggerTab(row, tab) {
			document.getElementById("csp-dbg-row-" + row + "-content-tab-1-panel").style.display = "none";
			document.getElementById("csp-dbg-row-" + row + "-content-tab-2-panel").style.display = "none";
			document.getElementById("csp-dbg-row-" + row + "-content-tab-3-panel").style.display = "none";
			document.getElementById("csp-dbg-row-" + row + "-content-tab-4-panel").style.display = "none";
			document.getElementById("csp-dbg-row-" + row + "-content-tab-5-panel").style.display = "none";
			document.getElementById("csp-dbg-row-" + row + "-content-tab-6-panel").style.display = "none";
			document.getElementById("csp-dbg-row-" + row + "-content-tab-7-panel").style.display = "none";

			document.getElementById("csp-dbg-row-" + row + "-content-tab-1").className = "";
			document.getElementById("csp-dbg-row-" + row + "-content-tab-2").className = "";
			document.getElementById("csp-dbg-row-" + row + "-content-tab-3").className = "";
			document.getElementById("csp-dbg-row-" + row + "-content-tab-4").className = "";
			document.getElementById("csp-dbg-row-" + row + "-content-tab-5").className = "";
			document.getElementById("csp-dbg-row-" + row + "-content-tab-6").className = "";
			document.getElementById("csp-dbg-row-" + row + "-content-tab-7").className = "";

			document.getElementById("csp-dbg-row-" + row + "-content-tab-" + tab + "-panel").style.display = "block";
			document.getElementById("csp-dbg-row-" + row + "-content-tab-" + tab).className = "csp-dbg-row-content-tab-selected";
		}
	</script>
	<div id="csp-dbg-open">
		<div id="csp-dbg-open-btn" onClick="cspopenDebugger();"><span>Open debugger</span></div>
	</div>
	<div id="csp-dbg-main" style="display: none;">
		<div id="csp-dbg-title">
			Debug Console
			<div class="csp-dbg-title-close" onClick="cspcloseDebugger();">
				&times;
			</div>
		</div>
		<div id="csp-dbg-content">
			<?php $csp_debugger->getCurrentPageRow(); ?>
		</div>
	</div>
</body>

</html>
<?php endif; ?>

