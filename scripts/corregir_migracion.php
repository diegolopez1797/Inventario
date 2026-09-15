<?php
// Corrige dos problemas detectados por el usuario despues de la migracion
// aplicada en scripts/ejecutar_migracion.php:
//
// 1. Las lineas de salida cuyo Etapa/Manzana/Casa de Produccion era
//    "Urbanismo" (sin "Traspaso" en ninguna de las tres) habian quedado
//    asignadas al nodo "Traspaso" en vez de a un nodo "Urbanismo" propio.
//    Regla correcta: si aparece "Traspaso" en cualquiera de las tres,
//    manda Traspaso; si no aparece Traspaso pero aparece "Urbanismo",
//    manda Urbanismo; si no aparece ninguno, es la jerarquia real.
//
// 2. registro_entradas/registro_salidas (y sus lineas) quedaron con IDs
//    that no coinciden con Produccion (continuaron el AUTO_INCREMENT viejo
//    de los datos de prueba ya borrados), rompiendo el "Salida No." /
//    "Entrada No." que la aplicacion muestra al usuario. Se corrige
//    truncando esas 4 tablas y reinsertando con el mismo ID que tienen en
//    Produccion.
//
// Corre dentro de una unica transaccion.

$pdo = new PDO('mysql:host=127.0.0.1;dbname=inventario;charset=utf8mb4', 'root', '', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
]);

$log = [];
function step($msg) {
    global $log;
    $log[] = $msg;
    fwrite(STDERR, $msg . "\n");
}

function normDesc($s) {
    return mb_strtolower(trim(str_replace('"', '', $s)), 'UTF-8');
}

$pdo->beginTransaction();

try {
    // ---------------------------------------------------------------
    // PASO A: agregar nodos raiz "Urbanismo" donde falten (proyectos 1 y 3)
    // ---------------------------------------------------------------
    $proyectosConUrbanismo = [1, 3]; // dev proyecto ID (Salamanca conjunto, Ciudadela)
    foreach ($proyectosConUrbanismo as $pid) {
        $stmt = $pdo->prepare("SELECT ID FROM inventario.ubicacion WHERE ProyectoID=? AND PadreID IS NULL AND Nombre='Urbanismo'");
        $stmt->execute([$pid]);
        if (!$stmt->fetchColumn()) {
            $ins = $pdo->prepare("INSERT INTO inventario.ubicacion (ProyectoID, PadreID, Nombre, Tipo, Nivel, Activo) VALUES (?, NULL, 'Urbanismo', 'URBANISMO', 0, 1)");
            $ins->execute([$pid]);
        }
    }
    step("Paso A: nodos raiz 'Urbanismo' verificados/creados para proyectos 1 y 3.");

    // ---------------------------------------------------------------
    // PASO B: truncar las 4 tablas de movimientos (solo contienen los
    // datos ya migrados, ninguna referencia externa las apunta)
    // ---------------------------------------------------------------
    $pdo->exec("SET FOREIGN_KEY_CHECKS=0");
    $pdo->exec("TRUNCATE TABLE inventario.material_registro_salidas");
    $pdo->exec("TRUNCATE TABLE inventario.registro_salidas");
    $pdo->exec("TRUNCATE TABLE inventario.material_registro_entradas");
    $pdo->exec("TRUNCATE TABLE inventario.registro_entradas");
    $pdo->exec("SET FOREIGN_KEY_CHECKS=1");
    step("Paso B: tablas de movimientos vaciadas para reinsertar con los mismos ID de Produccion.");

    // ---------------------------------------------------------------
    // PASO C: mapas en memoria (idénticos criterio que la migracion original,
    // ahora ya confiables porque Descripcion de material quedo sincronizada)
    // ---------------------------------------------------------------
    $prodUsuarioIdent = [];
    foreach ($pdo->query("SELECT ID, Identificacion FROM berdez_prod_real.usuario") as $r) {
        $prodUsuarioIdent[(int)$r['ID']] = (int)$r['Identificacion'];
    }
    $usuarioMap = [];
    foreach ($pdo->query("SELECT Identificacion, ID FROM inventario.usuario") as $r) {
        $usuarioMap[(int)$r['Identificacion']] = (int)$r['ID'];
    }

    $proyectoOverride = [2 => 'Salamanca la nueva conjunto residencial', 3 => 'Tienda D1'];
    $proyectoMap = [1 => null, 2 => null, 3 => null, 4 => null];
    foreach ($pdo->query("SELECT ID, Descripcion FROM berdez_prod_real.proyecto") as $r) {
        $pid = (int)$r['ID'];
        if ($pid === 4) {
            $devId = $pdo->query("SELECT ID FROM inventario.proyecto WHERE LOWER(TRIM(Descripcion))='alcala'")->fetchColumn();
        } else {
            $target = $proyectoOverride[$pid] ?? $r['Descripcion'];
            $stmt = $pdo->prepare("SELECT ID FROM inventario.proyecto WHERE LOWER(TRIM(Descripcion)) = LOWER(TRIM(?))");
            $stmt->execute([$target]);
            $devId = $stmt->fetchColumn();
        }
        if ($devId === false) throw new Exception("No se pudo mapear proyecto prod_id=$pid");
        $proyectoMap[$pid] = (int)$devId;
    }

    $contratistaOverride = [2 => 'Construcciones hermanos murcia'];
    $contratistaMap = [];
    foreach ($pdo->query("SELECT ID, Descripcion FROM berdez_prod_real.contratista") as $r) {
        $pid = (int)$r['ID'];
        $target = $contratistaOverride[$pid] ?? $r['Descripcion'];
        $stmt = $pdo->prepare("SELECT ID FROM inventario.contratista WHERE LOWER(TRIM(Descripcion)) = LOWER(TRIM(?))");
        $stmt->execute([$target]);
        $devId = $stmt->fetchColumn();
        if ($devId === false) throw new Exception("No se pudo mapear contratista prod_id=$pid");
        $contratistaMap[$pid] = (int)$devId;
    }

    $destinoMap = [];
    foreach ($pdo->query("SELECT ID, Descripcion FROM berdez_prod_real.destino") as $r) {
        $stmt = $pdo->prepare("SELECT ID FROM inventario.destino WHERE LOWER(TRIM(Descripcion)) = LOWER(TRIM(?))");
        $stmt->execute([$r['Descripcion']]);
        $devId = $stmt->fetchColumn();
        if ($devId === false) throw new Exception("No se pudo mapear destino prod_id={$r['ID']}");
        $destinoMap[(int)$r['ID']] = (int)$devId;
    }

    $rubroMap = [];
    foreach ($pdo->query("SELECT ID, Descripcion FROM berdez_prod_real.rubro") as $r) {
        $stmt = $pdo->prepare("SELECT ID FROM inventario.rubro WHERE LOWER(TRIM(Descripcion)) = LOWER(TRIM(?))");
        $stmt->execute([$r['Descripcion']]);
        $devId = $stmt->fetchColumn();
        if ($devId === false) throw new Exception("No se pudo mapear rubro prod_id={$r['ID']}");
        $rubroMap[(int)$r['ID']] = (int)$devId;
    }

    $devMaterialByDesc = [];
    foreach ($pdo->query("SELECT ID, Descripcion FROM inventario.material") as $r) {
        $devMaterialByDesc[normDesc($r['Descripcion'])] = (int)$r['ID'];
    }
    $materialMap = [];
    foreach ($pdo->query("SELECT ID, Descripcion FROM berdez_prod_real.material") as $r) {
        $key = normDesc($r['Descripcion']);
        if (!isset($devMaterialByDesc[$key])) {
            // Respaldo: comparacion via SQL (collation) por si la variante
            // exacta en PHP no calza pero SQL si la reconoce como igual.
            $stmt = $pdo->prepare("SELECT ID FROM inventario.material WHERE LOWER(TRIM(REPLACE(Descripcion,'\"',''))) = LOWER(TRIM(REPLACE(?, '\"','')))");
            $stmt->execute([$r['Descripcion']]);
            $devId = $stmt->fetchColumn();
            if ($devId === false) throw new Exception("No se pudo mapear material prod_id={$r['ID']} ('{$r['Descripcion']}')");
            $materialMap[(int)$r['ID']] = (int)$devId;
        } else {
            $materialMap[(int)$r['ID']] = $devMaterialByDesc[$key];
        }
    }

    $prodArea = []; foreach ($pdo->query("SELECT ID, Descripcion FROM berdez_prod_real.area") as $r) $prodArea[(int)$r['ID']] = $r['Descripcion'];
    $prodManzana = []; foreach ($pdo->query("SELECT ID, Descripcion FROM berdez_prod_real.manzana") as $r) $prodManzana[(int)$r['ID']] = $r['Descripcion'];
    $prodCasa = []; foreach ($pdo->query("SELECT ID, Descripcion FROM berdez_prod_real.casa") as $r) $prodCasa[(int)$r['ID']] = $r['Descripcion'];

    // Nodos raiz Traspaso / Urbanismo por proyecto dev, y hojas Casa reales
    $traspasoNode = []; $urbanismoNode = [];
    foreach ($pdo->query("SELECT ID, ProyectoID, Nombre FROM inventario.ubicacion WHERE PadreID IS NULL AND Nivel=0") as $r) {
        if (strtolower($r['Nombre']) === 'traspaso') $traspasoNode[(int)$r['ProyectoID']] = (int)$r['ID'];
        if (strtolower($r['Nombre']) === 'urbanismo') $urbanismoNode[(int)$r['ProyectoID']] = (int)$r['ID'];
    }
    $allUbic = $pdo->query("SELECT ID, ProyectoID, PadreID, Nombre, Nivel FROM inventario.ubicacion")->fetchAll(PDO::FETCH_ASSOC);
    $byId = [];
    foreach ($allUbic as $r) $byId[(int)$r['ID']] = $r;
    $ubicacionLeaf = [];
    foreach ($allUbic as $r) {
        if ((int)$r['Nivel'] !== 2) continue;
        $manzana = $byId[(int)$r['PadreID']] ?? null;
        if (!$manzana) continue;
        $etapa = $byId[(int)$manzana['PadreID']] ?? null;
        if (!$etapa) continue;
        $key = $r['ProyectoID'] . '|' . strtolower($etapa['Nombre']) . '|' . strtolower($manzana['Nombre']) . '|' . strtolower($r['Nombre']);
        $ubicacionLeaf[$key] = (int)$r['ID'];
    }
    step("Paso C: mapas reconstruidos en memoria.");

    // ---------------------------------------------------------------
    // PASO D: reinsertar registro_entradas / material_registro_entradas
    // con el mismo ID que en Produccion
    // ---------------------------------------------------------------
    $insEntradaHead = $pdo->prepare("INSERT INTO inventario.registro_entradas (ID, Fecha, Hora, UsuarioID, ProveedorID) VALUES (?, ?, ?, ?, NULL)");
    $insEntradaLinea = $pdo->prepare("INSERT INTO inventario.material_registro_entradas (ID, MaterialID, Registro_EntradasID, Cantidad, DestinoID, CostoUnitario) VALUES (?, ?, ?, ?, ?, NULL)");

    $nEntradas = 0; $nLineasEntrada = 0;
    foreach ($pdo->query("SELECT ID, Fecha, Hora, UsuarioID FROM berdez_prod_real.registro_entradas ORDER BY ID") as $enc) {
        $devUsuarioId = $usuarioMap[$prodUsuarioIdent[(int)$enc['UsuarioID']]];
        $insEntradaHead->execute([(int)$enc['ID'], $enc['Fecha'], $enc['Hora'], $devUsuarioId]);
        $nEntradas++;
    }
    foreach ($pdo->query("SELECT ID, MaterialID, Registro_EntradasID, Cantidad, DestinoID FROM berdez_prod_real.material_registro_entradas ORDER BY ID") as $lin) {
        $insEntradaLinea->execute([
            (int)$lin['ID'],
            $materialMap[(int)$lin['MaterialID']],
            (int)$lin['Registro_EntradasID'],
            $lin['Cantidad'],
            $destinoMap[(int)$lin['DestinoID']],
        ]);
        $nLineasEntrada++;
    }
    step("Paso D: $nEntradas encabezados y $nLineasEntrada lineas de entrada reinsertados con ID identico a Produccion.");

    // ---------------------------------------------------------------
    // PASO E: reinsertar registro_salidas / material_registro_salidas
    // con el mismo ID que en Produccion y UbicacionID corregido
    // ---------------------------------------------------------------
    $insSalidaHead = $pdo->prepare("INSERT INTO inventario.registro_salidas (ID, Fecha, Hora, UsuarioID, ContratistaID, ProyectoID) VALUES (?, ?, ?, ?, ?, ?)");
    $insSalidaLinea = $pdo->prepare("INSERT INTO inventario.material_registro_salidas
        (ID, MaterialID, Registro_SalidasID, Cantidad, CasaID, ManzanaID, DestinoID, AreaID, RubroID, UbicacionID, CostoUnitario)
        VALUES (?, ?, ?, ?, NULL, NULL, ?, NULL, ?, ?, NULL)");

    $devProyectoPorEncabezado = []; // prod Registro_SalidasID -> devProyectoId (para resolver lineas)
    $nSalidas = 0; $nLineasSalida = 0;
    foreach ($pdo->query("SELECT ID, Fecha, Hora, UsuarioID, ContratistaID, ProyectoID FROM berdez_prod_real.registro_salidas ORDER BY ID") as $enc) {
        $devUsuarioId = $usuarioMap[$prodUsuarioIdent[(int)$enc['UsuarioID']]];
        $devProyectoId = $proyectoMap[(int)$enc['ProyectoID']];
        $insSalidaHead->execute([(int)$enc['ID'], $enc['Fecha'], $enc['Hora'], $devUsuarioId, $contratistaMap[(int)$enc['ContratistaID']], $devProyectoId]);
        $devProyectoPorEncabezado[(int)$enc['ID']] = $devProyectoId;
        $nSalidas++;
    }
    foreach ($pdo->query("SELECT ID, MaterialID, Registro_SalidasID, Cantidad, CasaID, ManzanaID, DestinoID, AreaID, RubroID FROM berdez_prod_real.material_registro_salidas ORDER BY ID") as $lin) {
        $devProyectoId = $devProyectoPorEncabezado[(int)$lin['Registro_SalidasID']];
        $etapaNombre = $prodArea[(int)$lin['AreaID']];
        $manzanaNombre = $prodManzana[(int)$lin['ManzanaID']];
        $casaNombre = $prodCasa[(int)$lin['CasaID']];

        $esTraspaso = ($etapaNombre === 'Traspaso' || $manzanaNombre === 'Traspaso' || $casaNombre === 'Traspaso');
        $esUrbanismo = (!$esTraspaso) && ($etapaNombre === 'Urbanismo' || $manzanaNombre === 'Urbanismo' || $casaNombre === 'Urbanismo');

        if ($esTraspaso) {
            $ubicacionId = $traspasoNode[$devProyectoId] ?? null;
        } elseif ($esUrbanismo) {
            $ubicacionId = $urbanismoNode[$devProyectoId] ?? null;
        } else {
            $key = $devProyectoId . '|' . strtolower($etapaNombre) . '|' . strtolower($manzanaNombre) . '|' . strtolower($casaNombre);
            $ubicacionId = $ubicacionLeaf[$key] ?? null;
        }
        if ($ubicacionId === null) {
            throw new Exception("No se pudo resolver UbicacionID para linea prod_id={$lin['ID']} (Registro_SalidasID={$lin['Registro_SalidasID']}), etapa=$etapaNombre, manzana=$manzanaNombre, casa=$casaNombre");
        }

        $insSalidaLinea->execute([
            (int)$lin['ID'],
            $materialMap[(int)$lin['MaterialID']],
            (int)$lin['Registro_SalidasID'],
            $lin['Cantidad'],
            $destinoMap[(int)$lin['DestinoID']],
            $rubroMap[(int)$lin['RubroID']],
            $ubicacionId,
        ]);
        $nLineasSalida++;
    }
    step("Paso E: $nSalidas encabezados y $nLineasSalida lineas de salida reinsertados con ID identico a Produccion y Urbanismo/Traspaso corregidos.");

    // ---------------------------------------------------------------
    // VALIDACIONES
    // ---------------------------------------------------------------
    $fails = [];

    $v1 = (int) $pdo->query("SELECT COUNT(*) FROM inventario.registro_entradas")->fetchColumn();
    if ($v1 !== 79) $fails[] = "registro_entradas = $v1 (esperado 79)";
    $v2 = (int) $pdo->query("SELECT MIN(ID) FROM inventario.registro_entradas")->fetchColumn();
    $v2b = (int) $pdo->query("SELECT MAX(ID) FROM inventario.registro_entradas")->fetchColumn();
    if ($v2 !== 1 || $v2b !== 79) $fails[] = "registro_entradas rango ID = $v2-$v2b (esperado 1-79)";

    $v3 = (int) $pdo->query("SELECT COUNT(*) FROM inventario.material_registro_entradas")->fetchColumn();
    if ($v3 !== 268) $fails[] = "material_registro_entradas = $v3 (esperado 268)";

    $v4 = (int) $pdo->query("SELECT COUNT(*) FROM inventario.registro_salidas")->fetchColumn();
    if ($v4 !== 63) $fails[] = "registro_salidas = $v4 (esperado 63)";
    $v5 = (int) $pdo->query("SELECT MIN(ID) FROM inventario.registro_salidas")->fetchColumn();
    $v5b = (int) $pdo->query("SELECT MAX(ID) FROM inventario.registro_salidas")->fetchColumn();
    if ($v5 !== 1 || $v5b !== 63) $fails[] = "registro_salidas rango ID = $v5-$v5b (esperado 1-63)";

    $v6 = (int) $pdo->query("SELECT COUNT(*) FROM inventario.material_registro_salidas")->fetchColumn();
    if ($v6 !== 462) $fails[] = "material_registro_salidas = $v6 (esperado 462)";

    $v7 = (int) $pdo->query("SELECT COUNT(*) FROM inventario.material_registro_salidas WHERE UbicacionID IS NULL")->fetchColumn();
    if ($v7 !== 0) $fails[] = "lineas de salida sin UbicacionID = $v7";

    // La salida 62 (prod) debe tener sus 1 linea bajo el nodo Urbanismo del proyecto 1, no Traspaso
    $v8 = $pdo->query("
        SELECT u.Nombre FROM inventario.material_registro_salidas mrs
        JOIN inventario.ubicacion u ON u.ID = mrs.UbicacionID
        WHERE mrs.Registro_SalidasID = 62")->fetchAll(PDO::FETCH_COLUMN);
    foreach ($v8 as $nombre) {
        if (strtolower($nombre) !== 'urbanismo') $fails[] = "Salida 62: se esperaba nodo 'Urbanismo', quedo '$nombre'";
    }

    // Conteo de lineas por nodo raiz, deben coincidir con lo esperado
    $conteoRaiz = $pdo->query("
        SELECT u.ProyectoID, u.Nombre, COUNT(*) c FROM inventario.material_registro_salidas mrs
        JOIN inventario.ubicacion u ON u.ID=mrs.UbicacionID WHERE u.Nivel=0
        GROUP BY u.ProyectoID, u.Nombre ORDER BY u.ProyectoID, u.Nombre")->fetchAll(PDO::FETCH_ASSOC);
    $esperado = [
        '1|Traspaso' => null, '1|Urbanismo' => 37,
        '2|Traspaso' => 8,
        '3|Traspaso' => 3, '3|Urbanismo' => 4,
        '62|Traspaso' => 3,
    ];
    $obtenido = [];
    foreach ($conteoRaiz as $r) $obtenido[$r['ProyectoID'] . '|' . $r['Nombre']] = (int)$r['c'];
    foreach ($esperado as $k => $v) {
        if ($v === null) continue;
        if (($obtenido[$k] ?? null) !== $v) $fails[] = "Conteo nodo raiz $k = " . ($obtenido[$k] ?? 0) . " (esperado $v)";
    }

    if (!empty($fails)) {
        throw new Exception("Validaciones fallidas:\n - " . implode("\n - ", $fails));
    }

    step("TODAS LAS VALIDACIONES PASARON.");
    $pdo->commit();
    step("COMMIT realizado. Correccion aplicada.");

} catch (Throwable $e) {
    $pdo->rollBack();
    $pdo->exec("SET FOREIGN_KEY_CHECKS=1");
    step("ERROR: " . $e->getMessage());
    step("ROLLBACK realizado. inventario quedo sin cambios respecto al estado anterior a esta correccion.");
    fwrite(STDERR, "\n=== FALLO ===\n");
    exit(1);
}

echo "\n=== RESUMEN ===\n" . implode("\n", $log) . "\n";
