<?php
// SEGUNDA PRUEBA de migracion Produccion -> Desarrollo.
// A diferencia de la primera ronda, esta vez se exige paridad TOTAL con
// Produccion: mismo ID y misma Descripcion exacta en TODOS los catalogos
// derivados de Produccion (usuario, proyecto, contratista, destino, rubro,
// material), no solo en los movimientos.
//
// Estrategia: dado que ahora se exige igualdad total, ya no tiene sentido
// el enfoque de "emparejar por texto normalizado y mapear IDs" de la
// primera ronda -- se reconstruyen estas tablas completamente desde cero,
// copiando ID y texto exactos de Produccion. Lo unico que NO se toca es la
// estructura propia de Desarrollo sin equivalente en Produccion: rol,
// permiso, rol_permiso, almacen, tipo_ubicacion, notificacion_destinatario,
// proveedor (regla original: nunca retroceder el modelo actual).
//
// Requiere: berdez_prod_real_v2 (dump verbatim, con el mojibake ya
// corregido con mb_convert_encoding, cargado en el mismo servidor).

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
    // ---------------------------------------------------------------
    // FASE 0: hashes de clave reales, capturados de `usuario` ANTES de
    // truncar nada (ver docs/plan-ejecucion-final.md) -- fijos aqui porque
    // un intento anterior de esta misma corrida ya trunco `usuario`
    // (TRUNCATE hace commit implicito) y volver a leerlos de la tabla en
    // vivo ya no es posible tras un reintento.
    // ---------------------------------------------------------------
    $hashPorIdent = [
        1004035010 => '$2y$10$TOQQ.Y0fPY8xNmHXmw.DmuOB3DKyQRzovRj2hIOtyc.Lv4qDND9qS', // Juan
        83235047   => '$2y$10$P8.cW9Rus2gc30bNNdq93uQ1I06NGt.EzXvcSVJVd52zHsIUCruby', // Reinaldo
        1070605738 => '$2y$10$ADX.9MoHC0HnzwMMNGnkqeHsdzD1GvgiZnxAxUsfRvouRhXlynDCa', // Caterine
    ];
    step("Fase 0: hashes de clave fijados para " . count($hashPorIdent) . " usuarios.");

    // ---------------------------------------------------------------
    // FASE 1: truncar todas las tablas derivadas de Produccion
    // (orden: hijos antes que padres; TRUNCATE hace commit implicito,
    // por eso el backup ya se tomo ANTES de correr este script)
    // ---------------------------------------------------------------
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

    // A partir de aqui, todo INSERT queda dentro de una transaccion real
    // (ya no hay mas TRUNCATE de por medio).
    $pdo->beginTransaction();

    // ---------------------------------------------------------------
    // FASE 2: usuario -- mismo ID, Identificacion/Nombre/Apellido/RolID
    // exactos de Produccion; Clave = hash ya existente (nunca se
    // sobreescribe con el texto plano de Produccion)
    // ---------------------------------------------------------------
    $insUsuario = $pdo->prepare("INSERT INTO inventario.usuario (ID, Identificacion, Nombre, Apellido, Clave, RolID) VALUES (?, ?, ?, ?, ?, ?)");
    $nUsuarios = 0;
    foreach ($pdo->query("SELECT ID, Identificacion, Nombre, Apellido, RolID FROM berdez_prod_real_v2.usuario ORDER BY ID") as $r) {
        $ident = (int)$r['Identificacion'];
        if (!isset($hashPorIdent[$ident])) {
            throw new Exception("No hay hash capturado para el usuario real Identificacion=$ident");
        }
        $insUsuario->execute([(int)$r['ID'], $ident, $r['Nombre'], $r['Apellido'], $hashPorIdent[$ident], (int)$r['RolID']]);
        $nUsuarios++;
    }
    step("Fase 2: $nUsuarios usuarios reinsertados con ID/datos exactos de Produccion y su hash de clave preservado.");

    // ---------------------------------------------------------------
    // FASE 3: proyecto, contratista, destino, rubro, material -- copia
    // exacta de ID y texto
    // ---------------------------------------------------------------
    $insProyecto = $pdo->prepare("INSERT INTO inventario.proyecto (ID, Descripcion) VALUES (?, ?)");
    $nProyecto = 0;
    foreach ($pdo->query("SELECT ID, Descripcion FROM berdez_prod_real_v2.proyecto ORDER BY ID") as $r) {
        $insProyecto->execute([(int)$r['ID'], $r['Descripcion']]);
        $nProyecto++;
    }

    $insContratista = $pdo->prepare("INSERT INTO inventario.contratista (ID, Descripcion) VALUES (?, ?)");
    $nContratista = 0;
    foreach ($pdo->query("SELECT ID, Descripcion FROM berdez_prod_real_v2.contratista ORDER BY ID") as $r) {
        $insContratista->execute([(int)$r['ID'], $r['Descripcion']]);
        $nContratista++;
    }

    $insDestino = $pdo->prepare("INSERT INTO inventario.destino (ID, Descripcion) VALUES (?, ?)");
    $nDestino = 0;
    foreach ($pdo->query("SELECT ID, Descripcion FROM berdez_prod_real_v2.destino ORDER BY ID") as $r) {
        $insDestino->execute([(int)$r['ID'], $r['Descripcion']]);
        $nDestino++;
    }

    $insRubro = $pdo->prepare("INSERT INTO inventario.rubro (ID, Descripcion) VALUES (?, ?)");
    $nRubro = 0;
    foreach ($pdo->query("SELECT ID, Descripcion FROM berdez_prod_real_v2.rubro ORDER BY ID") as $r) {
        $insRubro->execute([(int)$r['ID'], $r['Descripcion']]);
        $nRubro++;
    }

    $insMaterial = $pdo->prepare("INSERT INTO inventario.material (ID, Codigo, Descripcion, Unidad, Saldo, Min_Almacen, Max_Casa, CostoPromedio) VALUES (?, ?, ?, ?, ?, ?, ?, NULL)");
    $nMaterial = 0;
    foreach ($pdo->query("SELECT ID, Codigo, Descripcion, Unidad, Saldo, Min_Almacen, Max_Casa FROM berdez_prod_real_v2.material ORDER BY ID") as $r) {
        $insMaterial->execute([(int)$r['ID'], $r['Codigo'], $r['Descripcion'], $r['Unidad'], $r['Saldo'], $r['Min_Almacen'], $r['Max_Casa']]);
        $nMaterial++;
    }
    step("Fase 3: proyecto=$nProyecto, contratista=$nContratista, destino=$nDestino, rubro=$nRubro, material=$nMaterial reinsertados con ID y texto exactos de Produccion.");

    // ---------------------------------------------------------------
    // FASE 4: material_almacen (un solo almacen activo, ID=1)
    // ---------------------------------------------------------------
    // material_almacen.Saldo es NOT NULL; dos materiales de Produccion
    // (Yee sanitario 3"x2", Buje sanitario 2"x1"1/2) tienen Saldo=NULL en
    // `material` (nunca se les registro una entrada real) -- se traduce a
    // 0 solo en material_almacen, sin alterar el NULL real de `material`.
    $insAlmacen = $pdo->prepare("INSERT INTO inventario.material_almacen (MaterialID, AlmacenID, Saldo) VALUES (?, 1, ?)");
    $nAlmacen = 0;
    foreach ($pdo->query("SELECT ID, Saldo FROM inventario.material") as $r) {
        $insAlmacen->execute([(int)$r['ID'], $r['Saldo'] ?? 0]);
        $nAlmacen++;
    }
    step("Fase 4: material_almacen reconstruido para $nAlmacen materiales.");

    // ---------------------------------------------------------------
    // FASE 5: reconstruir ubicacion desde cero (sin equivalente en
    // Produccion, asi que su propio ID no se fuerza -- solo importa la
    // jerarquia). Regla de prioridad confirmada por el usuario: Traspaso
    // manda si aparece en Etapa/Manzana/Casa; si no aparece Traspaso pero
    // aparece Urbanismo, va a un nodo Urbanismo; si no aparece ninguno,
    // jerarquia real Etapa->Manzana->Casa.
    // ---------------------------------------------------------------
    $combos = $pdo->query("
        SELECT rs.ProyectoID AS proyectoId, a.Descripcion AS etapa, m.Descripcion AS manzana, c.Descripcion AS casa
        FROM berdez_prod_real_v2.material_registro_salidas mrs
        JOIN berdez_prod_real_v2.registro_salidas rs ON rs.ID = mrs.Registro_SalidasID
        JOIN berdez_prod_real_v2.area a ON a.ID = mrs.AreaID
        JOIN berdez_prod_real_v2.manzana m ON m.ID = mrs.ManzanaID
        JOIN berdez_prod_real_v2.casa c ON c.ID = mrs.CasaID
        GROUP BY rs.ProyectoID, a.ID, m.ID, c.ID
    ")->fetchAll(PDO::FETCH_ASSOC);

    $insEtapa = $pdo->prepare("INSERT INTO inventario.ubicacion (ProyectoID, PadreID, Nombre, Tipo, Nivel, Activo) VALUES (?, NULL, ?, ?, 0, 1)");
    $insHijo = $pdo->prepare("INSERT INTO inventario.ubicacion (ProyectoID, PadreID, Nombre, Tipo, Nivel, Activo) VALUES (?, ?, ?, ?, ?, 1)");

    $raiz = []; // "proyectoId|nombreLower" -> ID  (Etapa, Traspaso o Urbanismo, todos Nivel 0)
    $manzanaCache = []; // "proyectoId|etapaId|nombreLower" -> ID
    $casaCache = []; // "proyectoId|manzanaId|nombreLower" -> ID

    foreach ($combos as $c) {
        $pid = (int)$c['proyectoId'];
        $etapa = $c['etapa']; $manzana = $c['manzana']; $casa = $c['casa'];
        $esTraspaso = ($etapa === 'Traspaso' || $manzana === 'Traspaso' || $casa === 'Traspaso');
        $esUrbanismo = (!$esTraspaso) && ($etapa === 'Urbanismo' || $manzana === 'Urbanismo' || $casa === 'Urbanismo');

        if ($esTraspaso) {
            $key = $pid . '|traspaso';
            if (!isset($raiz[$key])) {
                $insEtapa->execute([$pid, 'Traspaso', 'TRASPASO']);
                $raiz[$key] = (int)$pdo->lastInsertId();
            }
            continue; // el nodo Traspaso es plano, sin manzana/casa debajo
        }
        if ($esUrbanismo) {
            $key = $pid . '|urbanismo';
            if (!isset($raiz[$key])) {
                $insEtapa->execute([$pid, 'Urbanismo', 'URBANISMO']);
                $raiz[$key] = (int)$pdo->lastInsertId();
            }
            continue; // el nodo Urbanismo tambien es plano
        }

        // Jerarquia real Etapa -> Manzana -> Casa
        $keyEtapa = $pid . '|' . strtolower($etapa);
        if (!isset($raiz[$keyEtapa])) {
            $insEtapa->execute([$pid, $etapa, 'ETAPA']);
            $raiz[$keyEtapa] = (int)$pdo->lastInsertId();
        }
        $etapaId = $raiz[$keyEtapa];

        $keyManzana = $pid . '|' . $etapaId . '|' . strtolower($manzana);
        if (!isset($manzanaCache[$keyManzana])) {
            $insHijo->execute([$pid, $etapaId, $manzana, 'MANZANA', 1]);
            $manzanaCache[$keyManzana] = (int)$pdo->lastInsertId();
        }
        $manzanaId = $manzanaCache[$keyManzana];

        $keyCasa = $pid . '|' . $manzanaId . '|' . strtolower($casa);
        if (!isset($casaCache[$keyCasa])) {
            $insHijo->execute([$pid, $manzanaId, $casa, 'CASA', 2]);
            $casaCache[$keyCasa] = (int)$pdo->lastInsertId();
        }
    }
    $nUbic = (int) $pdo->query("SELECT COUNT(*) FROM inventario.ubicacion")->fetchColumn();
    step("Fase 5: arbol de ubicacion reconstruido desde cero, $nUbic nodos.");

    // ---------------------------------------------------------------
    // FASE 6: registro_entradas / material_registro_entradas -- ID y FK
    // identicos a Produccion (MaterialID/DestinoID ya no necesitan mapeo:
    // los IDs de material y destino ahora son literalmente los mismos)
    // ---------------------------------------------------------------
    $insEntradaHead = $pdo->prepare("INSERT INTO inventario.registro_entradas (ID, Fecha, Hora, UsuarioID, ProveedorID) VALUES (?, ?, ?, ?, NULL)");
    $insEntradaLinea = $pdo->prepare("INSERT INTO inventario.material_registro_entradas (ID, MaterialID, Registro_EntradasID, Cantidad, DestinoID, CostoUnitario) VALUES (?, ?, ?, ?, ?, NULL)");

    $nEntradas = 0;
    foreach ($pdo->query("SELECT ID, Fecha, Hora, UsuarioID FROM berdez_prod_real_v2.registro_entradas ORDER BY ID") as $r) {
        $insEntradaHead->execute([(int)$r['ID'], $r['Fecha'], $r['Hora'], (int)$r['UsuarioID']]);
        $nEntradas++;
    }
    $nLineasEntrada = 0;
    foreach ($pdo->query("SELECT ID, MaterialID, Registro_EntradasID, Cantidad, DestinoID FROM berdez_prod_real_v2.material_registro_entradas ORDER BY ID") as $r) {
        $insEntradaLinea->execute([(int)$r['ID'], (int)$r['MaterialID'], (int)$r['Registro_EntradasID'], $r['Cantidad'], (int)$r['DestinoID']]);
        $nLineasEntrada++;
    }
    step("Fase 6: $nEntradas encabezados y $nLineasEntrada lineas de entrada, ID y FK identicos a Produccion.");

    // ---------------------------------------------------------------
    // FASE 7: registro_salidas / material_registro_salidas -- idem,
    // resolviendo UbicacionID con el arbol recien construido
    // ---------------------------------------------------------------
    $insSalidaHead = $pdo->prepare("INSERT INTO inventario.registro_salidas (ID, Fecha, Hora, UsuarioID, ContratistaID, ProyectoID) VALUES (?, ?, ?, ?, ?, ?)");
    $insSalidaLinea = $pdo->prepare("INSERT INTO inventario.material_registro_salidas
        (ID, MaterialID, Registro_SalidasID, Cantidad, CasaID, ManzanaID, DestinoID, AreaID, RubroID, UbicacionID, CostoUnitario)
        VALUES (?, ?, ?, ?, NULL, NULL, ?, NULL, ?, ?, NULL)");

    $proyectoPorEncabezado = [];
    $nSalidas = 0;
    foreach ($pdo->query("SELECT ID, Fecha, Hora, UsuarioID, ContratistaID, ProyectoID FROM berdez_prod_real_v2.registro_salidas ORDER BY ID") as $r) {
        $insSalidaHead->execute([(int)$r['ID'], $r['Fecha'], $r['Hora'], (int)$r['UsuarioID'], (int)$r['ContratistaID'], (int)$r['ProyectoID']]);
        $proyectoPorEncabezado[(int)$r['ID']] = (int)$r['ProyectoID'];
        $nSalidas++;
    }

    $prodArea = []; foreach ($pdo->query("SELECT ID, Descripcion FROM berdez_prod_real_v2.area") as $r) $prodArea[(int)$r['ID']] = $r['Descripcion'];
    $prodManzana = []; foreach ($pdo->query("SELECT ID, Descripcion FROM berdez_prod_real_v2.manzana") as $r) $prodManzana[(int)$r['ID']] = $r['Descripcion'];
    $prodCasa = []; foreach ($pdo->query("SELECT ID, Descripcion FROM berdez_prod_real_v2.casa") as $r) $prodCasa[(int)$r['ID']] = $r['Descripcion'];

    $nLineasSalida = 0;
    foreach ($pdo->query("SELECT ID, MaterialID, Registro_SalidasID, Cantidad, CasaID, ManzanaID, DestinoID, AreaID, RubroID FROM berdez_prod_real_v2.material_registro_salidas ORDER BY ID") as $r) {
        $pid = $proyectoPorEncabezado[(int)$r['Registro_SalidasID']];
        $etapa = $prodArea[(int)$r['AreaID']];
        $manzana = $prodManzana[(int)$r['ManzanaID']];
        $casa = $prodCasa[(int)$r['CasaID']];

        $esTraspaso = ($etapa === 'Traspaso' || $manzana === 'Traspaso' || $casa === 'Traspaso');
        $esUrbanismo = (!$esTraspaso) && ($etapa === 'Urbanismo' || $manzana === 'Urbanismo' || $casa === 'Urbanismo');

        if ($esTraspaso) {
            $ubicacionId = $raiz[$pid . '|traspaso'] ?? null;
        } elseif ($esUrbanismo) {
            $ubicacionId = $raiz[$pid . '|urbanismo'] ?? null;
        } else {
            $etapaId = $raiz[$pid . '|' . strtolower($etapa)] ?? null;
            $manzanaId = $etapaId !== null ? ($manzanaCache[$pid . '|' . $etapaId . '|' . strtolower($manzana)] ?? null) : null;
            $ubicacionId = $manzanaId !== null ? ($casaCache[$pid . '|' . $manzanaId . '|' . strtolower($casa)] ?? null) : null;
        }
        if ($ubicacionId === null) {
            throw new Exception("No se pudo resolver UbicacionID para linea prod_id={$r['ID']} (Registro_SalidasID={$r['Registro_SalidasID']}), proyecto=$pid, etapa=$etapa, manzana=$manzana, casa=$casa");
        }

        $insSalidaLinea->execute([
            (int)$r['ID'], (int)$r['MaterialID'], (int)$r['Registro_SalidasID'], $r['Cantidad'],
            (int)$r['DestinoID'], (int)$r['RubroID'], $ubicacionId,
        ]);
        $nLineasSalida++;
    }
    step("Fase 7: $nSalidas encabezados y $nLineasSalida lineas de salida, ID y FK identicos a Produccion, UbicacionID resuelto al 100%.");

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
        ['material', 291, 1, 291],
        ['registro_entradas', 79, 1, 79],
        ['material_registro_entradas', 268, 1, 268],
        ['registro_salidas', 70, 1, 70],
        ['material_registro_salidas', 538, 1, 538],
    ] as [$tabla, $esperadoC, $esperadoMin, $esperadoMax]) {
        $c = (int) $pdo->query("SELECT COUNT(*) FROM inventario.`$tabla`")->fetchColumn();
        $min = (int) $pdo->query("SELECT MIN(ID) FROM inventario.`$tabla`")->fetchColumn();
        $max = (int) $pdo->query("SELECT MAX(ID) FROM inventario.`$tabla`")->fetchColumn();
        if ($c !== $esperadoC) $fails[] = "$tabla: count=$c (esperado $esperadoC)";
        if ($min !== $esperadoMin || $max !== $esperadoMax) $fails[] = "$tabla: rango ID $min-$max (esperado $esperadoMin-$esperadoMax)";
    }

    // Texto exacto: 0 diferencias de Descripcion contra Produccion, para cada catalogo
    foreach (['proyecto', 'contratista', 'destino', 'rubro'] as $tabla) {
        $dif = (int) $pdo->query("
            SELECT COUNT(*) FROM inventario.`$tabla` d
            JOIN berdez_prod_real_v2.`$tabla` p ON p.ID = d.ID
            WHERE d.Descripcion <> p.Descripcion")->fetchColumn();
        if ($dif !== 0) $fails[] = "$tabla: $dif filas con Descripcion distinta a Produccion";
    }
    $difMaterial = (int) $pdo->query("
        SELECT COUNT(*) FROM inventario.material d
        JOIN berdez_prod_real_v2.material p ON p.ID = d.ID
        WHERE d.Descripcion <> p.Descripcion OR d.Codigo <> p.Codigo OR d.Saldo <> p.Saldo
           OR d.Min_Almacen <> p.Min_Almacen OR d.Max_Casa <> p.Max_Casa OR d.Unidad <> p.Unidad")->fetchColumn();
    if ($difMaterial !== 0) $fails[] = "material: $difMaterial filas con datos distintos a Produccion";

    // material_almacen sincronizado
    $desalmacen = (int) $pdo->query("
        SELECT COUNT(*) FROM inventario.material m
        JOIN inventario.material_almacen ma ON ma.MaterialID=m.ID AND ma.AlmacenID=1
        WHERE ma.Saldo <> m.Saldo")->fetchColumn();
    if ($desalmacen !== 0) $fails[] = "material_almacen desincronizado: $desalmacen";

    // Sin lineas de salida sin ubicacion
    $sinUbic = (int) $pdo->query("SELECT COUNT(*) FROM inventario.material_registro_salidas WHERE UbicacionID IS NULL")->fetchColumn();
    if ($sinUbic !== 0) $fails[] = "lineas de salida sin UbicacionID: $sinUbic";

    // Integridad referencial completa
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

    // rol_permiso intacto (35 filas, nunca tocado)
    $rp = (int) $pdo->query("SELECT COUNT(*) FROM rol_permiso")->fetchColumn();
    if ($rp !== 35) $fails[] = "rol_permiso = $rp (esperado 35, no deberia haberse tocado)";

    if (!empty($fails)) {
        throw new Exception("Validaciones fallidas:\n - " . implode("\n - ", $fails));
    }

    step("TODAS LAS VALIDACIONES PASARON.");
    $pdo->commit();
    step("COMMIT realizado. Reconstruccion de paridad total aplicada.");

} catch (Throwable $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
        step("ROLLBACK realizado sobre la fase de INSERTs.");
    }
    $pdo->exec("SET FOREIGN_KEY_CHECKS=1");
    step("ERROR: " . $e->getMessage());
    step("Si el TRUNCATE de la Fase 1 ya se ejecuto, inventario puede haber quedado con tablas vacias -- restaurar desde el backup tomado antes de este script.");
    fwrite(STDERR, "\n=== FALLO ===\n");
    exit(1);
}

echo "\n=== RESUMEN ===\n" . implode("\n", $log) . "\n";
