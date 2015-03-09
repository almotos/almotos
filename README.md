almotos
=======

Software para la gestion comercial de establecimientos dedicados a la compra/venta de motopartes.

1)Instalacion:

1.1)Requisitos y configuración del ambiente: 
				- PHP version 5.3 o superior.
				- MySql version 5.0 o superior.
				- Apache 2

1.2)Activar Modulos Necesarios:
	1.2.1)Apache:
				- Activar Modulo de reescritura. (sudo a2enmod rewrite)
				- Permitir la reescritura: (En apache2.conf  cambiar los "AllowOverride none" por "AllowOverride All").
				- Se puede instalar directamente en la carpeta "root" (normalmente /var/www/ o /var/www/html/) o se puede instalar haciendo uso de un host virtual:
					-en "/etc/apache2/sites-available/" agregar el archivo con el nombre del Host Virtual, digamos por ejemplo "almotos[.conf]" (El .conf puede ser variable dependiendo de la version del apache, por eso se pone entre llaves).
					-El contenido del archivo "almotos[.conf]" sería algo así:
						"<VirtualHost almotos>
							ServerAdmin almotos@gmail.com
						     ServerName almotos
						     ServerAlias almotos
						     DocumentRoot /home/almotos
						     <Directory /home/almotos/>
						          Options Indexes FollowSymLinks
						          AllowOverride All
						          Require all granted
						     </Directory>
						 </VirtualHost>" 
					- Activar el "sitio" con el comando: "sudo a2ensite almotos"

	1.2.2)AccessFile: el archivo de acceso al proyecto es el archivo .htaccess, encargado de la reescritura de las URL y de establecer ciertos valores de la configuración de los logs, apache y php.


	1.2.3)Logging: El sistema de logs consta de dos partes, una los logs de las consultas SQL y los logs de errores del PHP.
		-Logs de PHP : En el archivo .htaccess modificar la linea 4 que dice: php_value error_log "/varlog/syslog.log" por: php_value error_log "/var/log/almotos_php_log.log" luego crear este archivo y establecerle los permisos:

		sudo touch /var/log/almotos_php_log.log && sudo chmod 7777 /var/log/almotos_php_log.log

		-Logs de SQL: la clase encargada de la interacción con la BD tiene un atributo llamado "depurar" que cuando se pone a "true" hará que la consulta SQL inmediatamente siguiente sea "grabada" en el archivo syslog. por ejemplo, justo antes de una consulta de selección: $sql->depurar = true;  escribirá esa consulta SQL en /var/log/syslog

1.3)Bases de datos: La aplicación cuenta con dos bases de datos: una llamada "global" y otra llamada "almotos". La base de datos almacena las tablas que son comunes a todas las empresas que utilizan el sistema, y la base de datos "almotos" almacena tablas particulares a cada empresa. Estas tablas son identificables para cada empresa por su prefijo numerico posterior al prefijo básico de cada tabla, por ejemplo, la tabla "facturas_venta":
 	fom_737_facturas_venta  -> fom= prefijo básico  |  737= id del cliente (empresa) 

1.3.1)Instalacion: 
		-Para instalar la Base de datos global, descargue el archivo SQL de (TBD)
		-Para instalar la Base de datos almotos, descargue el archivo SQL de (TBD)
		-O corra el script PHP ubicado en la carpeta "/scripts/instalacion/main.php" con el comando:
		php main.php --empresa=NombreEmpresa --admin=true 

