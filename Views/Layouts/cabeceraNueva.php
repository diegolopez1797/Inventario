
<header id="main-header">

    <a id="logo-header">
        <span class="site-name">BERDEZ S.A.S</span>
        <span>ALMACEN</span>
    </a> <!-- / #logo-header -->

    <a id="boton-cerrar" class="btn btn-outline-warning" href="?controller=Login&&action=salir"><span class="glyphicon glyphicon-log-out"> </span> Salir</a>

    <nav class="menuCSS3">
            <ul>
                <li><a href="?controller=RegistroEntradas&action=show">Entrada Material</a></li>
                <li><a href="?controller=RegistroSalidas&action=show">Salida Material</a></li>
                <li><a href="#">Informe Entrada</a>
                    <ul>
                        <li>
                           <li><a href="?controller=InformeEntrada&action=show">Por No. entrada</a></li>
                           <li><a href="?controller=InformeEntradaPorFecha&action=show">Por fecha</a></li>
                           <li><a href="?controller=InformeEntradaPorMaterialFecha&action=show">Por material y fecha</a></li>
                        </li>
                    </ul>
                </li>
                <li><a href="#">Informe Salida</a>
                    <ul>
                        <li>
                           <li><a href="?controller=InformeSalida&action=show">Por No. salida</a></li>
                           <li><a href="?controller=InformeSalidaPorFecha&action=show">Por fecha</a></li>
                           <li><a href="?controller=InformeSalidaPorMaterialFecha&action=show">Por material y fecha</a></li>
                           <li><a href="?controller=InformeSalidaPorCasa&action=show">Por casa</a></li>
                           <li><a href="?controller=InformeSalidaPorContratistaFecha&action=show">Por contratista y fecha</a></li>
                           <li><a href="?controller=InformeDetallado&action=show">Por material y proyecto</a></li>
                        </li>
                    </ul>
                </li>
                <li><a href="#">Informe Material</a>
                    <ul>
                        <li><a href="?controller=InformeGeneral&action=show">Completo</a></li>
                        <li><a href="?controller=InformeMaterialPorExistencia&action=show">Existente</a></li>
                        <li><a href="?controller=InformeMaterialPorCompra&action=show">Por compra</a></li>
                    </ul>
                </li>
                <li><a href="?controller=Material&action=show">Gestion Material</a></li>
                <li><a href="#">Gestion Destino</a>
                    <ul>
                        <li><a href="?controller=Destino&action=show">Destino</a></li>
                        <li><a href="?controller=Casa&action=show">Casa</a></li>
                        <li><a href="?controller=Manzana&action=show">Manzana</a></li>
                        <li><a href="?controller=Area&action=show">Etapa</a></li>
                        <li><a href="?controller=Proyecto&action=show">Proyecto</a></li>
                        <li><a href="?controller=Contratista&action=show">Contratista</a></li>
                    </ul>

                </li>
                <?php

                if ($_SESSION['usuario']->getRolId() == 1) {
                    echo '<li><a href="?controller=Usuario&action=show">Gestion Usuario</a></li>';
                }

                ?>
            </ul>
    </nav>
</header>