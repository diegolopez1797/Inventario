<?php
// TERCERA PRUEBA de migracion Produccion -> Desarrollo (dump 08-09-2026).
// Igual estrategia que reconstruir_paridad_total.php (paridad total de ID y
// texto exacto en todos los catalogos), pero con la regla de `ubicacion`
// corregida segun lo indicado por el usuario:
//
//   RAIZ:  si Etapa == "Traspaso"          -> raiz = "Traspaso"
//          si no                          -> raiz = la Etapa real (ej "ETAPA 6")
//
//   HIJO bajo esa raiz:
//          si Manzana == "Urbanismo" O Casa == "Urbanismo" -> hijo = "Urbanismo"
//          (Urbanismo predomina aunque el otro campo diga "Traspaso")
//          si no, y la raiz es "Traspaso"  -> hijo = valor real de Casa (ej "Casa 6")
//          si no                            -> jerarquia real Etapa->Manzana->Casa

$pdo = new PDO('mysql:host=127.0.0.1;dbname=inventario;charset=utf8mb4', 'root', '', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
]);

$log = [];
function step($msg) {
    global $log;
    $log[] = $msg;
    fwrite(STDERR, $msg . "\n");
}

try {
    // Hashes de clave capturados de `inventario.usuario` antes de truncar
    // (ver docs/plan-ejecucion-ronda2-paridad-total.md) -- se mantienen
    // fijos aqui, no cambiaron entre rondas.
    $hashPorIdent = [
        1004035010 => '$2y$10$TOQQ.Y0fPY8xNmHXmw.DmuOB3DKyQRzovRj2hIOtyc.Lv4qDND9qS', // Juan
        83235047   => '$2y$10$P8.cW9Rus2gc30bNNdq93uQ1I06NGt.EzXvcSVJVd52zHsIUCruby', // Reinaldo
        1070605738 => '$2y$10$ADX.9MoHC0HnzwMMNGnkqeHsdzD1GvgiZnxAxUsfRvouRhXlynDCa', // Caterine
    ];
    step("Fase 0: hashes de clave fijados para " . count($hashPorIdent) . " usuarios.");

    $pdo->exec("SET FOREIGN_KEY_CHECKS=0");
    foreach ([
        'material_registro_salidas', 'material_registro_entradas',
        'material_almacen', 'ubicacion',
        'registro_salidas', 'registro_entradas',
        'material', 'rubro', 'destino', 'contratista', 'proyecto', 'usuario',
    ] as $tabla) {
        $pdo->exec("TRUNCATE TABLE inventario.`$tabla`");
    }
    $pdo->exec("SET FOREIGN_KEY_CHECKS=1");
    step("Fase 1: tablas derivadas de Produccion truncadas.");

    $pdo->beginTransaction();

    // usuario
    $insUsuario = $pdo->prepare("INSERT INTO inventario.usuario (ID, Identificacion, Nombre, Apellido, Clave, RolID) VALUES (?, ?, ?, ?, ?, ?)");
    $nUsuarios = 0;
    foreach ($pdo->query("SELECT ID, Identificacion, Nombre, Apellido, RolID FROM berdez_prod_real_v3.usuario ORDER BY ID") as $r) {
        $ident = (int)$r['Identificacion'];
        if (!isset($hashPorIdent[$ident])) throw new Exception("No hay hash capturado para el usuario real Identificacion=$ident");
        $insUsuario->execute([(int)$r['ID'], $ident, $r['Nombre'], $r['Apellido'], $hashPorIdent[$ident], (int)$r['RolID']]);
        $nUsuarios++;
    }

    // proyecto, contratista, destino, rubro, material -- copia exacta de ID y texto
    $insProyecto = $pdo->prepare("INSERT INTO inventario.proyecto (ID, Descripcion) VALUES (?, ?)");
    $nProyecto = 0;
    foreach ($pdo->query("SELECT ID, Descripcion FROM berdez_prod_real_v3.proyecto ORDER BY ID") as $r) { $insProyecto->execute([(int)$r['ID'], $r['Descripcion']]); $nProyecto++; }

    $insContratista = $pdo->prepare("INSERT INTO inventario.contratista (ID, Descripcion) VALUES (?, ?)");
    $nContratista = 0;
    foreach ($pdo->query("SELECT ID, Descripcion FROM berdez_prod_real_v3.contratista ORDER BY ID") as $r) { $insContratista->execute([(int)$r['ID'], $r['Descripcion']]); $nContratista++; }

    $insDestino = $pdo->prepare("INSERT INTO inventario.destino (ID, Descripcion) VALUES (?, ?)");
    $nDestino = 0;
    foreach ($pdo->query("SELECT ID, Descripcion FROM berdez_prod_real_v3.destino ORDER BY ID") as $r) { $insDestino->execute([(int)$r['ID'], $r['Descripcion']]); $nDestino++; }

    $insRubro = $pdo->prepare("INSERT INTO inventario.rubro (ID, Descripcion) VALUES (?, ?)");
    $nRubro = 0;
    foreach ($pdo->query("SELECT ID, Descripcion FROM berdez_prod_real_v3.rubro ORDER BY ID") as $r) { $insRubro->execute([(int)$r['ID'], $r['Descripcion']]); $nRubro++; }

    $insMaterial = $pdo->prepare("INSERT INTO inventario.material (ID, Codigo, Descripcion, Unidad, Saldo, Min_Almacen, Max_Casa, CostoPromedio) VALUES (?, ?, ?, ?, ?, ?, ?, NULL)");
    $nMaterial = 0;
    foreach ($pdo->query("SELECT ID, Codigo, Descripcion, Unidad, Saldo, Min_Almacen, Max_Casa FROM berdez_prod_real_v3.material ORDER BY ID") as $r) {
        $insMaterial->execute([(int)$r['ID'], $r['Codigo'], $r['Descripcion'], $r['Unidad'], $r['Saldo'], $r['Min_Almacen'], $r['Max_Casa']]);
        $nMaterial++;
    }
    step("Fase 2-3: usuario=$nUsuarios, proyecto=$nProyecto, contratista=$nContratista, destino=$nDestino, rubro=$nRubro, material=$nMaterial reinsertados con ID y texto exactos de Produccion.");

    // material_almacen
    $insAlmacen = $pdo->prepare("INSERT INTO inventario.material_almacen (MaterialID, AlmacenID, Saldo) VALUES (?, 1, ?)");
    $nAlmacen = 0;
    foreach ($pdo->query("SELECT ID, Saldo FROM inventario.material") as $r) { $insAlmacen->execute([(int)$r['ID'], $r['Saldo'] ?? 0]); $nAlmacen++; }
    step("Fase 4: material_almacen reconstruido para $nAlmacen materiales.");

    // ---------------------------------------------------------------
    // Fase 5: reconstruir ubicacion con la regla corregida
    // ---------------------------------------------------------------
    $combos = $pdo->query("
        SELECT rs.ProyectoID AS proyectoId, a.Descripcion AS etapa, m.Descripcion AS manzana, c.Descripcion AS casa
        FROM berdez_prod_real_v3.material_registro_salidas mrs
        JOIN berdez_prod_real_v3.registro_salidas rs ON rs.ID = mrs.Registro_SalidasID
        JOIN berdez_prod_real_v3.area a ON a.ID = mrs.AreaID
        JOIN berdez_prod_real_v3.manzana m ON m.ID = mrs.ManzanaID
        JOIN berdez_prod_real_v3.casa c ON c.ID = mrs.CasaID
        GROUP BY rs.ProyectoID, a.ID, m.ID, c.ID
    ")->fetchAll(PDO::FETCH_ASSOC);

    $insRaiz = $pdo->prepare("INSERT INTO inventario.ubicacion (ProyectoID, PadreID, Nombre, Tipo, Nivel, Activo) VALUES (?, NULL, ?, ?, 0, 1)");
    $insHijo = $pdo->prepare("INSERT INTO inventario.ubicacion (ProyectoID, PadreID, Nombre, Tipo, Nivel, Activo) VALUES (?, ?, ?, ?, ?, 1)");

    $raiz = [];          // "proyectoId|nombreLowerRaiz" -> ID (Nivel 0: Etapa real o Traspaso)
    $hijoGenerico = [];  // "proyectoId|raizId|nombreLowerHijo" -> ID (Nivel 1: Urbanismo o Casa bajo una raiz generica/Etapa+Urbanismo)
    $manzanaCache = [];  // "proyectoId|etapaId|nombreLowerManzana" -> ID (jerarquia real, Nivel 1)
    $casaCache = [];     // "proyectoId|manzanaId|nombreLowerCasa" -> ID (jerarquia real, Nivel 2)

    function resolverNodo($pdo, &$raiz, &$hijoGenerico, &$manzanaCache, &$casaCache, $insRaiz, $insHijo, $pid, $etapa, $manzana, $casa) {
        $esTraspasoEtapa = ($etapa === 'Traspaso');
        $raizNombre = $esTraspasoEtapa ? 'Traspaso' : $etapa;
        $raizTipo = $esTraspasoEtapa ? 'TRASPASO' : 'ETAPA';
        $keyRaiz = $pid . '|' . strtolower($raizNombre);
        if (!isset($raiz[$keyRaiz])) {
            $insRaiz->execute([$pid, $raizNombre, $raizTipo]);
            $raiz[$keyRaiz] = (int) $pdo->lastInsertId();
        }
        $raizId = $raiz[$keyRaiz];

        $urbanismoPredomina = ($manzana === 'Urbanismo' || $casa === 'Urbanismo');
        if ($urbanismoPredomina) {
            $keyHijo = $pid . '|' . $raizId . '|urbanismo';
            if (!isset($hijoGenerico[$keyHijo])) {
                $insHijo->execute([$pid, $raizId, 'Urbanismo', 'URBANISMO', 1]);
                $hijoGenerico[$keyHijo] = (int) $pdo->lastInsertId();
            }
            return $hijoGenerico[$keyHijo];
        }

        if ($esTraspasoEtapa) {
            // Ni manzana ni casa son "Urbanismo": el valor real de Casa es el hijo (ej "Casa 6")
            $keyHijo = $pid . '|' . $raizId . '|' . strtolower($casa);
            if (!isset($hijoGenerico[$keyHijo])) {
                $insHijo->execute([$pid, $raizId, $casa, 'CASA', 1]);
                $hijoGenerico[$keyHijo] = (int) $pdo->lastInsertId();
            }
            return $hijoGenerico[$keyHijo];
        }

        // Jerarquia real: raizId (Etapa) -> Manzana -> Casa
        $keyManzana = $pid . '|' . $raizId . '|' . strtolower($manzana);
        if (!isset($manzanaCache[$keyManzana])) {
            $insHijo->execute([$pid, $raizId, $manzana, 'MANZANA', 1]);
            $manzanaCache[$keyManzana] = (int) $pdo->lastInsertId();
        }
        $manzanaId = $manzanaCache[$keyManzana];

        $keyCasa = $pid . '|' . $manzanaId . '|' . strtolower($casa);
        if (!isset($casaCache[$keyCasa])) {
            $insHijoLocal = $pdo->prepare("INSERT INTO inventario.ubicacion (ProyectoID, PadreID, Nombre, Tipo, Nivel, Activo) VALUES (?, ?, ?, 'CASA', 2, 1)");
            $insHijoLocal->execute([$pid, $manzanaId, $casa]);
            $casaCache[$keyCasa] = (int) $pdo->lastInsertId();
        }
        return $casaCache[$keyCasa];
    }

    foreach ($combos as $c) {
        resolverNodo($pdo, $raiz, $hijoGenerico, $manzanaCache, $casaCache, $insRaiz, $insHijo,
            (int)$c['proyectoId'], $c['etapa'], $c['manzana'], $c['casa']);
    }
    $nUbic = (int) $pdo->query("SELECT COUNT(*) FROM inventario.ubicacion")->fetchColumn();
    step("Fase 5: arbol de ubicacion reconstruido con la regla corregida, $nUbic nodos.");

    // ---------------------------------------------------------------
    // Fase 6: registro_entradas / material_registro_entradas
    // ---------------------------------------------------------------
    $insEntradaHead = $pdo->prepare("INSERT INTO inventario.registro_entradas (ID, Fecha, Hora, UsuarioID, ProveedorID) VALUES (?, ?, ?, ?, NULL)");
    $insEntradaLinea = $pdo->prepare("INSERT INTO inventario.material_registro_entradas (ID, MaterialID, Registro_EntradasID, Cantidad, DestinoID, CostoUnitario) VALUES (?, ?, ?, ?, ?, NULL)");
    $nEntradas = 0;
    foreach ($pdo->query("SELECT ID, Fecha, Hora, UsuarioID FROM berdez_prod_real_v3.registro_entradas ORDER BY ID") as $r) {
        $insEntradaHead->execute([(int)$r['ID'], $r['Fecha'], $r['Hora'], (int)$r['UsuarioID']]);
        $nEntradas++;
    }
    $nLineasEntrada = 0;
    foreach ($pdo->query("SELECT ID, MaterialID, Registro_EntradasID, Cantidad, DestinoID FROM berdez_prod_real_v3.material_registro_entradas ORDER BY ID") as $r) {
        $insEntradaLinea->execute([(int)$r['ID'], (int)$r['MaterialID'], (int)$r['Registro_EntradasID'], $r['Cantidad'], (int)$r['DestinoID']]);
        $nLineasEntrada++;
    }
    step("Fase 6: $nEntradas encabezados y $nLineasEntrada lineas de entrada, ID y FK identicos a Produccion.");

    // ---------------------------------------------------------------
    // Fase 7: registro_salidas / material_registro_salidas
    // ---------------------------------------------------------------
    $insSalidaHead = $pdo->prepare("INSERT INTO inventario.registro_salidas (ID, Fecha, Hora, UsuarioID, ContratistaID, ProyectoID) VALUES (?, ?, ?, ?, ?, ?)");
    $insSalidaLinea = $pdo->prepare("INSERT INTO inventario.material_registro_salidas
        (ID, MaterialID, Registro_SalidasID, Cantidad, CasaID, ManzanaID, DestinoID, AreaID, RubroID, UbicacionID, CostoUnitario)
        VALUES (?, ?, ?, ?, NULL, NULL, ?, NULL, ?, ?, NULL)");

    $proyectoPorEncabezado = [];
    $nSalidas = 0;
    foreach ($pdo->query("SELECT ID, Fecha, Hora, UsuarioID, ContratistaID, ProyectoID FROM berdez_prod_real_v3.registro_salidas ORDER BY ID") as $r) {
        $insSalidaHead->execute([(int)$r['ID'], $r['Fecha'], $r['Hora'], (int)$r['UsuarioID'], (int)$r['ContratistaID'], (int)$r['ProyectoID']]);
        $proyectoPorEncabezado[(int)$r['ID']] = (int)$r['ProyectoID'];
        $nSalidas++;
    }

    $prodArea = []; foreach ($pdo->query("SELECT ID, Descripcion FROM berdez_prod_real_v3.area") as $r) $prodArea[(int)$r['ID']] = $r['Descripcion'];
    $prodManzana = []; foreach ($pdo->query("SELECT ID, Descripcion FROM berdez_prod_real_v3.manzana") as $r) $prodManzana[(int)$r['ID']] = $r['Descripcion'];
    $prodCasa = []; foreach ($pdo->query("SELECT ID, Descripcion FROM berdez_prod_real_v3.casa") as $r) $prodCasa[(int)$r['ID']] = $r['Descripcion'];

    $nLineasSalida = 0;
    foreach ($pdo->query("SELECT ID, MaterialID, Registro_SalidasID, Cantidad, CasaID, ManzanaID, DestinoID, AreaID, RubroID FROM berdez_prod_real_v3.material_registro_salidas ORDER BY ID") as $r) {
        $pid = $proyectoPorEncabezado[(int)$r['Registro_SalidasID']];
        $etapa = $prodArea[(int)$r['AreaID']];
        $manzana = $prodManzana[(int)$r['ManzanaID']];
        $casa = $prodCasa[(int)$r['CasaID']];

        $ubicacionId = resolverNodo($pdo, $raiz, $hijoGenerico, $manzanaCache, $casaCache, $insRaiz, $insHijo, $pid, $etapa, $manzana, $casa);

        $insSalidaLinea->execute([
            (int)$r['ID'], (int)$r['MaterialID'], (int)$r['Registro_SalidasID'], $r['Cantidad'],
            (int)$r['DestinoID'], (int)$r['RubroID'], $ubicacionId,
        ]);
        $nLineasSalida++;
    }
    step("Fase 7: $nSalidas encabezados y $nLineasSalida lineas de salida, ID y FK identicos a Produccion, UbicacionID resuelto al 100% con la regla corregida.");

    // ---------------------------------------------------------------
    // VALIDACIONES
    // ---------------------------------------------------------------
    $fails = [];

    foreach ([
        ['usuario', 3, 1, 3],
        ['proyecto', 4, 1, 4],
        ['contratista', 6, 1, 6],
        ['destino', 9, 1, 9],
        ['rubro', 36, 1, 36],
        ['material', 292, 1, 292],
        ['registro_entradas', 82, 1, 82],
        ['material_registro_entradas', 271, 1, 271],
        ['registro_salidas', 75, 1, 75],
        ['material_registro_salidas', 545, 1, 545],
    ] as [$tabla, $esperadoC, $esperadoMin, $esperadoMax]) {
        $c = (int) $pdo->query("SELECT COUNT(*) FROM inventario.`$tabla`")->fetchColumn();
        $min = (int) $pdo->query("SELECT MIN(ID) FROM inventario.`$tabla`")->fetchColumn();
        $max = (int) $pdo->query("SELECT MAX(ID) FROM inventario.`$tabla`")->fetchColumn();
        if ($c !== $esperadoC) $fails[] = "$tabla: count=$c (esperado $esperadoC)";
        if ($min !== $esperadoMin || $max !== $esperadoMax) $fails[] = "$tabla: rango ID $min-$max (esperado $esperadoMin-$esperadoMax)";
    }

    foreach (['proyecto', 'contratista', 'destino', 'rubro'] as $tabla) {
        $dif = (int) $pdo->query("SELECT COUNT(*) FROM inventario.`$tabla` d JOIN berdez_prod_real_v3.`$tabla` p ON p.ID = d.ID WHERE d.Descripcion <> p.Descripcion")->fetchColumn();
        if ($dif !== 0) $fails[] = "$tabla: $dif filas con Descripcion distinta a Produccion";
    }
    $difMaterial = (int) $pdo->query("
        SELECT COUNT(*) FROM inventario.material d JOIN berdez_prod_real_v3.material p ON p.ID = d.ID
        WHERE d.Descripcion <> p.Descripcion OR d.Codigo <> p.Codigo OR d.Saldo <> p.Saldo
           OR d.Min_Almacen <> p.Min_Almacen OR d.Max_Casa <> p.Max_Casa OR d.Unidad <> p.Unidad")->fetchColumn();
    if ($difMaterial !== 0) $fails[] = "material: $difMaterial filas con datos distintos a Produccion";

    $desalmacen = (int) $pdo->query("SELECT COUNT(*) FROM inventario.material m JOIN inventario.material_almacen ma ON ma.MaterialID=m.ID AND ma.AlmacenID=1 WHERE ma.Saldo <> m.Saldo")->fetchColumn();
    if ($desalmacen !== 0) $fails[] = "material_almacen desincronizado: $desalmacen";

    $sinUbic = (int) $pdo->query("SELECT COUNT(*) FROM inventario.material_registro_salidas WHERE UbicacionID IS NULL")->fetchColumn();
    if ($sinUbic !== 0) $fails[] = "lineas de salida sin UbicacionID: $sinUbic";

    $checks = [
        "entradas.MaterialID" => "SELECT COUNT(*) FROM material_registro_entradas x LEFT JOIN material y ON y.ID=x.MaterialID WHERE y.ID IS NULL",
        "entradas.DestinoID" => "SELECT COUNT(*) FROM material_registro_entradas x LEFT JOIN destino y ON y.ID=x.DestinoID WHERE y.ID IS NULL",
        "entradas.Registro_EntradasID" => "SELECT COUNT(*) FROM material_registro_entradas x LEFT JOIN registro_entradas y ON y.ID=x.Registro_EntradasID WHERE y.ID IS NULL",
        "entradas.UsuarioID" => "SELECT COUNT(*) FROM registro_entradas x LEFT JOIN usuario y ON y.ID=x.UsuarioID WHERE y.ID IS NULL",
        "salidas.MaterialID" => "SELECT COUNT(*) FROM material_registro_salidas x LEFT JOIN material y ON y.ID=x.MaterialID WHERE y.ID IS NULL",
        "salidas.DestinoID" => "SELECT COUNT(*) FROM material_registro_salidas x LEFT JOIN destino y ON y.ID=x.DestinoID WHERE y.ID IS NULL",
        "salidas.RubroID" => "SELECT COUNT(*) FROM material_registro_salidas x LEFT JOIN rubro y ON y.ID=x.RubroID WHERE y.ID IS NULL",
        "salidas.UbicacionID" => "SELECT COUNT(*) FROM material_registro_salidas x LEFT JOIN ubicacion y ON y.ID=x.UbicacionID WHERE y.ID IS NULL",
        "salidas.Registro_SalidasID" => "SELECT COUNT(*) FROM material_registro_salidas x LEFT JOIN registro_salidas y ON y.ID=x.Registro_SalidasID WHERE y.ID IS NULL",
        "salidas.UsuarioID" => "SELECT COUNT(*) FROM registro_salidas x LEFT JOIN usuario y ON y.ID=x.UsuarioID WHERE y.ID IS NULL",
        "salidas.ContratistaID" => "SELECT COUNT(*) FROM registro_salidas x LEFT JOIN contratista y ON y.ID=x.ContratistaID WHERE y.ID IS NULL",
        "salidas.ProyectoID" => "SELECT COUNT(*) FROM registro_salidas x LEFT JOIN proyecto y ON y.ID=x.ProyectoID WHERE y.ID IS NULL",
    ];
    foreach ($checks as $nombre => $sql) {
        $c = (int) $pdo->query($sql)->fetchColumn();
        if ($c !== 0) $fails[] = "FK huerfana $nombre: $c";
    }

    $rp = (int) $pdo->query("SELECT COUNT(*) FROM rol_permiso")->fetchColumn();
    if ($rp !== 35) $fails[] = "rol_permiso = $rp (esperado 35, no deberia haberse tocado)";

    // Validaciones especificas de la nueva regla de ubicacion
    $verifNuevaRegla = [
        // [ProyectoID, Registro_SalidasID de ejemplo, nombre esperado de la raiz, nombre esperado del hijo directo]
    ];
    // Ejemplo puntual: la salida con Etapa=ETAPA6/Manzana=Urbanismo/Casa=Urbanismo debe colgar de un nodo raiz "ETAPA 6" (Nivel 0), con hijo "Urbanismo" (Nivel 1)
    $chk1 = $pdo->query("
        SELECT raiz.Nombre raizNombre, raiz.Nivel raizNivel, hijo.Nombre hijoNombre, hijo.Nivel hijoNivel
        FROM inventario.material_registro_salidas mrs
        JOIN inventario.ubicacion hijo ON hijo.ID = mrs.UbicacionID
        JOIN inventario.ubicacion raiz ON raiz.ID = hijo.PadreID
        JOIN berdez_prod_real_v3.material_registro_salidas p ON p.ID = mrs.ID
        JOIN berdez_prod_real_v3.area a ON a.ID = p.AreaID
        JOIN berdez_prod_real_v3.manzana m ON m.ID = p.ManzanaID
        JOIN berdez_prod_real_v3.casa c ON c.ID = p.CasaID
        WHERE a.Descripcion NOT IN ('Traspaso') AND (m.Descripcion='Urbanismo' OR c.Descripcion='Urbanismo')
        LIMIT 5
    ")->fetchAll(PDO::FETCH_ASSOC);
    foreach ($chk1 as $row) {
        if ($row['raizNivel'] != 0 || strtolower($row['raizNombre']) === 'urbanismo' || $row['hijoNombre'] !== 'Urbanismo') {
            $fails[] = "Regla Etapa+Urbanismo incumplida: raiz={$row['raizNombre']} hijo={$row['hijoNombre']}";
        }
    }

    if (!empty($fails)) {
        throw new Exception("Validaciones fallidas:\n - " . implode("\n - ", $fails));
    }

    step("TODAS LAS VALIDACIONES PASARON.");
    $pdo->commit();
    step("COMMIT realizado. Reconstruccion de paridad total (ronda 3) aplicada.");

} catch (Throwable $e) {
    if ($pdo->inTransaction()) { $pdo->rollBack(); step("ROLLBACK realizado sobre la fase de INSERTs."); }
    $pdo->exec("SET FOREIGN_KEY_CHECKS=1");
    step("ERROR: " . $e->getMessage());
    step("Si el TRUNCATE de la Fase 1 ya se ejecuto, inventario puede haber quedado con tablas vacias -- restaurar desde el backup tomado antes de este script.");
    fwrite(STDERR, "\n=== FALLO ===\n");
    exit(1);
}

echo "\n=== RESUMEN ===\n" . implode("\n", $log) . "\n";
