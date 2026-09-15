-- =====================================================================
-- MIGRACION PRODUCCION -> DESARROLLO (inventario)
-- Todos los conflictos de docs/plan-ejecucion-final.md fueron resueltos
-- por el usuario. Este script queda completo para los pasos 1-9
-- (catalogos, limpieza de prueba, ubicacion). Los pasos 10-11 (replay de
-- movimientos historicos) se ejecutan con un script PHP auxiliar, no con
-- SQL puro (ver la explicacion en esas secciones).
--
-- NO EJECUTAR hasta recibir "APROBADO, EJECUTA" del usuario.
--
-- Requiere, en el mismo servidor MySQL que la base `inventario`:
--   - berdez_prod_real          (dump verbatim de Produccion, ya cargado)
--   - berdez_migracion_staging  (tablas de mapeo, ya construidas)
-- Ver docs/plan-ejecucion-final.md para el detalle de cada mapeo.
--
-- REGLA APLICADA EN TODO EL SCRIPT (confirmada por el usuario):
-- Produccion es la unica fuente de verdad de los DATOS. Cualquier dato que
-- exista solo en Desarrollo, sin respaldo en Produccion, se considera no
-- relevante y se elimina. El ESQUEMA/modelo de Desarrollo (tablas, permisos,
-- estructura de `ubicacion`, almacenes, etc.) se conserva intacto: esta
-- regla aplica a los datos, no a la estructura.
-- =====================================================================

START TRANSACTION;

-- ---------------------------------------------------------------------
-- 0. BACKUP (recordatorio - se hace FUERA de esta transaccion, con
--    mysqldump, antes de ejecutar nada de este script)
-- ---------------------------------------------------------------------
-- mysqldump -u root inventario > backup_inventario_pre_migracion_YYYYMMDD.sql

-- ---------------------------------------------------------------------
-- 1. USUARIOS NUEVOS REALES
--    Contraseñas ya rehasheadas con password_hash() fuera de la BD, a partir
--    de la clave en texto plano real de Produccion (Rei_8323, Fer_1070).
--    Juan (Identificacion 1004035010) ya existe en Desarrollo: no se toca.
-- ---------------------------------------------------------------------
INSERT INTO inventario.usuario (Identificacion, Nombre, Apellido, Clave, RolID)
SELECT 83235047, 'Reinaldo', 'Gordo Losada',
       '$2y$10$P8.cW9Rus2gc30bNNdq93uQ1I06NGt.EzXvcSVJVd52zHsIUCruby',
       r.ID
FROM inventario.rol r WHERE r.Descripcion = 'Almacenero'
  AND NOT EXISTS (SELECT 1 FROM inventario.usuario WHERE Identificacion = 83235047);

INSERT INTO inventario.usuario (Identificacion, Nombre, Apellido, Clave, RolID)
SELECT 1070605738, 'Caterine Fernanda', 'Jovel Rincon',
       '$2y$10$ADX.9MoHC0HnzwMMNGnkqeHsdzD1GvgiZnxAxUsfRvouRhXlynDCa',
       r.ID
FROM inventario.rol r WHERE r.Descripcion = 'Almacenero'
  AND NOT EXISTS (SELECT 1 FROM inventario.usuario WHERE Identificacion = 1070605738);

-- (Rol "Almacenista" -> "Almacenero" confirmado por el usuario.)

-- ---------------------------------------------------------------------
-- 2. PROYECTOS
--    "Local D1" = "Tienda D1" (confirmado, mismo proyecto, no se inserta nada).
--    "Alcala" no tiene equivalente en Desarrollo -> se crea.
-- ---------------------------------------------------------------------
INSERT INTO inventario.proyecto (Descripcion)
SELECT 'Alcala'
WHERE NOT EXISTS (SELECT 1 FROM inventario.proyecto WHERE LOWER(TRIM(Descripcion)) = 'alcala');

-- ---------------------------------------------------------------------
-- 3. CONTRATISTAS
--    "Amin Narvaez" = "Construcciones narvaez camacho" (confirmado, mismo
--    contratista); Produccion es la fuente de verdad del dato -> se renombra
--    el registro existente de Desarrollo al nombre de Produccion.
--    Sendos y Grabiel cardona no tienen equivalente -> se insertan.
-- ---------------------------------------------------------------------
UPDATE inventario.contratista
SET Descripcion = 'Amin Narvaez'
WHERE LOWER(TRIM(Descripcion)) = 'construcciones narvaez camacho';

INSERT INTO inventario.contratista (Descripcion)
SELECT p.Descripcion
FROM berdez_prod_real.contratista p
WHERE p.Descripcion IN ('Sendos', 'Grabiel cardona')
  AND NOT EXISTS (
    SELECT 1 FROM inventario.contratista d WHERE LOWER(TRIM(d.Descripcion)) = LOWER(TRIM(p.Descripcion))
  );

-- ---------------------------------------------------------------------
-- 4. DESTINOS (los 4 que faltan: Cubierta, Tuberías, Vías..., Accesorios)
-- ---------------------------------------------------------------------
INSERT INTO inventario.destino (Descripcion)
SELECT p.Descripcion
FROM berdez_prod_real.destino p
WHERE NOT EXISTS (
  SELECT 1 FROM inventario.destino d WHERE LOWER(TRIM(d.Descripcion)) = LOWER(TRIM(p.Descripcion))
);

-- ---------------------------------------------------------------------
-- 5. RUBROS (los 35 de Produccion; los 2 de prueba de Desarrollo -Cajilla/
--    Anden- se eliminan mas abajo, en el paso 7, junto con el resto de
--    datos de prueba)
-- ---------------------------------------------------------------------
INSERT INTO inventario.rubro (Descripcion)
SELECT p.Descripcion
FROM berdez_prod_real.rubro p
WHERE NOT EXISTS (
  SELECT 1 FROM inventario.rubro d WHERE LOWER(TRIM(d.Descripcion)) = LOWER(TRIM(p.Descripcion))
);

-- ---------------------------------------------------------------------
-- 6. MATERIALES
--    6a. Actualizar los 273 emparejados con el dato real de Produccion.
--        CostoPromedio se resetea a NULL (no hay costo real conocido).
-- ---------------------------------------------------------------------
UPDATE inventario.material d
JOIN berdez_prod_real.material p
  ON LOWER(TRIM(REPLACE(d.Descripcion,'"',''))) = LOWER(TRIM(REPLACE(p.Descripcion,'"','')))
SET d.Codigo = p.Codigo,
    d.Saldo = p.Saldo,
    d.Min_Almacen = p.Min_Almacen,
    d.Max_Casa = p.Max_Casa,
    d.CostoPromedio = NULL;

-- 6b. Insertar los 16 materiales nuevos y limpios de Produccion (Codigo 356-371)
INSERT INTO inventario.material (Codigo, Descripcion, Unidad, Saldo, Min_Almacen, Max_Casa, CostoPromedio)
SELECT p.Codigo, p.Descripcion, p.Unidad, p.Saldo, p.Min_Almacen, p.Max_Casa, NULL
FROM berdez_prod_real.material p
WHERE p.Codigo BETWEEN 356 AND 371
  AND NOT EXISTS (
    SELECT 1 FROM inventario.material d
    WHERE LOWER(TRIM(REPLACE(d.Descripcion,'"',''))) = LOWER(TRIM(REPLACE(p.Descripcion,'"','')))
  );

-- 6c. Conflicto "Gancho tipo C" (Codigo 26 y 29): Produccion es la fuente de
--     verdad del dato -> se sobrescribe nombre y saldo de Desarrollo.
UPDATE inventario.material
SET Descripcion = 'Gancho tipo C de 1/4 20x8x8', Codigo = 26,
    Saldo = 6880, Min_Almacen = 0, Max_Casa = 0, CostoPromedio = NULL
WHERE ID = 43;

UPDATE inventario.material
SET Descripcion = 'Gancho tipo C de 1/4 10x8x8', Codigo = 29,
    Saldo = 6790, Min_Almacen = 0, Max_Casa = 0, CostoPromedio = NULL
WHERE ID = 46;

-- ---------------------------------------------------------------------
-- 7. LIMPIEZA DE DATOS DE PRUEBA (confirmado: ninguno tiene respaldo en
--    Produccion, se elimina todo). Orden de dependencias FK: primero las
--    tablas hijas, luego las tablas padre.
-- ---------------------------------------------------------------------

-- 7a. Exportar auditoria antes de vaciar (hacer con mysqldump aparte, fuera
--     de esta transaccion, antes de correr este script):
--     mysqldump -u root inventario auditoria > backup_auditoria_pruebas.sql
DELETE FROM inventario.auditoria;

-- 7b. Solicitudes de prueba
DELETE FROM inventario.solicitud_detalle;
DELETE FROM inventario.solicitud;

-- 7c. Ajustes de inventario de prueba
DELETE FROM inventario.ajuste_inventario;

-- 7d. Historial de movimientos de prueba (41 entradas / 64 salidas)
DELETE FROM inventario.material_registro_salidas;
DELETE FROM inventario.registro_salidas;
DELETE FROM inventario.material_registro_entradas;
DELETE FROM inventario.registro_entradas;

-- 7e. Usuario de prueba
DELETE FROM inventario.usuario WHERE Identificacion = 123456789;

-- 7f. Proyecto de prueba (CC san pablo) - ya no tiene ubicaciones ni
--     movimientos dependientes tras los pasos anteriores
DELETE FROM inventario.proyecto WHERE LOWER(TRIM(Descripcion)) = 'cc san pablo';

-- 7g. Proveedor de prueba
DELETE FROM inventario.proveedor WHERE LOWER(TRIM(Descripcion)) = 'andamios del sur';

-- 7h. Rubros de prueba
DELETE FROM inventario.rubro WHERE Descripcion IN ('Cajilla', 'Anden');

-- 7i. Materiales de prueba (21 IDs sin ningun respaldo en Produccion)
DELETE FROM inventario.material_almacen WHERE MaterialID IN
  (1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21);
DELETE FROM inventario.material WHERE ID IN
  (1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21);

-- 7j. Nodos de `ubicacion` de prueba (198 filas: Torre/Piso/Apartamento y el
--     esqueleto generico Etapa1/M10-M11/casa1-3 repetido en los 3 proyectos
--     reales, que no corresponde a ninguna combinacion real de Produccion)
DELETE FROM inventario.ubicacion WHERE ProyectoID IN (1,2,3,57);

-- ---------------------------------------------------------------------
-- 8. RECONSTRUIR `ubicacion` CON LAS COMBINACIONES REALES DE PRODUCCION
--    Jerarquia: Proyecto -> Etapa -> Manzana -> Casa/Urbanismo.
--    Caso especial "Urbanismo"/"Traspaso" (confirmado): en vez de forzar una
--    jerarquia Etapa/Manzana/Casa para lo que en realidad es un casillero
--    generico de traspaso interno, se crea UN SOLO nodo raiz "Traspaso" por
--    proyecto (mismo nivel que las Etapas) y los movimientos genericos se
--    asignan directamente ahi.
-- ---------------------------------------------------------------------

-- 8a. Nodo raiz "Traspaso" por cada proyecto real que tenga movimientos
--     genericos (Etapa/Manzana/Casa = Urbanismo o Traspaso en cualquier
--     combinacion)
INSERT INTO inventario.ubicacion (ProyectoID, PadreID, Nombre, Tipo, Nivel, Activo)
SELECT DISTINCT c.proyecto_dev_id, NULL, 'Traspaso', 'TRASPASO', 0, 1
FROM berdez_migracion_staging.combos_ubicacion c
WHERE c.proyecto_dev_id IS NOT NULL
  AND (c.etapa = 'Traspaso' OR c.manzana IN ('Traspaso','Urbanismo') OR c.casa = 'Urbanismo')
  AND NOT EXISTS (
    SELECT 1 FROM inventario.ubicacion u
    WHERE u.ProyectoID = c.proyecto_dev_id AND u.PadreID IS NULL AND LOWER(u.Nombre) = 'traspaso'
  );

-- 8b. Nodos de Etapa reales (Nivel 0) - excluye las combinaciones genericas,
--     que ya fueron resueltas en 8a
INSERT INTO inventario.ubicacion (ProyectoID, PadreID, Nombre, Tipo, Nivel, Activo)
SELECT DISTINCT c.proyecto_dev_id, NULL, c.etapa, 'ETAPA', 0, 1
FROM berdez_migracion_staging.combos_ubicacion c
WHERE c.proyecto_dev_id IS NOT NULL
  AND NOT (c.etapa = 'Traspaso' OR c.manzana IN ('Traspaso','Urbanismo') OR c.casa = 'Urbanismo')
  AND NOT EXISTS (
    SELECT 1 FROM inventario.ubicacion u
    WHERE u.ProyectoID = c.proyecto_dev_id AND u.PadreID IS NULL AND LOWER(u.Nombre) = LOWER(c.etapa)
  );

-- 8c. Nodos de Manzana reales (Nivel 1, hijos de su Etapa)
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
  );

-- 8d. Nodos de Casa reales (Nivel 2, hijos de su Manzana). El caso Casa 4 /
--     Etapa 4 (Ciudadela, Manzana K) se conserva tal cual segun lo indicado
--     por el usuario: no se corrige el dato historico, queda como un nodo
--     mas bajo Etapa 4 -> K -> Casa 4, aunque solo tenga un movimiento.
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
  );

-- 8e. Pendiente: el proyecto "Alcala" (recien creado en el paso 2) solo tiene
--     movimientos genericos ("Traspaso/Traspaso/Urbanismo"), que hoy no
--     aparecen en `combos_ubicacion` porque esa tabla se genero antes de
--     crear el proyecto en Desarrollo. Antes de correr los pasos 8a-8d hay
--     que regenerar `combos_ubicacion` (o agregar una fila manual) para que
--     incluya el nuevo proyecto_dev_id de Alcala; entonces el paso 8a le
--     crea su propio nodo raiz "Traspaso" igual que a los demas.

-- ---------------------------------------------------------------------
-- 9. SINCRONIZAR material_almacen CON EL Saldo FINAL (un solo almacen activo)
-- ---------------------------------------------------------------------
INSERT INTO inventario.material_almacen (MaterialID, AlmacenID, Saldo)
SELECT m.ID, 1, m.Saldo
FROM inventario.material m
WHERE NOT EXISTS (
  SELECT 1 FROM inventario.material_almacen ma WHERE ma.MaterialID = m.ID AND ma.AlmacenID = 1
)
ON DUPLICATE KEY UPDATE Saldo = VALUES(Saldo);

UPDATE inventario.material_almacen ma
JOIN inventario.material m ON m.ID = ma.MaterialID
SET ma.Saldo = m.Saldo
WHERE ma.AlmacenID = 1 AND ma.Saldo <> m.Saldo;

-- ---------------------------------------------------------------------
-- 10. REGISTRO DE ENTRADAS (79 encabezados, 268 lineas)
--     ProveedorID y CostoUnitario quedan NULL (Produccion no los tenia).
--     MySQL no permite capturar LAST_INSERT_ID() fila a fila dentro de un
--     INSERT...SELECT masivo, asi que este paso se ejecuta con un script PHP
--     de una sola vez (reutilizando Model/RegistroEntradas.php), no con SQL
--     puro. Pseudocodigo:
--
--   foreach (fila in berdez_prod_real.registro_entradas ordenado por ID):
--     usuario_dev_id = inventario.usuario.ID donde Identificacion coincide
--                      (via map_usuario) con el UsuarioID de la fila
--     INSERT INTO inventario.registro_entradas (Fecha,Hora,UsuarioID,ProveedorID)
--       VALUES (fila.Fecha, fila.Hora, usuario_dev_id, NULL)
--     dev_id = LAST_INSERT_ID()
--     foreach (linea in berdez_prod_real.material_registro_entradas WHERE Registro_EntradasID = fila.ID):
--       material_dev_id = inventario.material.ID por Descripcion normalizada
--       destino_dev_id  = inventario.destino.ID por Descripcion normalizada
--       INSERT INTO inventario.material_registro_entradas
--         (MaterialID, Registro_EntradasID, Cantidad, DestinoID, CostoUnitario)
--         VALUES (material_dev_id, dev_id, linea.Cantidad, destino_dev_id, NULL)
-- ---------------------------------------------------------------------

-- ---------------------------------------------------------------------
-- 11. REGISTRO DE SALIDAS (63 encabezados, 462 lineas)
--     Mismo criterio que el paso 10: script PHP de una sola vez. UbicacionID
--     se resuelve asi:
--       - Si (Etapa/Manzana/Casa) de la linea es "Traspaso"/"Urbanismo" en
--         cualquier combinacion -> usar el nodo raiz "Traspaso" del proyecto
--         (creado en el paso 8a).
--       - En caso contrario -> navegar Proyecto -> Etapa -> Manzana -> Casa
--         ya reconstruido en los pasos 8b-8d.
--     ContratistaID, ProyectoID, RubroID, DestinoID se resuelven por
--     Descripcion normalizada contra sus tablas ya migradas.
-- ---------------------------------------------------------------------

-- === VALIDACIONES (correr ANTES del COMMIT; si alguna falla, ROLLBACK) ===

-- V1. Los 3 usuarios reales de Produccion existen en Desarrollo
SELECT COUNT(*) AS v1_usuarios_reales FROM inventario.usuario
WHERE Identificacion IN (1004035010, 83235047, 1070605738); -- esperado: 3

-- V2. El usuario de prueba ya no existe
SELECT COUNT(*) AS v2_pedro_perez FROM inventario.usuario WHERE Identificacion = 123456789; -- esperado: 0

-- V3. Todo usuario tiene un RolID valido
SELECT COUNT(*) AS v3_roles_invalidos FROM inventario.usuario u
LEFT JOIN inventario.rol r ON r.ID = u.RolID WHERE r.ID IS NULL; -- esperado: 0

-- V4. rol_permiso no cambio de tamaño (35 filas, sin tocar)
SELECT COUNT(*) AS v4_rol_permiso FROM inventario.rol_permiso; -- esperado: 35

-- V5. Los 273 materiales emparejados quedaron con el Saldo real de Produccion
--     (recalcular sobre map_material, ya que los 2 Gancho ahora tambien deben coincidir)
SELECT COUNT(*) AS v5_saldo_no_coincide FROM inventario.material d
JOIN berdez_prod_real.material p
  ON LOWER(TRIM(REPLACE(d.Descripcion,'"',''))) = LOWER(TRIM(REPLACE(p.Descripcion,'"','')))
WHERE d.Saldo <> p.Saldo; -- esperado: 0

-- V6. material_almacen sincronizado con material.Saldo
SELECT COUNT(*) AS v6_almacen_desincronizado FROM inventario.material m
JOIN inventario.material_almacen ma ON ma.MaterialID = m.ID AND ma.AlmacenID = 1
WHERE ma.Saldo <> m.Saldo; -- esperado: 0

-- V7. No quedan nodos de ubicacion de prueba
SELECT COUNT(*) AS v7_nodos_prueba FROM inventario.ubicacion
WHERE Tipo IN ('Torre','Piso','Apartamento','Local'); -- esperado: 0

-- V8. No quedan los 21 materiales de prueba ni los datos de prueba dependientes
SELECT COUNT(*) AS v8_materiales_prueba FROM inventario.material WHERE ID IN
  (1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21); -- esperado: 0
SELECT COUNT(*) AS v8_proyecto_prueba FROM inventario.proyecto WHERE LOWER(TRIM(Descripcion)) = 'cc san pablo'; -- esperado: 0
SELECT COUNT(*) AS v8_proveedor_prueba FROM inventario.proveedor WHERE LOWER(TRIM(Descripcion)) = 'andamios del sur'; -- esperado: 0
SELECT COUNT(*) AS v8_rubros_prueba FROM inventario.rubro WHERE Descripcion IN ('Cajilla','Anden'); -- esperado: 0

-- V9. Sin duplicados de Descripcion en material
SELECT Descripcion, COUNT(*) c FROM inventario.material
GROUP BY LOWER(TRIM(Descripcion)) HAVING c > 1; -- esperado: 0 filas

-- V10. El proyecto Alcala y el contratista Amin Narvaez (renombrado) existen
SELECT COUNT(*) AS v10_alcala FROM inventario.proyecto WHERE LOWER(TRIM(Descripcion)) = 'alcala'; -- esperado: 1
SELECT COUNT(*) AS v10_amin FROM inventario.contratista WHERE Descripcion = 'Amin Narvaez'; -- esperado: 1

-- Si TODAS las validaciones anteriores pasan:
-- COMMIT;
-- Si alguna falla:
-- ROLLBACK;

-- === ROLLBACK MANUAL (si ya se hizo COMMIT y se detecta un problema despues) ===
-- 1. Restaurar desde el backup tomado en el paso 0:
--    mysql -u root inventario < backup_inventario_pre_migracion_YYYYMMDD.sql
-- 2. Verificar con las mismas validaciones de arriba que el estado restaurado
--    es el que existia antes de la migracion.
