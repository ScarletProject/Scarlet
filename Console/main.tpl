{doctype}
<html>
	<head>
		<meta http-equiv="Content-type" content="text/html; charset=utf-8">
		<title>Console</title>
		{&CSS 'reset', 'css/console.css', 'css/switcher.css'}
	</head>
	<body>
	<div id="wrapper">
		<div id="intro">
			<h1 style="float:left">Scarlet Console</h1>
			<p class="field switch" style="float:right">
			    <input type="radio" id="radio1" name="field"  checked />
			    <input type="radio" id="radio2" name="field" />
			    <label for="radio1" class="cb-enable selected"><span>Html</span></label>
			    <label for="radio2" class="cb-disable"><span>Text</span></label>
			</p>
		</div>
		<div id="left-pane">
			<div id="console">
				<textarea></textarea>
			</div>
			<div id="assets">
				<ul></ul>
			</div>
		</div>
		<div id="right-pane">
			<div id="preview">
				
			</div>
		</div>
	</div><!-- wrapper -->
	</body>
	{Javascript 'jquery', 'javascript/console.js', 'javascript/switcher.js'}
</html>