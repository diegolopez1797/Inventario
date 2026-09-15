<?php
// Plantilla de configuracion SMTP. Copiar este archivo como "mail.config.php"
// (en la raiz del proyecto) y completar con las credenciales reales.
// "mail.config.php" esta excluido de Git mediante .gitignore.
// Los destinatarios de las alertas de stock minimo ya NO se configuran aqui: se administran
// desde Berdez en "Usuarios y Roles > Destinatarios de Alertas" (tabla notificacion_destinatario).

return [
	'host' => 'smtp.gmail.com',
	'port' => 587,
	'smtp_secure' => 'tls',
	'username' => 'correo@ejemplo.com',
	'password' => 'clave-de-aplicacion',
	'from_email' => 'correo@ejemplo.com',
	'from_name' => 'Almacen Berdez',
];
