# Segunda prueba — Reconstrucción con paridad total (ID y texto exactos)

**Estado: EJECUTADO Y CONFIRMADO (COMMIT) el 2026-09-02.**

## Objetivo de esta ronda

A diferencia de la primera prueba (que emparejaba por texto normalizado y conservaba los ID propios de Desarrollo), esta vez el requisito explícito fue: **todos los catálogos derivados de Producción deben quedar con el mismo `ID` y el mismo texto exacto que en Producción** (`usuario`, `proyecto`, `contratista`, `destino`, `rubro`, `material`), usando además un dump más reciente de Producción (02-09-2026, con datos nuevos: contratista "Administración", rubro "Reperacion grietas", renombrado de manzanas "K"→"Manzana K"/"M9"→"Manzana M9"/"M9 A"→"Manzana M9 A", 7 salidas nuevas y 76 líneas nuevas).

## Cambio de estrategia respecto a la primera prueba

Como ahora se exige igualdad total (no solo de contenido sino de `ID`), ya no tenía sentido "emparejar y mapear" como en la ronda 1 — se optó por **reconstruir por completo** las tablas derivadas de Producción: `TRUNCATE` + reinserción con el `ID` y el texto exactos de Producción, tomados directamente del dump. Esto además simplificó mucho el resto del proceso: como `material`, `proyecto`, `contratista`, `destino` y `rubro` ahora tienen el mismo `ID` en ambas bases, ya no hace falta ninguna tabla de mapeo para traducir las FK de los movimientos — se copian directo.

Lo que **no** se tocó, por ser estructura propia de Desarrollo sin equivalente en Producción (regla original del encargo): `rol`, `permiso`, `rol_permiso`, `almacen`, `tipo_ubicacion`, `notificacion_destinatario`, `proveedor`. Confirmado intacto: 6 roles, 15 permisos, 35 filas de `rol_permiso`.

## Dos detalles técnicos importantes de esta vuelta

1. **Corrección del mojibake sin retipear a mano.** El dump llegó con acentos corruptos (`CimentaciÃ³n`, `AdministraciÃ³n`, etc. — típico de texto UTF-8 reinterpretado como Latin-1). En la primera prueba corregí esto retipeando manualmente el texto correcto, lo cual introdujo un error real: cambié "presi**ò**n" (tilde grave) por "presi**ó**n" (tilde aguda) en un material, asumiendo que era un typo — pero **ese dump nuevo confirma que "presiòn" con tilde grave es el dato real y verdadero de Producción**, no un error de Desarrollo. Esta vez apliqué una técnica mecánica y verificada (`mb_convert_encoding($texto, 'ISO-8859-1', 'UTF-8')`, que revierte exactamente la doble codificación) en vez de retipear, eliminando ese riesgo. Ver `scripts/reconstruir_paridad_total.php` — el texto de todos los catálogos quedó verificado como 0 diferencias byte a byte contra el dump.
2. **Contraseñas nunca se sobrescriben con el texto plano de Producción.** Antes de truncar `usuario`, se capturaron los 3 hashes bcrypt ya vigentes en Desarrollo (Juan, Reinaldo, Caterine) y se reinsertaron tal cual junto con el `ID`/`Identificacion`/`Nombre`/`Apellido`/`RolID` exactos de Producción. Verificado con `password_verify()` para Reinaldo y Caterine.

## Resultado final (todas las validaciones automáticas pasaron antes del `COMMIT`)

| Tabla | Filas | Rango de ID | Coincide con Producción |
|---|---|---|---|
| `usuario` | 3 | 1–3 | Sí (Identificacion/Nombre/Apellido/RolID; Clave = hash propio) |
| `proyecto` | 4 | 1–4 | Sí, texto exacto |
| `contratista` | 6 | 1–6 | Sí, texto exacto (incluye "Administración", nuevo) |
| `destino` | 9 | 1–9 | Sí, texto exacto |
| `rubro` | 36 | 1–36 | Sí, texto exacto (incluye "Reperacion grietas", nuevo) |
| `material` | 291 | 1–291 | Sí, `Codigo`/`Descripcion`/`Unidad`/`Saldo`/`Min_Almacen`/`Max_Casa` exactos (0 diferencias verificadas) |
| `material_almacen` | 291 | — | Sincronizado con `material.Saldo` (los 2 materiales con `Saldo NULL` en Producción quedan en 0 solo en esta tabla, no se inventa un saldo) |
| `ubicacion` | 35 | 1–35 | Sin equivalente en Producción (concepto nuevo de Desarrollo); reconstruida desde cero con la regla Traspaso > Urbanismo > jerarquía real, incluyendo las ramas nuevas (Manzana M9 → Casa 20, Casa 9 y 10 en Manzana M10) |
| `registro_entradas` | 79 | 1–79 | Sí |
| `material_registro_entradas` | 268 | 1–268 | Sí |
| `registro_salidas` | 70 | 1–70 | Sí (incluye las 7 salidas nuevas 64–70) |
| `material_registro_salidas` | 538 | 1–538 | Sí (incluye las 76 líneas nuevas), `UbicacionID` resuelto en el 100% de las líneas |

Integridad referencial verificada en las 12 relaciones del esquema: 0 huérfanas. La regresión de la corrección anterior (salida 62 → nodo "Urbanismo", no "Traspaso") se revalidó y sigue correcta.

## Archivos de esta ronda

- `scripts/reconstruir_paridad_total.php` — script ejecutado.
- `backups/backup_inventario_antes_ronda2_20260902_104504.sql` — backup completo de Desarrollo tomado antes de truncar nada.
- `berdez_prod_real_v2` — el dump de esta ronda, ya con el mojibake corregido, cargado en un esquema aparte (solo lectura, no afecta `inventario`).

Los esquemas de la ronda 1 (`berdez_prod_real`, `berdez_migracion_staging`) ya no son necesarios para nada — la estrategia de esta ronda no los usa — se pueden borrar cuando quieras.

## Nota sobre el futuro corte a Producción real

Este ejercicio confirma que la reconstrucción completa (en vez de mapeo incremental) es más simple y más segura cuando se exige paridad total de ID — es el enfoque que recomiendo usar el día del corte real, alimentado con el dump fresco de ese momento.
