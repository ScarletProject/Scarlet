
{doctype}
<html>
	<head>
		<meta http-equiv="Content-type" content="text/html; charset=utf-8">
		<title>Scarlet</title>
		{&CSS 'css/main.css'}
	</head>
	<body>
		<div id="wrapper">
			{form:text "Hello World!", width = '700px', class = 'blah', theme = "blue"}
			
			

			{box:header "Hello World!" height='30' rounded = '5px'}
			{theme:new "blue", "form:text"}
			
				Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.
			{/box:header}
		</div>
	</body>
	{&javascript}
</html>