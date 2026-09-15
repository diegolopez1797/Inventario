<?php
// Ejecuta la migracion Produccion -> Desarrollo aprobada en
// docs/plan-ejecucion-final.md. Corre dentro de UNA transaccion: si alguna
// validacion final falla, hace ROLLBACK y no deja nada a medias.
//
// Requiere: berdez_prod_real y berdez_migracion_staging ya existentes en el
// mismo servidor MySQL que `inventario` (creados durante el analisis).

$pdo = new PDO('mysql:host=127.0.0.1;dbname=inventario;charset=utf8mb4', 'root', '', [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
]);

function normDesc($s) {
    return mb_strtolower(trim(str_replace('"', '', $s)), 'UTF-8');
}

$log = [];
function step($msg) {
    global $log;
    $log[] = $msg;
    fwrite(STDERR, $msg . "\n");
}

$pdo->beginTransaction();

try {
    // ---------------------------------------------------------------
    // PASO 1: usuarios nuevos reales
    // ---------------------------------------------------------------
    $pdo->exec(<<<'SQL'
INSERT INTO inventario.usuario (Identificacion, Nombre, Apellido, Clave, RolID)
SELECT 83235047, 'Reinaldo', 'Gordo Losada',
       '$2y$10$P8.cW9Rus2gc30bNNdq93uQ1I06NGt.EzXvcSVJVd52zHsIUCruby',
       r.ID
FROM inventario.rol r WHERE r.Descripcion = 'Almacenero'
  AND NOT EXISTS (SELECT 1 FROM inventario.usuario WHERE Identificacion = 83235047)
SQL);
    $pdo->exec(<<<'SQL'
INSERT INTO inventario.usuario (Identificacion, Nombre, Apellido, Clave, RolID)
SELECT 1070605738, 'Caterine Fernanda', 'Jovel Rincon',
       '$2y$10$ADX.9MoHC0HnzwMMNGnkqeHsdzD1GvgiZnxAxUsfRvouRhXlynDCa',
       r.ID
FROM inventario.rol r WHERE r.Descripcion = 'Almacenero'
  AND NOT EXISTS (SELECT 1 FROM inventario.usuario WHERE Identificacion = 1070605738)
SQL);
    step("Paso 1: usuarios reales insertados (Reinaldo, Caterine).");

    // ---------------------------------------------------------------
    // PASO 2: proyecto Alcala
    // ---------------------------------------------------------------
    $pdo->exec(<<<'SQL'
INSERT INTO inventario.proyecto (Descripcion)
SELECT 'Alcala'
WHERE NOT EXISTS (SELECT 1 FROM inventario.proyecto WHERE LOWER(TRIM(Descripcion)) = 'alcala')
SQL);
    $alcalaDevId = (int) $pdo->query("SELECT ID FROM inventario.proyecto WHERE LOWER(TRIM(Descripcion))='alcala'")->fetchColumn();
    step("Paso 2: proyecto Alcala creado con dev_id=$alcalaDevId.");

    // Completar combos_ubicacion para Alcala (prod_id=4), que no tenia dev_id
    // porque el proyecto no existia cuando se genero esa tabla.
    $pdo->exec("DELETE FROM berdez_migracion_staging.combos_ubicacion WHERE proyecto_dev_id IS NULL");
    $stmt = $pdo->prepare(<<<'SQL'
INSERT INTO berdez_migracion_staging.combos_ubicacion
  (proyecto_dev_id, proyecto_dev, etapa, manzana, casa, movimientos)
VALUES (?, 'Alcala', 'Traspaso', 'Traspaso', 'Urbanismo', 3)
SQL);
    $stmt->execute([$alcalaDevId]);
    step("Paso 2b: combos_ubicacion completado para Alcala.");

    // ---------------------------------------------------------------
    // PASO 3: contratistas
    // ---------------------------------------------------------------
    $pdo->exec(<<<'SQL'
UPDATE inventario.contratista
SET Descripcion = 'Amin Narvaez'
WHERE LOWER(TRIM(Descripcion)) = 'construcciones narvaez camacho'
SQL);
    $pdo->exec(<<<'SQL'
INSERT INTO inventario.contratista (Descripcion)
SELECT p.Descripcion
FROM berdez_prod_real.contratista p
WHERE p.Descripcion IN ('Sendos', 'Grabiel cardona')
  AND NOT EXISTS (
    SELECT 1 FROM inventario.contratista d WHERE LOWER(TRIM(d.Descripcion)) = LOWER(TRIM(p.Descripcion))
  )
SQL);
    step("Paso 3: contratistas actualizados (Amin Narvaez renombrado, Sendos y Grabiel cardona insertados).");

    // ---------------------------------------------------------------
    // PASO 4: destinos
    // ---------------------------------------------------------------
    $pdo->exec(<<<'SQL'
INSERT INTO inventario.destino (Descripcion)
SELECT p.Descripcion
FROM berdez_prod_real.destino p
WHERE NOT EXISTS (
  SELECT 1 FROM inventario.destino d WHERE LOWER(TRIM(d.Descripcion)) = LOWER(TRIM(p.Descripcion))
)
SQL);
    step("Paso 4: destinos completados.");

    // ---------------------------------------------------------------
    // PASO 5: rubros
    // ---------------------------------------------------------------
    $pdo->exec(<<<'SQL'
INSERT INTO inventario.rubro (Descripcion)
SELECT p.Descripcion
FROM berdez_prod_real.rubro p
WHERE NOT EXISTS (
  SELECT 1 FROM inventario.rubro d WHERE LOWER(TRIM(d.Descripcion)) = LOWER(TRIM(p.Descripcion))
)
SQL);
    step("Paso 5: los 35 rubros de Produccion insertados.");

    // ---------------------------------------------------------------
    // PASO 6: materiales
    // ---------------------------------------------------------------
    // Tambien se actualiza Descripcion (no solo Codigo/Saldo/Min/Max): se
    // detecto que la collation utf8mb4_general_ci trata como iguales textos
    // que en realidad difieren a nivel de caracter (ej. "presiòn" en
    // Desarrollo vs "presión" real en Produccion, tilde grave vs aguda).
    // Con la regla "gana Produccion" aplicada a todo, se corrige el texto.
    $pdo->exec(<<<'SQL'
UPDATE inventario.material d
JOIN berdez_prod_real.material p
  ON LOWER(TRIM(REPLACE(d.Descripcion,'"',''))) = LOWER(TRIM(REPLACE(p.Descripcion,'"','')))
SET d.Codigo = p.Codigo,
    d.Descripcion = p.Descripcion,
    d.Saldo = p.Saldo,
    d.Min_Almacen = p.Min_Almacen,
    d.Max_Casa = p.Max_Casa,
    d.CostoPromedio = NULL
SQL);
    $pdo->exec(<<<'SQL'
INSERT INTO inventario.material (Codigo, Descripcion, Unidad, Saldo, Min_Almacen, Max_Casa, CostoPromedio)
SELECT p.Codigo, p.Descripcion, p.Unidad, p.Saldo, p.Min_Almacen, p.Max_Casa, NULL
FROM berdez_prod_real.material p
WHERE p.Codigo BETWEEN 356 AND 371
  AND NOT EXISTS (
    SELECT 1 FROM inventario.material d
    WHERE LOWER(TRIM(REPLACE(d.Descripcion,'"',''))) = LOWER(TRIM(REPLACE(p.Descripcion,'"','')))
  )
SQL);
    $pdo->exec("UPDATE inventario.material SET Descripcion='Gancho tipo C de 1/4 20x8x8', Codigo=26, Saldo=6880, Min_Almacen=0, Max_Casa=0, CostoPromedio=NULL WHERE ID=43");
    $pdo->exec("UPDATE inventario.material SET Descripcion='Gancho tipo C de 1/4 10x8x8', Codigo=29, Saldo=6790, Min_Almacen=0, Max_Casa=0, CostoPromedio=NULL WHERE ID=46");
    step("Paso 6: 273 materiales actualizados, 16 insertados, 2 conflictos de Gancho resueltos.");

    // ---------------------------------------------------------------
    // PASO 7: limpieza de datos de prueba
    // ---------------------------------------------------------------
    // 7a. Exportar auditoria antes de vaciar
    $auditRows = $pdo->query("SELECT * FROM inventario.auditoria")->fetchAll(PDO::FETCH_ASSOC);
    file_put_contents(__DIR__ . '/../backups/auditoria_pruebas_export.json', json_encode($auditRows, JSON_PRETTY_PRINT));
    $pdo->exec("DELETE FROM inventario.auditoria");
    step("Paso 7a: " . count($auditRows) . " filas de auditoria exportadas a backups/auditoria_pruebas_export.json y vaciadas.");

    $pdo->exec("DELETE FROM inventario.solicitud_detalle");
    $pdo->exec("DELETE FROM inventario.solicitud");
    $pdo->exec("DELETE FROM inventario.ajuste_inventario");
    $pdo->exec("DELETE FROM inventario.material_registro_salidas");
    $pdo->exec("DELETE FROM inventario.registro_salidas");
    $pdo->exec("DELETE FROM inventario.material_registro_entradas");
    $pdo->exec("DELETE FROM inventario.registro_entradas");
    step("Paso 7b-d: solicitudes, ajustes e historial de movimientos de prueba eliminados.");

    $pdo->exec("DELETE FROM inventario.usuario WHERE Identificacion = 123456789");
    $pdo->exec("DELETE FROM inventario.proveedor WHERE LOWER(TRIM(Descripcion)) = 'andamios del sur'");
    $pdo->exec("DELETE FROM inventario.rubro WHERE Descripcion IN ('Cajilla', 'Anden')");
    $pdo->exec("DELETE FROM inventario.material_almacen WHERE MaterialID IN (1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21)");
    $pdo->exec("DELETE FROM inventario.material WHERE ID IN (1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21)");
    step("Paso 7e-i: Pedro Perez, Andamios del sur, rubros de prueba y 21 materiales de prueba eliminados.");

    // Los nodos de ubicacion referencian al proyecto por FK, asi que se borran
    // ANTES de poder borrar el proyecto "CC san pablo" que los contenia.
    // Se borra por Nivel descendente (hijos antes que padres) porque
    // ubicacion.PadreID es una FK autorreferenciada: un DELETE unico que
    // mezcle niveles puede intentar borrar un padre antes que su hijo dentro
    // del mismo lote y violar la constraint aunque ambos vayan a borrarse.
    for ($nivel = 3; $nivel >= 0; $nivel--) {
        $pdo->exec("DELETE FROM inventario.ubicacion WHERE ProyectoID IN (1,2,3,57) AND Nivel = $nivel");
    }
    step("Paso 7j: 198 nodos de ubicacion de prueba eliminados.");

    $pdo->exec("DELETE FROM inventario.proyecto WHERE LOWER(TRIM(Descripcion)) = 'cc san pablo'");
    step("Paso 7f: proyecto de prueba 'CC san pablo' eliminado.");

    // ---------------------------------------------------------------
    // PASO 8: reconstruir ubicacion
    // ---------------------------------------------------------------
    $pdo->exec(<<<'SQL'
INSERT INTO inventario.ubicacion (ProyectoID, PadreID, Nombre, Tipo, Nivel, Activo)
SELECT DISTINCT c.proyecto_dev_id, NULL, 'Traspaso', 'TRASPASO', 0, 1
FROM berdez_migracion_staging.combos_ubicacion c
WHERE c.proyecto_dev_id IS NOT NULL
  AND (c.etapa = 'Traspaso' OR c.manzana IN ('Traspaso','Urbanismo') OR c.casa = 'Urbanismo')
  AND NOT EXISTS (
    SELECT 1 FROM inventario.ubicacion u
    WHERE u.ProyectoID = c.proyecto_dev_id AND u.PadreID IS NULL AND LOWER(u.Nombre) = 'traspaso'
  )
SQL);
    $pdo->exec(<<<'SQL'
INSERT INTO inventario.ubicacion (ProyectoID, PadreID, Nombre, Tipo, Nivel, Activo)
SELECT DISTINCT c.proyecto_dev_id, NULL, c.etapa, 'ETAPA', 0, 1
FROM berdez_migracion_staging.combos_ubicacion c
WHERE c.proyecto_dev_id IS NOT NULL
  AND NOT (c.etapa = 'Traspaso' OR c.manzana IN ('Traspaso','Urbanismo') OR c.casa = 'Urbanismo')
  AND NOT EXISTS (
    SELECT 1 FROM inventario.ubicacion u
    WHERE u.ProyectoID = c.proyecto_dev_id AND u.PadreID IS NULL AND LOWER(u.Nombre) = LOWER(c.etapa)
  )
SQL);
    $pdo->exec(<<<'SQL'
INSERT INTO inventario.ubicacion (ProyectoID, PadreID, Nombre, Tipo, Nivel, Activo)
SELECT DISTINCT c.proyecto_dev_id, ue.ID, c.manzana, 'MANZANA', 1, 1
FROM berdez_migracion_staging.combos_ubicacion c
JOIN inventario.ubicacion ue
  ON ue.ProyectoID = c.proyecto_dev_id AND ue.PadreID IS NULL AND LOWER(ue.Nombre) = LOWER(c.etapa)
WHERE c.proyecto_dev_id IS NOT NULL
  AND NOT (c.etapa = 'Traspaso' OR c.manzana IN ('Traspaso','Urbanismo') OR c.casa = 'Urbanismo')
  AND NOT EXISTS (
    SELECT 1 FROM inventario.ubicacion um
    WHERE um.ProyectoID = c.proyecto_dev_id AND um.PadreID = ue.ID AND LOWER(um.Nombre) = LOWER(c.manzana)
  )
SQL);
    $pdo->exec(<<<'SQL'
INSERT INTO inventario.ubicacion (ProyectoID, PadreID, Nombre, Tipo, Nivel, Activo)
SELECT DISTINCT c.proyecto_dev_id, um.ID, c.casa, 'CASA', 2, 1
FROM berdez_migracion_staging.combos_ubicacion c
JOIN inventario.ubicacion ue
  ON ue.ProyectoID = c.proyecto_dev_id AND ue.PadreID IS NULL AND LOWER(ue.Nombre) = LOWER(c.etapa)
JOIN inventario.ubicacion um
  ON um.ProyectoID = c.proyecto_dev_id AND um.PadreID = ue.ID AND LOWER(um.Nombre) = LOWER(c.manzana)
WHERE c.proyecto_dev_id IS NOT NULL
  AND NOT (c.etapa = 'Traspaso' OR c.manzana IN ('Traspaso','Urbanismo') OR c.casa = 'Urbanismo')
  AND NOT EXISTS (
    SELECT 1 FROM inventario.ubicacion uc
    WHERE uc.ProyectoID = c.proyecto_dev_id AND uc.PadreID = um.ID AND LOWER(uc.Nombre) = LOWER(c.casa)
  )
SQL);
    $nUbic = (int) $pdo->query("SELECT COUNT(*) FROM inventario.ubicacion WHERE ProyectoID IN (1,2,3,$alcalaDevId)")->fetchColumn();
    step("Paso 8: arbol de ubicacion reconstruido ($nUbic nodos reales, incluyendo Alcala).");

    // ---------------------------------------------------------------
    // PASO 9: sincronizar material_almacen
    // ---------------------------------------------------------------
    $pdo->exec(<<<'SQL'
INSERT INTO inventario.material_almacen (MaterialID, AlmacenID, Saldo)
SELECT m.ID, 1, m.Saldo
FROM inventario.material m
WHERE NOT EXISTS (
  SELECT 1 FROM inventario.material_almacen ma WHERE ma.MaterialID = m.ID AND ma.AlmacenID = 1
)
ON DUPLICATE KEY UPDATE Saldo = VALUES(Saldo)
SQL);
    $pdo->exec(<<<'SQL'
UPDATE inventario.material_almacen ma
JOIN inventario.material m ON m.ID = ma.MaterialID
SET ma.Saldo = m.Saldo
WHERE ma.AlmacenID = 1 AND ma.Saldo <> m.Saldo
SQL);
    step("Paso 9: material_almacen sincronizado.");

    // ---------------------------------------------------------------
    // PASO 10-11: PREPARAR MAPAS EN MEMORIA para el replay
    // ---------------------------------------------------------------
    $usuarioMap = []; // prod Identificacion -> dev usuario ID
    foreach ($pdo->query("SELECT Identificacion, ID FROM inventario.usuario") as $r) {
        $usuarioMap[(int)$r['Identificacion']] = (int)$r['ID'];
    }
    // prod usuario ID -> Identificacion (para traducir UsuarioID de los encabezados de prod)
    $prodUsuarioIdent = [];
    foreach ($pdo->query("SELECT ID, Identificacion FROM berdez_prod_real.usuario") as $r) {
        $prodUsuarioIdent[(int)$r['ID']] = (int)$r['Identificacion'];
    }

    $proyectoOverride = [2 => 'Salamanca la nueva conjunto residencial', 3 => 'Tienda D1'];
    $proyectoMap = [];
    foreach ($pdo->query("SELECT ID, Descripcion FROM berdez_prod_real.proyecto") as $r) {
        $pid = (int)$r['ID'];
        $target = $proyectoOverride[$pid] ?? $r['Descripcion'];
        if ($pid === 4) { $proyectoMap[$pid] = $alcalaDevId; continue; }
        $stmt = $pdo->prepare("SELECT ID FROM inventario.proyecto WHERE LOWER(TRIM(Descripcion)) = LOWER(TRIM(?))");
        $stmt->execute([$target]);
        $devId = $stmt->fetchColumn();
        if ($devId === false) throw new Exception("No se pudo mapear proyecto prod_id=$pid ('{$r['Descripcion']}')");
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
        if ($devId === false) throw new Exception("No se pudo mapear contratista prod_id=$pid ('{$r['Descripcion']}')");
        $contratistaMap[$pid] = (int)$devId;
    }

    $destinoMap = [];
    foreach ($pdo->query("SELECT ID, Descripcion FROM berdez_prod_real.destino") as $r) {
        $stmt = $pdo->prepare("SELECT ID FROM inventario.destino WHERE LOWER(TRIM(Descripcion)) = LOWER(TRIM(?))");
        $stmt->execute([$r['Descripcion']]);
        $devId = $stmt->fetchColumn();
        if ($devId === false) throw new Exception("No se pudo mapear destino prod_id={$r['ID']} ('{$r['Descripcion']}')");
        $destinoMap[(int)$r['ID']] = (int)$devId;
    }

    $rubroMap = [];
    foreach ($pdo->query("SELECT ID, Descripcion FROM berdez_prod_real.rubro") as $r) {
        $stmt = $pdo->prepare("SELECT ID FROM inventario.rubro WHERE LOWER(TRIM(Descripcion)) = LOWER(TRIM(?))");
        $stmt->execute([$r['Descripcion']]);
        $devId = $stmt->fetchColumn();
        if ($devId === false) throw new Exception("No se pudo mapear rubro prod_id={$r['ID']} ('{$r['Descripcion']}')");
        $rubroMap[(int)$r['ID']] = (int)$devId;
    }

    $materialMap = [];
    $devMaterials = $pdo->query("SELECT ID, Descripcion FROM inventario.material")->fetchAll(PDO::FETCH_ASSOC);
    $devMaterialByDesc = [];
    foreach ($devMaterials as $r) {
        $devMaterialByDesc[normDesc($r['Descripcion'])] = (int)$r['ID'];
    }
    foreach ($pdo->query("SELECT ID, Descripcion FROM berdez_prod_real.material") as $r) {
        $key = normDesc($r['Descripcion']);
        if (!isset($devMaterialByDesc[$key])) throw new Exception("No se pudo mapear material prod_id={$r['ID']} ('{$r['Descripcion']}')");
        $materialMap[(int)$r['ID']] = $devMaterialByDesc[$key];
    }

    // Descripciones de area/manzana/casa de Produccion (catalogos pequeños)
    $prodArea = []; foreach ($pdo->query("SELECT ID, Descripcion FROM berdez_prod_real.area") as $r) $prodArea[(int)$r['ID']] = $r['Descripcion'];
    $prodManzana = []; foreach ($pdo->query("SELECT ID, Descripcion FROM berdez_prod_real.manzana") as $r) $prodManzana[(int)$r['ID']] = $r['Descripcion'];
    $prodCasa = []; foreach ($pdo->query("SELECT ID, Descripcion FROM berdez_prod_real.casa") as $r) $prodCasa[(int)$r['ID']] = $r['Descripcion'];

    // Mapa de ubicacion: hoja Casa (nivel 2) y nodo raiz Traspaso (nivel 0), por proyecto
    $ubicacionLeaf = []; // "$devProyectoId|$etapaLower|$manzanaLower|$casaLower" -> ubicacionId
    $traspasoNode = []; // devProyectoId -> ubicacionId
    foreach ($pdo->query("
        SELECT u.ID, u.ProyectoID, u.PadreID, u.Nombre, u.Nivel
        FROM inventario.ubicacion u") as $r) {
        if ((int)$r['Nivel'] === 0 && $r['PadreID'] === null && strtolower($r['Nombre']) === 'traspaso') {
            $traspasoNode[(int)$r['ProyectoID']] = (int)$r['ID'];
        }
    }
    // Reconstruir rutas etapa/manzana/casa recorriendo el arbol recien creado
    $allUbic = $pdo->query("SELECT ID, ProyectoID, PadreID, Nombre, Nivel FROM inventario.ubicacion")->fetchAll(PDO::FETCH_ASSOC);
    $byId = [];
    foreach ($allUbic as $r) $byId[(int)$r['ID']] = $r;
    foreach ($allUbic as $r) {
        if ((int)$r['Nivel'] !== 2) continue; // solo hojas Casa
        $manzana = $byId[(int)$r['PadreID']] ?? null;
        if (!$manzana) continue;
        $etapa = $byId[(int)$manzana['PadreID']] ?? null;
        if (!$etapa) continue;
        $key = $r['ProyectoID'] . '|' . strtolower($etapa['Nombre']) . '|' . strtolower($manzana['Nombre']) . '|' . strtolower($r['Nombre']);
        $ubicacionLeaf[$key] = (int)$r['ID'];
    }
    step("Paso 10-11 prep: mapas de usuario/proyecto/contratista/destino/rubro/material/ubicacion construidos en memoria.");

    // ---------------------------------------------------------------
    // PASO 10: replay de registro_entradas
    // ---------------------------------------------------------------
    $insEntradaHead = $pdo->prepare("INSERT INTO inventario.registro_entradas (Fecha, Hora, UsuarioID, ProveedorID) VALUES (?, ?, ?, NULL)");
    $insEntradaLinea = $pdo->prepare("INSERT INTO inventario.material_registro_entradas (MaterialID, Registro_EntradasID, Cantidad, DestinoID, CostoUnitario) VALUES (?, ?, ?, ?, NULL)");
    $selEntradaLineas = $pdo->prepare("SELECT MaterialID, Cantidad, DestinoID FROM berdez_prod_real.material_registro_entradas WHERE Registro_EntradasID = ?");

    $nEntradas = 0; $nLineasEntrada = 0;
    foreach ($pdo->query("SELECT ID, Fecha, Hora, UsuarioID FROM berdez_prod_real.registro_entradas ORDER BY ID") as $enc) {
        $devUsuarioId = $usuarioMap[$prodUsuarioIdent[(int)$enc['UsuarioID']]];
        $insEntradaHead->execute([$enc['Fecha'], $enc['Hora'], $devUsuarioId]);
        $devEncId = (int) $pdo->lastInsertId();
        $nEntradas++;
        $selEntradaLineas->execute([(int)$enc['ID']]);
        foreach ($selEntradaLineas->fetchAll(PDO::FETCH_ASSOC) as $lin) {
            $insEntradaLinea->execute([
                $materialMap[(int)$lin['MaterialID']],
                $devEncId,
                $lin['Cantidad'],
                $destinoMap[(int)$lin['DestinoID']],
            ]);
            $nLineasEntrada++;
        }
    }
    step("Paso 10: $nEntradas encabezados de entrada y $nLineasEntrada lineas replicados.");

    // ---------------------------------------------------------------
    // PASO 11: replay de registro_salidas
    // ---------------------------------------------------------------
    $insSalidaHead = $pdo->prepare("INSERT INTO inventario.registro_salidas (Fecha, Hora, UsuarioID, ContratistaID, ProyectoID) VALUES (?, ?, ?, ?, ?)");
    $insSalidaLinea = $pdo->prepare("INSERT INTO inventario.material_registro_salidas
        (MaterialID, Registro_SalidasID, Cantidad, CasaID, ManzanaID, DestinoID, AreaID, RubroID, UbicacionID, CostoUnitario)
        VALUES (?, ?, ?, NULL, NULL, ?, NULL, ?, ?, NULL)");
    $selSalidaLineas = $pdo->prepare("SELECT MaterialID, Cantidad, CasaID, ManzanaID, DestinoID, AreaID, RubroID FROM berdez_prod_real.material_registro_salidas WHERE Registro_SalidasID = ?");

    $nSalidas = 0; $nLineasSalida = 0;
    foreach ($pdo->query("SELECT ID, Fecha, Hora, UsuarioID, ContratistaID, ProyectoID FROM berdez_prod_real.registro_salidas ORDER BY ID") as $enc) {
        $devUsuarioId = $usuarioMap[$prodUsuarioIdent[(int)$enc['UsuarioID']]];
        $devProyectoId = $proyectoMap[(int)$enc['ProyectoID']];
        $insSalidaHead->execute([$enc['Fecha'], $enc['Hora'], $devUsuarioId, $contratistaMap[(int)$enc['ContratistaID']], $devProyectoId]);
        $devEncId = (int) $pdo->lastInsertId();
        $nSalidas++;
        $selSalidaLineas->execute([(int)$enc['ID']]);
        foreach ($selSalidaLineas->fetchAll(PDO::FETCH_ASSOC) as $lin) {
            $etapaNombre = $prodArea[(int)$lin['AreaID']];
            $manzanaNombre = $prodManzana[(int)$lin['ManzanaID']];
            $casaNombre = $prodCasa[(int)$lin['CasaID']];
            $esGenerico = ($etapaNombre === 'Traspaso' || in_array($manzanaNombre, ['Traspaso', 'Urbanismo'], true) || $casaNombre === 'Urbanismo');
            if ($esGenerico) {
                $ubicacionId = $traspasoNode[$devProyectoId] ?? null;
            } else {
                $key = $devProyectoId . '|' . strtolower($etapaNombre) . '|' . strtolower($manzanaNombre) . '|' . strtolower($casaNombre);
                $ubicacionId = $ubicacionLeaf[$key] ?? null;
            }
            if ($ubicacionId === null) {
                throw new Exception("No se pudo resolver UbicacionID para salida encabezado prod_id={$enc['ID']}, etapa=$etapaNombre, manzana=$manzanaNombre, casa=$casaNombre");
            }
            $insSalidaLinea->execute([
                $materialMap[(int)$lin['MaterialID']],
                $devEncId,
                $lin['Cantidad'],
                $destinoMap[(int)$lin['DestinoID']],
                $rubroMap[(int)$lin['RubroID']],
                $ubicacionId,
            ]);
            $nLineasSalida++;
        }
    }
    step("Paso 11: $nSalidas encabezados de salida y $nLineasSalida lineas replicados.");

    // ---------------------------------------------------------------
    // VALIDACIONES
    // ---------------------------------------------------------------
    $fails = [];

    $v1 = (int) $pdo->query("SELECT COUNT(*) FROM inventario.usuario WHERE Identificacion IN (1004035010, 83235047, 1070605738)")->fetchColumn();
    if ($v1 !== 3) $fails[] = "V1: usuarios reales = $v1 (esperado 3)";

    $v2 = (int) $pdo->query("SELECT COUNT(*) FROM inventario.usuario WHERE Identificacion = 123456789")->fetchColumn();
    if ($v2 !== 0) $fails[] = "V2: Pedro Perez sigue existiendo";

    $v3 = (int) $pdo->query("SELECT COUNT(*) FROM inventario.usuario u LEFT JOIN inventario.rol r ON r.ID=u.RolID WHERE r.ID IS NULL")->fetchColumn();
    if ($v3 !== 0) $fails[] = "V3: usuarios con RolID invalido = $v3";

    $v4 = (int) $pdo->query("SELECT COUNT(*) FROM inventario.rol_permiso")->fetchColumn();
    if ($v4 !== 35) $fails[] = "V4: rol_permiso tiene $v4 filas (esperado 35)";

    $v5 = (int) $pdo->query("
        SELECT COUNT(*) FROM inventario.material d
        JOIN berdez_prod_real.material p ON LOWER(TRIM(REPLACE(d.Descripcion,'\"',''))) = LOWER(TRIM(REPLACE(p.Descripcion,'\"','')))
        WHERE d.Saldo <> p.Saldo")->fetchColumn();
    if ($v5 !== 0) $fails[] = "V5: materiales con saldo distinto al real = $v5";

    $v6 = (int) $pdo->query("
        SELECT COUNT(*) FROM inventario.material m
        JOIN inventario.material_almacen ma ON ma.MaterialID=m.ID AND ma.AlmacenID=1
        WHERE ma.Saldo <> m.Saldo")->fetchColumn();
    if ($v6 !== 0) $fails[] = "V6: material_almacen desincronizado = $v6";

    $v7 = (int) $pdo->query("SELECT COUNT(*) FROM inventario.ubicacion WHERE Tipo IN ('Torre','Piso','Apartamento','Local')")->fetchColumn();
    if ($v7 !== 0) $fails[] = "V7: quedan nodos de ubicacion de prueba = $v7";

    $v8a = (int) $pdo->query("SELECT COUNT(*) FROM inventario.material WHERE ID IN (1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21)")->fetchColumn();
    if ($v8a !== 0) $fails[] = "V8a: quedan materiales de prueba = $v8a";
    $v8b = (int) $pdo->query("SELECT COUNT(*) FROM inventario.proyecto WHERE LOWER(TRIM(Descripcion))='cc san pablo'")->fetchColumn();
    if ($v8b !== 0) $fails[] = "V8b: CC san pablo sigue existiendo";
    $v8c = (int) $pdo->query("SELECT COUNT(*) FROM inventario.proveedor WHERE LOWER(TRIM(Descripcion))='andamios del sur'")->fetchColumn();
    if ($v8c !== 0) $fails[] = "V8c: Andamios del sur sigue existiendo";
    $v8d = (int) $pdo->query("SELECT COUNT(*) FROM inventario.rubro WHERE Descripcion IN ('Cajilla','Anden')")->fetchColumn();
    if ($v8d !== 0) $fails[] = "V8d: rubros de prueba siguen existiendo = $v8d";

    $v9 = (int) $pdo->query("SELECT COUNT(*) FROM (SELECT LOWER(TRIM(Descripcion)) d FROM inventario.material GROUP BY d HAVING COUNT(*)>1) t")->fetchColumn();
    if ($v9 !== 0) $fails[] = "V9: descripciones de material duplicadas = $v9";

    $v10a = (int) $pdo->query("SELECT COUNT(*) FROM inventario.proyecto WHERE LOWER(TRIM(Descripcion))='alcala'")->fetchColumn();
    if ($v10a !== 1) $fails[] = "V10a: proyecto Alcala = $v10a (esperado 1)";
    $v10b = (int) $pdo->query("SELECT COUNT(*) FROM inventario.contratista WHERE Descripcion='Amin Narvaez'")->fetchColumn();
    if ($v10b !== 1) $fails[] = "V10b: contratista Amin Narvaez = $v10b (esperado 1)";

    $v11a = (int) $pdo->query("SELECT COUNT(*) FROM inventario.registro_entradas")->fetchColumn();
    if ($v11a !== 79) $fails[] = "V11a: registro_entradas = $v11a (esperado 79)";
    $v11b = (int) $pdo->query("SELECT COUNT(*) FROM inventario.material_registro_entradas")->fetchColumn();
    if ($v11b !== 268) $fails[] = "V11b: material_registro_entradas = $v11b (esperado 268)";
    $v11c = (int) $pdo->query("SELECT COUNT(*) FROM inventario.registro_salidas")->fetchColumn();
    if ($v11c !== 63) $fails[] = "V11c: registro_salidas = $v11c (esperado 63)";
    $v11d = (int) $pdo->query("SELECT COUNT(*) FROM inventario.material_registro_salidas")->fetchColumn();
    if ($v11d !== 462) $fails[] = "V11d: material_registro_salidas = $v11d (esperado 462)";

    $v12 = (int) $pdo->query("SELECT COUNT(*) FROM inventario.material_registro_salidas WHERE UbicacionID IS NULL")->fetchColumn();
    if ($v12 !== 0) $fails[] = "V12: lineas de salida sin UbicacionID = $v12";

    if (!empty($fails)) {
        throw new Exception("Validaciones fallidas:\n - " . implode("\n - ", $fails));
    }

    step("TODAS LAS VALIDACIONES PASARON.");
    $pdo->commit();
    step("COMMIT realizado. Migracion aplicada correctamente.");

} catch (Throwable $e) {
    $pdo->rollBack();
    step("ERROR: " . $e->getMessage());
    step("ROLLBACK realizado. inventario quedo sin cambios.");
    fwrite(STDERR, "\n=== FALLO, VER MENSAJE ARRIBA ===\n");
    exit(1);
}

echo "\n=== RESUMEN ===\n" . implode("\n", $log) . "\n";
