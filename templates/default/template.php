<head>
	<meta charset="utf-8"> 
</head>
<body>
	<div id="error">
		%error%
	</div>
	
	<div id="menu">
		<ul>
			<li><a href="<?= $this->site_url; ?>">Main</a></li>
			<li><a href="<?= $this->site_url; ?>index.php/?component=tasks&action=list">Task List</a></li>
		</ul>
	</div>
	<b>aaa Здесь будет лежать какой-то текст в HTML</b>
	<div id="top-content">
		%top%
	</div>
	<div id="bottom-content">
		%bottom%
	</div>
</body>
