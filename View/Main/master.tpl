{theme:new "scarlet", 'box' default}
<!-- {theme:default "scarlet"} -->

{doctype}
<html>
	<head>
		<meta http-equiv="Content-type" content="text/html; charset=utf-8">
		<title>Scarlet</title>
		{&CSS 'css/main.css'}
	</head>
	<body>
		<div id="wrapper">			
			{box "New Project:" height = "300" width = "634" id = "projectBox"}
				{form:text "Project Name:" width = "600px"}
				{form:text "Location:" width = "600px"}
				{button "Create" float = "right" click = "createProject"}
				{button $hello, width = $width float='right'}
			{/box}
		</div>
	</body>
	{&javascript 'jquery', 'javascript/main.js'}
</html>