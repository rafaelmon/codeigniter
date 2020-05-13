<html>
<head>
<title>Error de Base de datos</title>
<style type="text/css">

body {
background-color:	#fff;
margin:				40px;
font-family:		Lucida Grande, Verdana, Sans-serif;
font-size:			12px;
color:				#000;
}

#content  {
border:				#999 1px solid;
background-color:	#fff;
padding:			20px 20px 12px 20px;
}

h1 {
font-weight:		normal;
font-size:			14px;
color:				#1B405A;
margin: 			0 0 4px 0;
}
</style>
</head>
<body>
	<div id="content">
		<h1>Polidata.... - <?= $heading ?></h1>
		<p>Error al intentar conectarse con el servidor de Base de Datos<br /><?= $message ?></p>
	</div>
</body>
</html>