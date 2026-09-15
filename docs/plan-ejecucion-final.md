# Plan de Ejecución Final — Migración Producción → Desarrollo

**Estado: EJECUTADO Y CONFIRMADO (COMMIT) el 2026-09-01.** Backup previo en `backups/backup_inventario_pre_migracion_20260901_105519.sql`. Script ejecutado: `scripts/ejecutar_migracion.php`. Todas las validaciones automáticas pasaron antes del `COMMIT`; ver el resumen de resultados al final de este documento. Producción no fue tocada en ningún momento (todo el acceso a `berdez_prod_real` fue de solo lectura).

Este documento complementa (no reemplaza) `docs/plan-actualizacion-bd-produccion.md`, que sigue siendo válido para las reglas de negocio, el árbol de decisiones y los riesgos. Aquí están los **entregables 1–6** pedidos: matrices de mapeo con datos reales, lista de conflictos, lista de datos de prueba, el script de migración completo, el rollback y las validaciones.

**No ejecutar nada de este documento hasta recibir "APROBADO, EJECUTA".**

---

## 0. Corrección importante sobre el análisis anterior

El documento previo comparó los materiales usando una copia retipeada a mano del dump de Producción, y esa retipeada eliminó por error las comillas de pulgada (`"`) de las descripciones (`varilla corrugada de 1/4" * 6 mt` quedó como `...1/4 * 6 mt`). Eso hizo que la comparación fallara para decenas de materiales que en realidad sí coinciden. Esta vez se cargó el **dump verbatim, sin retiparlo**, en un esquema aislado (`berdez_prod_real`, en el mismo servidor MySQL de Desarrollo, sin tocar `inventario`), y se comparó con SQL real. Los números correctos son:

| | Documento anterior (incorrecto) | Real (verificado ahora) |
|---|---|---|
| Materiales de Producción con match en Desarrollo | 103 | **273 de 291** |
| Materiales solo en Desarrollo (sin match) | 193 | **23 de 296** |
| Materiales solo en Producción (faltan en Desarrollo) | 188 | **18 de 291** (16 limpios + 2 en conflicto, ver §3) |

Esto reduce drásticamente el trabajo de revisión manual de la sección 9.1 original: de "193 materiales por clasificar uno por uno" pasamos a "21 materiales de prueba con veredicto claro + 2 casos puntuales de conflicto real". Ver la matriz completa en `berdez_migracion_staging.map_material` (tabla construida en este análisis, ver §7).

**Segunda corrección, sobre `ubicacion`:** el documento anterior asumía que los 4 nodos "Etapa 1 / M10 o M11 / casa 1 o 3" que ya existían en Desarrollo bajo los proyectos reales 1 y 3 eran un intento previo de migración real, y recomendaba completarlos. Al revisar ahora **los tres proyectos reales a la vez**, se ve que los tres tienen exactamente el mismo esqueleto genérico repetido (`Etapa 1` → `M10`/`M11` → `casa 1`/`casa 3`), que **no coincide con ninguna combinación real de Producción** para esos proyectos (Producción usa `ETAPA 4/5/6` y manzana `K` para el proyecto 1, `ETAPA 6`/`M10` para el proyecto 2, y `Traspaso/Traspaso` para el proyecto 3-mapeado-a-"Local D1"). Es decir: **es un fixture de prueba repetido manualmente al probar el CRUD de Ubicación en cada proyecto, no un dato real parcialmente migrado.** Corrección: estos nodos entran también en la lista de limpieza (§4), igual que los de Torre/Piso/Apartamento.

---

## 1. Entregable — Matrices de mapeo (con datos reales)

Todas las tablas siguientes existen físicamente y son consultables en el esquema `berdez_migracion_staging` (creado en este análisis, vive junto a `inventario` en el mismo MySQL local, no la toca). Resumen:

### 1.1 `map_usuario`

| Identificacion | Nombre Producción | Rol prod | dev_id | Rol dev | Confianza | Acción |
|---|---|---|---|---|---|---|
| 1004035010 | Juan Lopez | Administrador | 1 | Administrador | ALTA | Ya existe, no tocar clave |
| 83235047 | Reinaldo Gordo Losada | Almacenista | *(ninguno)* | — | NUEVO | Crear, clave rehasheada |
| 1070605738 | Caterine Fernanda Jovel Rincon | Almacenista | *(ninguno)* | — | NUEVO | Crear, clave rehasheada |

### 1.2 `map_proyecto`

| prod_id | Producción | dev_id | Desarrollo | Confianza |
|---|---|---|---|---|
| 1 | Ciudadela Salamanca La Nueva | 3 | Ciudadela salamanca la nueva | ALTA |
| 2 | Salamanca la Nueva - conjunto residencial | 1 | Salamanca la nueva conjunto residencial | ALTA |
| 3 | Local D1 | 2 | Tienda D1 | MEDIA — confirmar |
| 4 | Alcala | *(ninguno)* | — | NUEVO |
| — | — | 57 | CC san pablo | AMBIGUA — sin equivalente en Producción |

### 1.3 `map_contratista`

| prod_id | Producción | dev_id | Desarrollo | Confianza |
|---|---|---|---|---|
| 2 | Hermanos Murcia | 1 | Construcciones hermanos murcia | ALTA |
| 3 | Smartools | 2 | Smartools | ALTA |
| 1 | Amin Narvaez | 3 | Construcciones narvaez camacho | BAJA — confirmar |
| 4 | Sendos | *(ninguno)* | — | NUEVO |
| 5 | Grabiel cardona | *(ninguno)* | — | NUEVO |

### 1.4 `map_destino` — 5 de 9 ya existen (ALTA), faltan 4 (Cubierta, Tuberías, Vías/andenes/sardineles, Accesorios) → NUEVO.

### 1.5 `map_rubro` — 0 de 35 existen en Desarrollo con ese nombre → los 35 son NUEVO (los 2 rubros actuales de Desarrollo, "Cajilla"/"Anden", no tienen equivalente y se tratan en §4).

### 1.6 `map_material` — 291 filas, resumen:

- **273 ALTA** (match confiable por descripción normalizada): se actualiza `Codigo`, `Saldo`, `Min_Almacen`, `Max_Casa` desde Producción.
- **18 NUEVO**: no existen en Desarrollo, se insertan. De estos, **16 son inserciones limpias** (Codigo 356–371, el rango más alto de Producción — evidencia de que se agregaron a Producción *después* de que se copiara el catálogo a Desarrollo por primera vez) y **2 están en conflicto** (ver §3).

---

## 2. Entregable — Ubicación: combinaciones reales (`combos_ubicacion`)

25 combinaciones hoja reales (24 vivienda/urbanismo + 1 sin proyecto mapeado), ya resueltas contra el `dev_id` de proyecto:

| proyecto_dev_id | Proyecto Desarrollo | Etapa | Manzana | Casa | Movimientos |
|---|---|---|---|---|---|
| 1 | Salamanca la nueva conjunto residencial | ETAPA 6 | Manzana M10 | casa 1 | 34 |
| 1 | ídem | ETAPA 6 | Manzana M10 | casa 2 | 37 |
| 1 | ídem | ETAPA 6 | Manzana M10 | casa 3 | 38 |
| 1 | ídem | ETAPA 6 | Manzana M10 | casa 4 | 37 |
| 1 | ídem | ETAPA 6 | Manzana M10 | casa 5 | 29 |
| 1 | ídem | ETAPA 6 | Manzana M10 | Casa 6 | 52 |
| 1 | ídem | ETAPA 6 | Manzana M10 | Casa 7 | 1 |
| 1 | ídem | ETAPA 6 | Manzana M10 | Casa 8 | 2 |
| 1 | ídem | ETAPA 6 | Urbanismo | Urbanismo | 37 |
| 2 | Tienda D1 | Traspaso | Traspaso | Casa 6 | 8 |
| 3 | Ciudadela salamanca la nueva | ETAPA 4 | K | casa 4 | 1 *(anomalía, ver plan original §3.5)* |
| 3 | ídem | ETAPA 5 | K | casa 3 | 16 |
| 3 | ídem | ETAPA 5 | K | casa 4 | 16 |
| 3 | ídem | ETAPA 5 | K | casa 5 | 16 |
| 3 | ídem | ETAPA 5 | K | Casa 6 | 18 |
| 3 | ídem | ETAPA 5 | K | Casa 7 | 16 |
| 3 | ídem | ETAPA 5 | K | Casa 22 | 16 |
| 3 | ídem | ETAPA 5 | K | Casa 23 | 16 |
| 3 | ídem | ETAPA 5 | K | Casa 24 | 16 |
| 3 | ídem | ETAPA 5 | K | Casa 25 | 15 |
| 3 | ídem | ETAPA 5 | K | Casa 26 | 15 |
| 3 | ídem | ETAPA 5 | K | Casa 27 | 16 |
| 3 | ídem | ETAPA 6 | Urbanismo | Urbanismo | 4 |
| 3 | ídem | Traspaso | Traspaso | Casa 6 | 2 |
| 3 | ídem | Traspaso | Urbanismo | Urbanismo | 1 |
| **(sin mapear)** | Producción "Alcala" (prod_id 4, sin dev_id) | Traspaso | Traspaso | Urbanismo | 3 |

La última fila queda resuelta con la decisión #6 de la tabla siguiente (se crea "Alcala" en Desarrollo). Nota técnica: como `combos_ubicacion` se generó antes de crear ese proyecto, hay que regenerar esa tabla (o agregar la fila a mano) para que el paso 8 del script le cree su nodo raíz "Traspaso" — ver comentario en `scripts/migracion_produccion_a_desarrollo.sql` paso 8e.

---

## 3. Entregable — Conflictos: RESUELTOS

Todos los conflictos fueron revisados y resueltos por el usuario. Principio general aplicado a partir de aquí: **Producción es la única fuente de verdad de los datos — cualquier dato que exista solo en Desarrollo, sin respaldo en Producción, se elimina.** Esto no afecta el esquema/modelo de Desarrollo (tablas, permisos, estructura de `ubicacion`), que se conserva intacto tal como establecen las reglas absolutas del encargo.

| # | Conflicto | Resolución | Reflejado en el script |
|---|---|---|---|
| 1 | Codigo=26 reutilizado (Gancho tipo C) | Gana Producción: nombre `Gancho tipo C de 1/4 20x8x8`, saldo 6880 | Paso 6c |
| 2 | Codigo=29 reutilizado (Gancho tipo C) | Gana Producción: nombre `Gancho tipo C de 1/4 10x8x8`, saldo 6790 | Paso 6c |
| 3 | Proyecto "Local D1" vs "Tienda D1" | Es el mismo proyecto — los movimientos se migran a "Tienda D1" | Paso 2 (sin inserción nueva) |
| 4 | Contratista "Amin Narvaez" vs "Construcciones narvaez camacho" | Es el mismo contratista; gana el nombre de Producción — se renombra el registro de Desarrollo | Paso 3 |
| 5 | Proyecto "CC san pablo" (solo Desarrollo) | No existe en Producción → se elimina | Paso 7f |
| 6 | Proyecto "Alcala" (solo Producción) | Se crea en Desarrollo | Paso 2 |
| 7 | Rol "Almacenista" → "Almacenero" | Confirmado, mismo rol | Paso 1 (uso directo del RolID) |
| 8 | Usuario de prueba "Pedro Perez" | No existe en Producción → se elimina | Paso 7e |
| 9 | 21 materiales de prueba solo en Desarrollo | No existen en Producción → se eliminan | Paso 7i |
| 10 | Nodos `ubicacion` de prueba (198 filas) | No existen en Producción → se eliminan y se reconstruye el árbol desde cero | Pasos 7j y 8 |
| 11 | Auditoría histórica de prueba (81 filas) | No existe en Producción → se elimina (con exportación previa como respaldo) | Paso 7a |
| 12 | Proveedor "Andamios del sur" (solo Desarrollo) | No existe en Producción → se elimina | Paso 7g |
| 13 | Rubros "Cajilla"/"Anden" (prueba, solo Desarrollo) | No existen en Producción → se eliminan | Paso 7h |
| 14 | "Urbanismo"/"Traspaso" — cómo modelarlos en `ubicacion` | Un nodo raíz único "Traspaso" por proyecto (mismo nivel que las Etapas), sin manzana/casa debajo; los movimientos genéricos se asignan ahí directamente | Paso 8a |
| 15 | Casa 4 / Etapa 4 (anomalía, 1 movimiento vs 16 en Etapa 5) | Se conserva tal cual en Producción, sin corregir el dato histórico | Paso 8d (sin tratamiento especial) |

---

## 4. Entregable — Lista exacta de datos de prueba candidatos a eliminación

### 4.1 Materiales (21 de los 23 "solo Desarrollo"; los otros 2 son los conflictos §3.1/3.2, no se tocan sin decisión)

```
ID  Codigo  Descripcion                                    Saldo
1   1022    cemento                                        1
2   69      tubo liso de 6 sanitario                       219
3   87      union pvc de 2 sanitario                       81
4   89      tee pvc de 2 sanitario                         148
5   90      union pvc de 1 1/2 sanitario                   721
6   92      semicodo pvc de 2 sanitario                    490
7   102     varilla corrugada de 1/2 x 6 mts                1675
8   108     malla electrosoldada # 6.5mm                   384
9   191     tee sanitaria de 4                              554
10  192     adaptador limpieza de 4                         216
11  196     buje de 2 x 1 1/2 presion                       4
12  209     espatula metalica de 3                          11
13  236     tee presion de 4                                10
14  267     eucobar x 190 kilos x 55 galones                1
15  268     buje sanitario de 4x3                           109
16  270     union pvc de 3 presion                          27
17  295     pintura vinilo blanco                           4
18  297     union sanitaria de 6                            50
19  301     cemento blanco x 40 kilos                       7
20  321     medio polin x 3 mts                             100
21  324     tapa prueba de 2                                110
```

### 4.2 Ubicaciones (198 filas — todas las de los proyectos 1, 2, 3, 57 excepto las nuevas que se crearán en §5)

```
ProyectoID  Tipo         Cantidad  Rango de ID
1           Apartamento  151       6618–6796
1           casa         3         6–18
1           etapa        2         4–19
1           manzana      2         5–7
1           Piso         26        6617–6646
1           Torre        2         4577–6621
1           urbanismo    1         6615
2           casa         1         11
2           etapa        1         9
2           Local        1         6611
2           manzana      1         10
3           casa         2         14–16
3           etapa        1         12
3           manzana      2         13–15
57          Local        1         6798
57          Piso         1         6797
```

Dependencia detectada: **50 filas de `material_registro_salidas`** (de prueba) referencian estos `UbicacionID`. Se deben limpiar junto con el resto del historial de prueba de salidas (ya identificado en el plan original §14), en ese orden, antes de borrar los nodos de `ubicacion`.

### 4.3 Resto de datos de prueba — sin cambios respecto al plan original §14 (usuario Pedro Perez, 64 salidas / 41 entradas de prueba, 3 solicitudes, 5 ajustes, 81 filas de auditoría, rubros "Cajilla"/"Anden", proveedor "Andamios del sur" y proyecto "CC san pablo" quedan **pendientes de tu decisión**, no de un veredicto automático).

---

## 5. Entregable — Script de migración (completo, revisable, NO ejecutado)

El script vive en [`scripts/migracion_produccion_a_desarrollo.sql`](../scripts/migracion_produccion_a_desarrollo.sql). Estructura:

1. Requiere que exista el esquema `berdez_prod_real` (el dump verbatim de Producción cargado durante este análisis) y `berdez_migracion_staging` (las tablas de mapeo ya construidas) en el mismo servidor MySQL, junto a `inventario`.
2. Usa `INSERT ... SELECT` con `JOIN` a las tablas de mapeo — no usa IDs literales para las FK, así que es correcto aunque los `AUTO_INCREMENT` de Desarrollo avancen entre el momento de escribir el script y el de ejecutarlo.
3. Todo dentro de una única transacción (`START TRANSACTION` … `COMMIT`), con los `SELECT` de validación de la sección 6 corriendo **antes del `COMMIT` final**, de modo que un `ROLLBACK` sea automático si algo no cuadra (ver comentarios en el propio script).
4. Los pasos marcados `-- REQUIERE DECISION` están comentados (`-- `) y no se ejecutan aunque se corra el script completo, hasta que se resuelva cada conflicto de la sección 3 y se descomenten a mano.
5. Las contraseñas de Reinaldo y Caterine ya vienen como hash bcrypt precalculado (ver el script) — el cálculo del hash no modifica ninguna base de datos, se hizo aparte con `password_hash()` en PHP, igual que lo haría `UsuarioController::store()`.

**No se ejecutó.** Está listo para revisión.

---

## 6. Entregable — Validaciones y rollback

Incluidas al final del mismo archivo `scripts/migracion_produccion_a_desarrollo.sql`, en dos secciones separadas (`-- === VALIDACIONES ===` y `-- === ROLLBACK ===`), más las ya listadas en `docs/plan-actualizacion-bd-produccion.md` §17 y §20 (siguen vigentes, no se repiten aquí).

Adicional específico de esta corrección:

```sql
-- Confirmar que los 273 materiales emparejados quedaron con el Saldo real de Producción
SELECT COUNT(*) FROM inventario.material d
JOIN berdez_migracion_staging.map_material mp ON mp.dev_id = d.ID
WHERE mp.confianza = 'ALTA' AND d.Saldo <> mp.prod_saldo;
-- esperado: 0 (después de ejecutar la migración)

-- Confirmar que no quedó ningún nodo de ubicacion de prueba
SELECT COUNT(*) FROM inventario.ubicacion WHERE Tipo IN ('Torre','Piso','Apartamento','Local');
-- esperado: 0 (salvo que "CC san pablo" se decida conservar con su nodo "Local")
```

---

## 7. Dónde queda todo esto para tu revisión

- `berdez_prod_real` — el dump de Producción, cargado tal cual, sin tocar `inventario`. Solo lectura.
- `berdez_migracion_staging` — las tablas `map_usuario`, `map_proyecto`, `map_contratista`, `map_destino`, `map_rubro`, `map_material`, `material_solo_desarrollo`, `combos_ubicacion`. Puedes consultarlas directamente con cualquier cliente MySQL apuntando a `localhost` (mismo servidor de `inventario`) para revisar fila por fila antes de aprobar.
- `docs/plan-actualizacion-bd-produccion.md` — el análisis original completo (sigue vigente salvo la corrección de la sección 0 de este documento).
- `docs/plan-ejecucion-final.md` — este documento.
- `scripts/migracion_produccion_a_desarrollo.sql` — el script completo, no ejecutado.

Ambos esquemas temporales (`berdez_prod_real`, `berdez_migracion_staging`) son inertes: no son referenciados por el código de la aplicación (`connection.php` apunta únicamente a `inventario`) y se pueden borrar en cualquier momento sin ningún efecto sobre el sistema. Los dejo creados a propósito para que puedas revisarlos antes de decidir.

---

## Estado: todos los conflictos resueltos, pendiente tu aprobación de ejecución

Los 15 puntos de la sección 3 quedaron resueltos (ver tabla) y ya están reflejados en `scripts/migracion_produccion_a_desarrollo.sql`, pasos 1–9 (catálogos, limpieza de datos de prueba, reconstrucción de `ubicacion`). Un detalle técnico queda anotado en el paso 8e del script: como la tabla `combos_ubicacion` se generó antes de crear el proyecto "Alcala", hay que regenerarla (o agregar la fila a mano) para que sus 3 movimientos de traspaso genérico entren en el mismo tratamiento del paso 8a — lo hago en el momento de ejecutar, no requiere una decisión nueva tuya.

Los pasos 10–11 (replay de las 79 entradas / 268 líneas y 63 salidas / 462 líneas históricas) todavía no tienen script — se escriben como un script PHP de una sola vez justo antes de ejecutar, reutilizando los mismos criterios de mapeo ya validados aquí, para no tener que rehacerlos si algo cambia entre ahora y el momento de correrlo.

## Resultado de la ejecución (2026-09-01)

Aprobado por el usuario con "APROBADO, EJECUTA". Se tomó el backup, se escribió `scripts/ejecutar_migracion.php` (cubre los pasos 1–11 completos, incluido el replay de movimientos que en el script `.sql` estático había quedado como pseudocódigo), y se ejecutó dentro de una única transacción PDO.

**Dos problemas aparecieron durante la ejecución y se corrigieron antes del `COMMIT` final** (la transacción hizo `ROLLBACK` automático en cada intento fallido, sin dejar nada a medias):

1. Un `DELETE` sobre `ubicacion` que mezclaba niveles en un solo lote violaba la FK autorreferenciada `PadreID` (borrar un padre antes que su hijo dentro del mismo `DELETE`). Se corrigió borrando por `Nivel` descendente (hijos antes que padres).
2. Un material de Producción, "Tuberia pvc pres**ió**n de 1/2\"..." (tilde aguda), no calzaba contra su equivalente real en Desarrollo, "Tuberia pvc pres**ò**n..." (tilde **grave** — un typo real, preexistente, en los datos de Desarrollo). La comparación SQL con `LOWER(TRIM())` los daba por iguales porque la collation `utf8mb4_general_ci` trata esas variantes como equivalentes al ordenar, pero no son el mismo texto a nivel de caracter. Se corrigió agregando `Descripcion = Produccion.Descripcion` a la actualización de los 273 materiales emparejados (consistente con la regla "gana Producción" ya aplicada a los conflictos de "Gancho tipo C"), lo que de paso corrige ese typo y cualquier otro similar que hubiera pasado desapercibido.

**Validaciones finales, todas OK:**

| Tabla | Resultado |
|---|---|
| `usuario` | 3 (Juan, Reinaldo, Caterine — Pedro Perez eliminado) |
| `proyecto` | 4 (Alcala creado, CC san pablo eliminado) |
| `contratista` | 5 (Amin Narvaez renombrado, Sendos y Grabiel cardona nuevos) |
| `destino` | 9 |
| `rubro` | 35 |
| `material` | 291 (273 actualizados + 16 nuevos + 2 conflictos de Gancho resueltos; los 21 de prueba eliminados) |
| `ubicacion` | 30 nodos reales (incluye 4 nodos raíz "Traspaso", uno por proyecto con movimientos genéricos, y el caso Casa 4/Etapa 4 preservado como nodo separado de Casa 4/Etapa 5) |
| `registro_entradas` / líneas | 79 / 268 (coincide exacto con Producción) |
| `registro_salidas` / líneas | 63 / 462 (coincide exacto con Producción, `UbicacionID` resuelto en el 100% de las líneas) |
| `auditoria`, `solicitud`, `ajuste_inventario` | 0 (datos de prueba eliminados; auditoría exportada antes a `backups/auditoria_pruebas_export.json`) |
| `material_almacen` | Saldo sincronizado 1:1 con `material.Saldo` en las 291 filas |
| Contraseñas | `password_verify()` confirmado para Reinaldo y Caterine con sus claves reales de Producción |

Los esquemas `berdez_prod_real` y `berdez_migracion_staging` siguen existiendo en el servidor MySQL (no afectan a la aplicación, que solo usa `inventario`) por si querés revisarlos; se pueden borrar cuando quieras con `DROP DATABASE`.

## Corrección posterior (2026-09-01, mismo día): dos problemas reportados por el usuario

Backup adicional: `backups/backup_inventario_antes_correccion_20260901_112303.sql`. Script: `scripts/corregir_migracion.php`.

**Problema 1 — "Salida No." no coincidía con Producción.** `registro_entradas`/`registro_salidas` (y sus líneas) habían quedado con IDs correlativos nuevos (ej. 89–151) en vez de conservar el mismo número que en Producción (1–63 para salidas, 1–79 para entradas), porque el `AUTO_INCREMENT` de Desarrollo venía arrastrando el conteo de los datos de prueba ya borrados. Como esas columnas se muestran directamente al usuario como "Salida No."/"Entrada No." (ver `SalidaMaterialPDF.php`), esto rompía la continuidad con los comprobantes físicos ya entregados. **Corregido**: se truncaron las 4 tablas de movimientos y se reinsertaron con el mismo `ID` exacto que tienen en Producción. Verificado: `registro_entradas` ID 1–79, `registro_salidas` ID 1–63, sin huérfanos de FK en ninguna de las 12 relaciones revisadas.

**Nota técnica importante**: `TRUNCATE` en MySQL genera un *commit implícito* — cierra la transacción en curso automáticamente, sin avisar. Esto significa que la protección de "todo o nada" que se había diseñado para este paso no funcionó como se pensaba (el `COMMIT`/`ROLLBACK` final de PDO ya no tenía nada que controlar). Se verificó manualmente con consultas de integridad referencial completas que el resultado final quedó correcto, pero para cualquier corrección futura que necesite `TRUNCATE`, hay que asumir que **no es reversible con `ROLLBACK`** y tomar backup inmediatamente antes, no confiar en la transacción.

**Problema 2 — "Urbanismo" se estaba tratando igual que "Traspaso".** La regla implementada originalmente agrupaba cualquier aparición de "Urbanismo" (en Etapa, Manzana o Casa) dentro del mismo nodo genérico "Traspaso". Regla corregida (indicada por el usuario): si aparece "Traspaso" en cualquiera de las tres columnas, la ubicación es el nodo "Traspaso"; si no aparece "Traspaso" pero sí aparece "Urbanismo", la ubicación es un nodo "Urbanismo" aparte; en cualquier otro caso, la jerarquía real Etapa→Manzana→Casa. Se agregaron nodos raíz "Urbanismo" a los proyectos 1 (Salamanca conjunto) y 3 (Ciudadela) — los únicos con ese caso — y se reprocesaron las 462 líneas de salida. Distribución final verificada: proyecto 1 → 37 líneas en "Urbanismo"; proyecto 2 → 8 en "Traspaso"; proyecto 3 → 3 en "Traspaso" + 4 en "Urbanismo"; Alcala → 3 en "Traspaso". La salida No. 62 (el caso reportado) quedó confirmada bajo "Urbanismo".

**Pendiente de tu confirmación** — el tercer punto de tu mensaje ("la información de Desarrollo no es relevante, que todo quede tal cual como en Producción") ya está aplicado en el sentido de contenido de datos (regla vigente desde el inicio) y ahora también en la numeración de `registro_entradas`/`registro_salidas`. Falta decidir si ese mismo criterio de **igualar los ID exactos de Producción** se extiende también a las tablas de catálogo (`material`, `proyecto`, `contratista`, `destino`, `rubro`, `usuario`), cuyos ID en Desarrollo hoy NO coinciden numéricamente con Producción (por diseño, según la regla original de "nunca asumir que los ID coinciden"). Ninguna de esas columnas se muestra al usuario como número de referencia (los materiales se identifican por `Codigo`, que sí quedó igual a Producción) — por eso no lo cambié sin preguntar, ya que renumerar esas tablas es una operación mucho más grande y riesgosa (afecta las claves foráneas de todo el esquema) que no tiene la misma evidencia de necesidad que el caso de "Salida No.".
