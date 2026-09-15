# Tercera prueba — Corrección de trazabilidad en `ubicacion` (Etapa + Urbanismo)

**Estado: EJECUTADO Y CONFIRMADO (COMMIT) el 2026-09-08.**

## Motivo

En la ronda 2, cuando una salida tenía una Etapa real (ej. "ETAPA 6") pero Manzana y/o Casa marcados como "Urbanismo", el sistema creaba un nodo raíz genérico "Urbanismo" a nivel de todo el proyecto — perdiendo la trazabilidad de a qué etapa pertenecía ese urbanismo. El usuario pidió corregir esto con dump actualizado de Producción (08-09-2026).

## Regla final aplicada

1. **Raíz**: si Etapa = "Traspaso" → raíz = "Traspaso"; si no → raíz = la Etapa real (ej. "ETAPA 6").
2. **Hijo bajo esa raíz**: si Manzana o Casa = "Urbanismo" (incluso si el otro campo dice "Traspaso") → hijo = "Urbanismo", sin importar la raíz. Si ninguno de los dos es "Urbanismo" pero la raíz es "Traspaso" (ej. Manzana="Traspaso", Casa="Casa 6") → hijo = el valor real de Casa. En cualquier otro caso → jerarquía real completa Etapa→Manzana→Casa (sin cambios respecto a las rondas anteriores).

Estrategia de ejecución: igual a la ronda 2 (reconstrucción completa por `TRUNCATE` + reinserción con ID y texto exactos de Producción para todos los catálogos), esta vez con el dump del 08-09-2026 (292 materiales, 6 contratistas, 82 entradas/271 líneas, 75 salidas/545 líneas) y la lógica de `ubicacion` corregida.

## Resultado verificado

| Tabla | Filas | Rango ID |
|---|---|---|
| usuario / proyecto / contratista / destino / rubro / material | 3 / 4 / 6 / 9 / 36 / 292 | 1–N, igual a Producción |
| registro_entradas / líneas | 82 / 271 | 1–82 / 1–271 |
| registro_salidas / líneas | 75 / 545 | 1–75 / 1–545 |
| ubicacion | 42 nodos | sin equivalente en Producción (concepto propio de Desarrollo) |

Árbol de `ubicacion` verificado nodo por nodo:
- Proyecto 1 (Ciudadela): raíces `ETAPA 4`, `ETAPA 5`, `ETAPA 6`, `Traspaso`. `ETAPA 6` → hijo `Urbanismo`. `Traspaso` → hijos `Casa 6` y `Urbanismo` (dos casos distintos correctamente separados).
- Proyecto 2 (Salamanca conjunto): raíces `ETAPA 6`, `Traspaso`. `ETAPA 6` → `Manzana M10` (con Casa 1–10, incluidas las nuevas Casa 9 y 10) y también → hijo `Urbanismo`. `Traspaso` → hijo `Urbanismo` (nuevo caso de este dump).
- Proyecto 3 (Local D1): raíz `Traspaso` → hijo `Casa 6`.
- Proyecto 4 (Alcala): raíz `Traspaso` → hijo `Urbanismo`.

Contraseñas de Reinaldo y Caterine verificadas con `password_verify()`; hash de Juan preservado sin tocar. Integridad referencial verificada en las 12 relaciones del esquema: 0 huérfanas. `rol_permiso` intacto (35 filas).

## Archivos de esta ronda

- Backup: `backups/backup_inventario_antes_ronda3_20260908_110451.sql`
- Script ejecutado: `scripts/reconstruir_paridad_total_v3.php`
- Dump de análisis (solo lectura): esquema `berdez_prod_real_v3`
