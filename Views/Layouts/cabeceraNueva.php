
<header id="main-header">

    <!--<a id="logo-header">
        <span class="site-name">BERDEZ S.A.S</span>
        <span>ALMACEN</span>
    </a> <!-- / #logo-header -->

    <a href="?controller=Dashboard&action=show" id="logo-header">
        <img src="/Berdez/fpdf/tutorial/logo.png" alt="Constructora Berdez">
    </a>

    <nav class="menuCSS3">
            <ul>
                <li><a href="?controller=Dashboard&action=show">Inicio</a></li>
                <li><a href="?controller=RegistroEntradas&action=show">Entrada Material</a></li>
                <li><a href="?controller=RegistroSalidas&action=show">Salida Material</a></li>
                <?php if (Permiso::usuarioPuede('inventario.ajustar')) { ?>
                <li><a href="?controller=AjusteInventario&action=show">Ajustes de Inventario</a></li>
                <?php } ?>
                <li><a href="#">Informes</a>
                    <ul>
                        <li><a href="?controller=Material&action=show">Inventario Actual</a></li>
                        <li><a href="?controller=Movimientos&action=show">Movimientos</a></li>
                        <li><a href="?controller=Kardex&action=show">Kardex</a></li>
                        <li><a href="?controller=InformeDetallado&action=show">Salidas por Ubicación</a></li>
                        <li><a href="?controller=InformePorUsuario&action=show">Por Usuario</a></li>
                        <li><a href="?controller=Solicitud&action=reporte">Informe de Solicitudes</a></li>
                        <li><a href="?controller=Material&action=sinMovimiento">Materiales sin Movimiento</a></li>
                    </ul>
                </li>
                <?php if (Permiso::usuarioPuede('solicitud.ver')) { ?>
                <li><a href="?controller=Solicitud&action=show">Solicitudes</a></li>
                <?php } ?>
                <li><a href="?controller=Material&action=show">Gestion Material</a></li>
                <li><a href="#">Gestion Destino</a>
                    <ul>
                        <li><a href="?controller=Rubro&action=show">Actividad</a></li>
                        <li><a href="?controller=Destino&action=show">Destino</a></li>
                        <li><a href="?controller=Ubicacion&action=show">Ubicaciones</a></li>
                        <li><a href="?controller=TipoUbicacion&action=show">Tipos de Ubicación</a></li>
                        <li><a href="?controller=Proveedor&action=show">Proveedor</a></li>
                        <li><a href="?controller=Almacen&action=show">Almacén</a></li>
                        <li><a href="?controller=Proyecto&action=show">Proyecto</a></li>
                        <li><a href="?controller=Contratista&action=show">Contratista</a></li>
                    </ul>

                </li>
                <?php if (Permiso::usuarioPuede('usuario.gestionar') || Permiso::usuarioPuede('rol.gestionar') || Permiso::usuarioPuede('notificacion.gestionar')) { ?>
                <li><a href="#">Usuarios y Roles</a>
                    <ul>
                        <?php if (Permiso::usuarioPuede('usuario.gestionar')) { ?>
                        <li><a href="?controller=Usuario&action=show">Gestion Usuario</a></li>
                        <?php } ?>
                        <?php if (Permiso::usuarioPuede('rol.gestionar')) { ?>
                        <li><a href="?controller=Rol&action=show">Gestion Roles</a></li>
                        <?php } ?>
                        <?php if (Permiso::usuarioPuede('notificacion.gestionar')) { ?>
                        <li><a href="?controller=NotificacionDestinatario&action=show">Destinatarios de Alertas</a></li>
                        <?php } ?>
                    </ul>
                </li>
                <?php } ?>
                <?php if (Permiso::usuarioPuede('auditoria.ver')) { ?>
                <li><a href="?controller=Auditoria&action=show">Auditoria</a></li>
                <?php } ?>
            </ul>
    </nav>
    
    <a id="boton-cerrar" class="btn btn-outline-warning" href="?controller=Login&&action=salir"><span class="glyphicon glyphicon-log-out"> </span> Salir</a>

</header>