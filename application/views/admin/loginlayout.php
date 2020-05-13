<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Login</title>
<link href="css/login/estilo.css" rel="stylesheet" type="text/css">
<link rel="shortcut icon" href="<?=URL_BASE?>images/favicon.ico" type="image/x-icon" />
</head>

<body style="background-image:url(css/login/sdj.jpg)">

	<div class="container">
		<form class="signIn active-dx" action="admin" method="post">
			<h3>Bienvenido a<br> SDJ</h3>
			<input type="text" id="username" name="username" placeholder="Usuario" autocomplete="off" required />
			<input type="password" id="password" name="password" placeholder="Contraseña" required />
			<button class="form-btn dx" type="submit">Acceder</button>
		</form>
	</div>

</body>
</html>