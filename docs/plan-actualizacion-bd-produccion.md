# Plan de Actualización de BD de Desarrollo con Datos de Producción

**Estado: SOLO ANÁLISIS. No se ejecutó ningún INSERT/UPDATE/DELETE ni cambio de código. Producción no fue tocada.**

Metodología: se comparó el schema real de la BD de Desarrollo (`inventario`, MySQL local vía `connection.php`, consultada en vivo con `mysqldump`/`SELECT`) contra el dump de Producción pegado por el usuario. El dump se cargó tal cual en una base de datos temporal aislada (`berdez_prod_audit_tmp`, creada y eliminada durante este análisis, sin tocar `inventario` ni ningún servidor real) para poder correr consultas SQL exactas en vez de estimar a ojo. **Limitación conocida:** para poder cargar el dump en un archivo de trabajo, los caracteres especiales (tildes, la `Ñ`, y sobre todo comillas dobla `"` usadas como símbolo de pulgadas en `Descripcion`) se normalizaron manualmente. Esto puede haber introducido falsos "no-match" en la comparación de texto de `material.Descripcion` (ver sección 9). **Antes de ejecutar la migración real, hay que repetir la comparación de materiales con un `mysqldump` real y sin retipear, no con este archivo de trabajo.**

---

## 1. Objetivo

Llevar a la BD de Desarrollo (`inventario`) los datos reales y vigentes de la empresa —usuarios, catálogos, inventario, movimientos— que hoy solo existen en Producción, adaptándolos al esquema y las reglas de negocio ya implementadas en Desarrollo (roles/permisos, `ubicacion` jerárquica, costo promedio, almacenes, solicitudes, auditoría). Al terminar, Desarrollo debe poder desplegarse a Producción llevando consigo la información real de la empresa, sin haber retrocedido ninguno de los cambios de esquema ya construidos.

No es una sincronización permanente ni una integración entre bases: es una migración de datos, ejecutada una sola vez, en un sentido (Producción → Desarrollo).

---

## 2. Estado actual: Producción vs Desarrollo

| | Producción | Desarrollo (`inventario`) |
|---|---|---|
| Motor | MariaDB 11.8.8 (export phpMyAdmin) | MariaDB local (XAMPP), vía `connection.php` (`root`, sin password) |
| Tablas | 14 | 26 |
| Modelo de ubicación | `area` + `manzana` + `casa`, 3 catálogos planos e independientes | `ubicacion` (árbol autorreferenciado por `PadreID`) + `tipo_ubicacion` (catálogo de tipos, sin FK real) |
| Seguridad | `usuario` + `rol` (2 roles fijos), sin tabla de permisos | `usuario` + `rol` (6 roles) + `permiso` (15) + `rol_permiso` (matriz) |
| Contraseñas | Texto plano en `usuario.Clave varchar(10)` | Hash bcrypt (`password_hash`/`password_verify`) en `usuario.Clave varchar(255)` |
| Costo de material | No existe | `material.CostoPromedio`, `CostoUnitario` en líneas de entrada/salida, `ajuste_inventario` |
| Almacenes | Implícito (uno solo, sin tabla) | `almacen` + `material_almacen` (saldo por almacén; hoy 1 almacén activo) |
| Proveedores | No existe | `proveedor`, `registro_entradas.ProveedorID` (nullable) |
| Solicitudes | No existe | `solicitud` + `solicitud_detalle` (flujo de aprobación) |
| Auditoría | No existe | `auditoria` (log de acciones) |
| Notificaciones | No existe | `notificacion_destinatario` (config de alertas de stock mínimo por correo) |
| `Casa/Manzana/Area` en salidas | Únicas columnas de ubicación | Siguen existiendo como columnas **nullable** en `material_registro_salidas`, ya no obligatorias; `UbicacionID` es ahora el campo principal (ver comentario en `RegistroSalidasController.php`) |

Conclusión: Desarrollo no es "Producción con features nuevas" — es un sistema que ya reemplazó el modelo de ubicación y de seguridad, y que fue poblado mayormente con datos de prueba mientras se construían esas features. La tabla `material` es la única excepción parcial: como se ve en la sección 9, una porción de ella sí proviene de un volcado real de Producción hecho en algún momento, y después se contaminó con pruebas.

---

## 3. Caso crítico: Ubicación (Casa/Manzana/Etapa → `ubicacion`)

### 3.1 Lo que hay en Producción

`area` (7 filas, = "Etapa"), `manzana` (7 filas), `casa` (28 filas) son **catálogos globales e independientes**: un registro de salida (`material_registro_salidas`) simplemente guarda un `AreaID`, un `ManzanaID` y un `CasaID` como tres selects independientes del formulario. Verifiqué con SQL si existía una relación jerárquica real entre ellos (¿la Casa 3 siempre pertenece a la misma Manzana/Etapa?) y la respuesta es **no, consistentemente**:

```
CasaID=3  →  aparece con (Etapa 5, Manzana K)   -- 16 movimientos
CasaID=3  →  aparece con (Etapa 6, Manzana M10) -- 38 movimientos  (otro proyecto)
CasaID=4  →  aparece con (Etapa 4, Manzana K)   -- 1 movimiento
CasaID=4  →  aparece con (Etapa 5, Manzana K)   -- 16 movimientos
CasaID=4  →  aparece con (Etapa 6, Manzana M10) -- 37 movimientos (otro proyecto)
```

Es decir: "Casa 3" no es un identificador único de vivienda — es un número de casa que **se repite dentro de cada Manzana**, y solo la combinación **(Proyecto, Etapa, Manzana, Casa)** identifica una vivienda física real. Esto es una conclusión derivada de los datos, no un supuesto: la numeración de casas se reinicia por manzana, como es normal en obra.

### 3.2 Combinaciones reales que hay que preservar

De las 462 líneas de `material_registro_salidas` en Producción, solo existen **24 combinaciones distintas** de (Proyecto, Etapa, Manzana, Casa/Urbanismo):

| Proyecto (prod) | Etapa | Manzana | Casa | Movimientos |
|---|---|---|---|---|
| Ciudadela Salamanca La Nueva | ETAPA 5 | K | casa 3, 4, 5, Casa 6, 7, 22–27 | 15–18 c/u |
| Ciudadela Salamanca La Nueva | ETAPA 4 | K | casa 4 | 1 *(ver anomalía abajo)* |
| Ciudadela Salamanca La Nueva | ETAPA 6 | Urbanismo | Urbanismo | 4 |
| Ciudadela Salamanca La Nueva | Traspaso | Traspaso | Casa 6 | 2 |
| Ciudadela Salamanca La Nueva | Traspaso | Urbanismo | Urbanismo | 1 |
| Salamanca la Nueva - conjunto residencial | ETAPA 6 | Manzana M10 | casa 1–5, Casa 6–8 | 1–52 c/u |
| Salamanca la Nueva - conjunto residencial | ETAPA 6 | Urbanismo | Urbanismo | 37 |
| Local D1 | Traspaso | Traspaso | Casa 6 | 8 |
| Alcala | Traspaso | Traspaso | Urbanismo | 3 |

Esto es perfectamente manejable a mano: son 24 nodos hoja, no miles.

### 3.3 Lo que hay hoy en `ubicacion` (Desarrollo)

`ubicacion` tiene 198 filas, pero mezcla dos cosas muy distintas:

1. **Datos reales parciales**, ya migrados en algún momento anterior: bajo `ProyectoID=1` ("Salamanca la nueva conjunto residencial") existen nodos `Etapa 1`, `etapa 2`, `M10`, `M11` y solo **3 casas** (`casa 1`, `casa 1` otra vez bajo otro padre, `casa 3`) — muy incompleto frente a las 24 combinaciones reales de arriba.
2. **Datos de demostración/prueba** de un modelo de edificio en altura (`Torre 1`, `Torre 2`, `Piso 01`–`25`, `Apto 01`–`06`, 151 apartamentos) bajo el mismo `ProyectoID=1`, y un `Piso`/`Local` bajo `ProyectoID=57`. Berdez es una constructora de **casas** (viviendas unifamiliares por manzana/etapa), no de torres de apartamentos — esto es inequívocamente contenido de prueba de la funcionalidad de árbol de `ubicacion`, no una obra real de la empresa.

### 3.4 Estrategia recomendada

1. Eliminar de `ubicacion` los nodos de prueba tipo Torre/Piso/Apartamento (bajo Proyecto 1 y 57) — no representan ninguna obra real.
2. Reconstruir el árbol para cada Proyecto real usando exactamente las 24 combinaciones de la sección 3.2, con esta jerarquía (`Nivel`/`Tipo`):
   `Proyecto → Etapa (Tipo='ETAPA', Nivel 0) → Manzana (Tipo='MANZANA', Nivel 1) → Casa (Tipo='CASA', Nivel 2)`
   y como caso especial, `Urbanismo` y `Traspaso` como nodos de un solo nivel (bolsas de imputación genérica, no viviendas — ver 3.5).
3. Conservar/completar los 4 nodos reales que ya existen en Desarrollo (Etapa 1, etapa 2, M10, M11) en vez de recrearlos, para no duplicar; solo agregar lo que falte.

### 3.5 Requiere decisión

- **`Urbanismo` y `Traspaso` no son viviendas.** Aparecen como Etapa+Manzana+Casa al mismo tiempo (p. ej. `Traspaso/Traspaso/Urbanismo`) y se repiten **entre proyectos distintos** (Ciudadela y Local D1 comparten `Traspaso/Traspaso/Casa 6`). Esto indica que son valores de "no aplica" / traspasos internos de bodega, no ubicaciones físicas reales. **Decisión pendiente:** ¿se modelan como un nodo `ubicacion` especial por proyecto (ej. "Urbanismo" como hijo directo del proyecto, tipo `URBANISMO`), o se resuelven fuera del árbol de ubicación (dejando `UbicacionID = NULL` en esas líneas migradas y describiendo el destino solo con `DestinoID`)? Ambas son razonables; no hay forma de saberlo sin preguntar al negocio.
- **Anomalía de datos:** `Casa 4` bajo `Manzana K` aparece 1 vez en `ETAPA 4` y 16 veces en `ETAPA 5`, siempre en el mismo proyecto. Es casi seguro un error de digitación (la etapa correcta parece ser la 5), pero no se debe "corregir" unilateralmente un dato histórico de Producción. **Decisión pendiente:** confirmar con el usuario si ese único movimiento se reclasifica a Etapa 5 o se conserva tal cual bajo un nodo Etapa 4/K/Casa 4 igualmente creado.
- **Proyecto "Alcala" (Producción, ID 4)** no tiene ningún equivalente por nombre en Desarrollo, y solo tiene un movimiento de tipo "Traspaso/Traspaso/Urbanismo" (sin vivienda real asociada). Ver sección 9.

---

## 4. Mapeo de datos — resumen ejecutivo

| Producción | → | Desarrollo | Estrategia |
|---|---|---|---|
| `area`+`manzana`+`casa` (planos) | → | `ubicacion` (árbol) | Reconstruir árbol por las 24 combinaciones reales (sección 3) |
| `usuario` (Clave texto plano) | → | `usuario` (Clave bcrypt) | Insertar/actualizar por `Identificacion`, rehashear con `password_hash()` |
| `rol` (2 roles) | → | `rol` (6 roles) | Mapear por nombre, no crear nuevos roles |
| — (no existe) | → | `permiso`, `rol_permiso` | Conservar tal cual están en Desarrollo, no tocar |
| `material` (291) | → | `material` (296, mezcla real+prueba) | Ver sección 9 — el caso más delicado de todos |
| `contratista` (5) | → | `contratista` (3) | Completar por nombre, ver sección 9 |
| `destino` (9) | → | `destino` (5) | Completar; los primeros 5 ya coinciden 1:1 por nombre |
| `rubro` (35) | → | `rubro` (2, placeholder) | Reemplazar el catálogo casi completo |
| `proyecto` (4) | → | `proyecto` (4, IDs distintos) | Mapear por nombre, 1 sin equivalente (ver sección 9) |
| `registro_entradas`/`material_registro_entradas` | → | ídem + `ProveedorID`, `CostoUnitario` nullable | Replay histórico con IDs remapeados, campos nuevos en `NULL` |
| `registro_salidas`/`material_registro_salidas` | → | ídem + `UbicacionID`, `CostoUnitario` nullable | Replay histórico + `UbicacionID` resuelto por el árbol de la sección 3 |
| — (no existe) | → | `solicitud`, `ajuste_inventario`, `auditoria`, `notificacion_destinatario`, `almacen`, `proveedor` | Sin equivalente en Producción; ver secciones 10–11 sobre qué conservar de lo que ya hay en Desarrollo |

---

## 5. Usuarios

**Producción (3 usuarios reales):**

| Identificacion | Nombre | Rol prod |
|---|---|---|
| 1004035010 | Juan Lopez | Administrador |
| 83235047 | Reinaldo Gordo Losada | Almacenista |
| 1070605738 | Caterine Fernanda Jovel Rincon | Almacenista |

**Desarrollo (2 usuarios):**

| Identificacion | Nombre | Rol dev |
|---|---|---|
| 1004035010 | Juan Diego Lopez | Administrador |
| 123456789 | Pedro Perez | Almacenero |

Cruzando por `Identificacion` (la única clave estable entre ambas bases — los `ID` autoincrementales no coinciden):

- **1004035010 (Juan)** ya existe en ambos lados. Es la **misma persona** (Desarrollo tiene el nombre completo "Juan Diego"), con contraseña ya migrada a bcrypt. **No tocar.**
- **83235047 (Reinaldo)** y **1070605738 (Caterine)** son usuarios reales de Producción que **no existen en Desarrollo** — hay que crearlos.
- **123456789 (Pedro Perez)** existe solo en Desarrollo. `123456789` es un número de identificación claramente ficticio (patrón de prueba), y "Pedro Perez" no aparece en ningún registro de Producción. **Es un usuario de prueba.** No debe copiarse a Producción cuando se despliegue; se recomienda desactivarlo o eliminarlo de Desarrollo antes del corte final (requiere confirmación del usuario, no se debe borrar sin avisar porque pudo haberse usado para pruebas activas del equipo).

---

## 6. Roles

Producción solo tiene 2 roles (`Administrador`, `Almacenista`); Desarrollo tiene 6, producto de la nueva matriz de permisos. El mapeo por nombre:

| RolID producción | Nombre prod | → RolID Desarrollo | Nombre dev | Confianza |
|---|---|---|---|---|
| 1 | Administrador | 1 | Administrador | Alta (coincidencia exacta) |
| 2 | Almacenista | 2 | Almacenero | Media — mismo puesto, nombre ligeramente distinto; **requiere confirmación de que es el mismo rol y no uno nuevo** |

No hay necesidad de tocar `permiso` ni `rol_permeso`: son enteramente nuevos en Desarrollo (Producción no tiene concepto de permisos granulares) y ya están configurados con una matriz coherente (verificada: Administrador tiene los 14 permisos relevantes, Almacenero tiene los 6 operativos de bodega, y hay 4 roles adicionales — Residente de Obra, Compras, Gerencia, Consulta — sin usuarios reales todavía asignados). **Regla: no se reemplaza ni se resetea esta matriz; solo se les asignan los `RolID` correctos a los usuarios migrados.**

---

## 7. Permisos

No requieren migración de datos — ya son responsabilidad exclusiva de Desarrollo. Único punto de atención: al crear a Reinaldo y Caterine con `RolID` = Almacenero, verificar en la aplicación real (no solo en la BD) que el conjunto de permisos de "Almacenero" cubre lo que esas dos personas hacían en Producción (registrar entradas y salidas) — según la matriz ya cargada, sí lo cubre (`entrada.registrar`, `salida.registrar`).

---

## 8. Contraseñas

**Producción:** texto plano en `usuario.Clave varchar(10)` (ej. `'123456789'`, `'Rei_8323'`, `'Fer_1070'`).
**Desarrollo:** hash bcrypt (`password_hash($_POST['clave'], PASSWORD_DEFAULT)`, verificado con `password_verify()` en `Model/Usuario.php`), columna ampliada a `varchar(255)` con collation `utf8mb4_bin`.

Esto **sí es migrable de forma segura**, porque a diferencia de un hash irreversible, la contraseña de Producción está disponible en texto plano: no hace falta pedirle a nadie que "resetee" nada. La estrategia es:

1. Para Juan (ya existe en dev con hash): no tocar, ya inició sesión con su clave nueva.
2. Para Reinaldo y Caterine (nuevos): al insertarlos, tomar su clave de Producción (`Rei_8323`, `Fer_1070`) y aplicarles `password_hash()` antes de guardarla — igual que hace `UsuarioController::store()` con cualquier usuario nuevo. Así el usuario puede seguir usando **la misma contraseña que ya conoce** el primer día, sin fricción, y queda almacenada de forma segura.

**Riesgo a documentar:** las claves de Producción son cortas y predecibles (`Rei_8323` deriva del nombre + parte del documento). Vale la pena, en paralelo (no como parte de esta migración de datos), sugerir a esos usuarios cambiar su clave la primera vez que entren al sistema nuevo — pero **eso es una recomendación de producto, no un requisito de la migración**.

---

## 9. Catálogos — hallazgos y decisiones

### 9.1 `material` — el caso más delicado

Este es el hallazgo central de toda la auditoría. Al comparar `material.Descripcion` (normalizada) entre ambas bases:

- **103 de los 291 materiales de Producción tienen una fila en Desarrollo con el mismo `Codigo` y una `Descripcion` prácticamente idéntica.** Esto no es casualidad: demuestra que el catálogo de Desarrollo **nació de un volcado real de Producción** en algún momento del proyecto (comparten el mismo esquema de numeración de `Codigo`, que es un código de negocio, no un autoincremental).
- Sin embargo, el `Saldo` de esos 103 materiales **difiere entre Producción y Desarrollo en 87 de los 103 casos** verificados. Esto es consistente con que, después de aquel volcado inicial, en Desarrollo se ejecutaron entradas/salidas de **prueba** (hay 41 registros de entrada y 64 de salida en `inventario`, con fechas y usuarios que no corresponden a la operación real) que movieron los saldos lejos de la realidad actual de Producción.
- Los **188 materiales de Producción restantes no tienen equivalente en Desarrollo** — nunca se copiaron, o se copiaron con una descripción tan distinta que no calzan por texto (esto último no se puede descartar del todo por la limitación de transcripción mencionada al inicio del documento).
- Los **193 materiales de Desarrollo sin equivalente en Producción** son o bien materiales agregados después del volcado (reales, en cuyo caso se deben conservar) o datos de prueba. **No se pueden distinguir automáticamente unos de otros con la información disponible — requiere que alguien del negocio revise esa lista de 193 y diga cuáles son reales.**
- **Riesgo detectado y confirmado con SQL:** el campo `Codigo` **no es un identificador confiable para emparejar**, porque en Desarrollo se reutilizaron números de `Codigo` para materiales nuevos que no tienen relación con el material de Producción que originalmente tenía ese mismo `Codigo`. Cualquier script de migración que empareje por `Codigo` en vez de por `Descripcion` normalizada corre el riesgo de sobreescribir un material con los datos de otro completamente distinto.

**Estrategia recomendada para `material`:**
1. Emparejar por `Descripcion` normalizada (minúsculas, sin tildes, espacios recortados) como clave primaria de negocio — nunca por `Codigo` ni por `ID`.
2. Para los emparejados: `UPDATE` de `Codigo`, `Saldo`, `Min_Almacen`, `Max_Casa` desde Producción (fuente de verdad de datos); dejar `CostoPromedio` en `NULL` salvo que el negocio diga lo contrario — el valor que hoy tiene en Desarrollo, si lo tiene, se calculó sobre movimientos de prueba y no es confiable (ver sección 10).
3. Para los 188 solo-en-Producción: `INSERT` nuevo en Desarrollo, `CostoPromedio = NULL` (nace sin historial, tal como ya hace `Material::insert()` para altas nuevas).
4. Para los 193 solo-en-Desarrollo: **requiere decisión humana**, uno por uno o por lote — no se pueden auto-clasificar como prueba o reales sin conocimiento de negocio.
5. Repetir el emparejamiento con el dump real (sin retipear) antes de ejecutar nada, por la limitación de transcripción explicada al inicio.

### 9.2 `contratista`

| Producción | Desarrollo | Confianza del match |
|---|---|---|
| Hermanos Murcia | Construcciones hermanos murcia | Alta |
| Smartools | Smartools | Alta (exacto) |
| Amin Narvaez | Construcciones narvaez camacho | **Baja — nombres distintos, requiere confirmación de que es la misma empresa/persona** |
| Sendos | *(sin equivalente)* | Insertar nuevo |
| Grabiel cardona | *(sin equivalente)* | Insertar nuevo |

### 9.3 `destino`

Los 5 primeros de Desarrollo coinciden **exactamente por nombre y orden** con los 5 primeros de Producción (Urbanismo, Cimentación, Industrializado, Consumibles, Acabados) — match de alta confianza, mismos IDs incluso. Producción tiene 4 destinos adicionales que Desarrollo no tiene: `Cubierta`, `Tuberías`, `Vías/andenes/sardineles`, `Accesorios`. Estos ya están referenciados por movimientos reales de entrada (`DestinoID` 6–9 en `material_registro_entradas` de Producción) — **deben insertarse sí o sí** para poder migrar esas entradas.

### 9.4 `rubro`

Desarrollo solo tiene 2 filas (`Cajilla`, `Anden`) que ni siquiera coinciden por nombre con las 35 de Producción (`Cajas inspeccion`, `Sumideros vias`, etc. — son 35 rubros de obra civil detallados). Esto es evidentemente un catálogo placeholder de prueba de la nueva feature de Rubro. **Estrategia: insertar las 35 filas de Producción tal cual; las 2 de Desarrollo se mantienen aparte solo si algún dato de prueba activo las referencia** (verificar antes de tocar/borrar).

### 9.5 `proyecto`

| Producción | Desarrollo | Confianza |
|---|---|---|
| Ciudadela Salamanca La Nueva (ID 1) | Ciudadela salamanca la nueva (ID 3) | Alta |
| Salamanca la Nueva - conjunto residencial (ID 2) | Salamanca la nueva conjunto residencial (ID 1) | Alta |
| Local D1 (ID 3) | Tienda D1 (ID 2) | Media — "Local" vs "Tienda", probablemente el mismo, **requiere confirmación** |
| Alcala (ID 4) | *(sin equivalente)* | Insertar nuevo — su único movimiento histórico es un "Traspaso" genérico sin vivienda real asociada (sección 3.5), así que el riesgo de esta alta es bajo |
| *(sin equivalente)* | CC san pablo (ID 57) | **Requiere decisión:** ¿proyecto nuevo real que ya se está usando en Desarrollo (se conserva), o dato de prueba (se elimina)? No se puede saber sin preguntar |

**Importante:** ningún `ProyectoID` coincide numéricamente entre las dos bases — cualquier script de migración necesita una tabla de mapeo explícita `ProduccionProyectoID → DesarrolloProyectoID`, nunca asumir igualdad.

### 9.6 `almacen`, `proveedor`

Ambos son conceptos 100% nuevos de Desarrollo, sin equivalente en Producción. `almacen` ya tiene su único registro real ("Almacén Principal") — no requiere ninguna migración, solo asegurarse de que `material_almacen` quede sincronizado con el `Saldo` final de cada material (la función `Almacen::sincronizarSaldoPrincipal()` ya existe para esto). `proveedor` tiene 1 fila ("Andamios del sur") que puede ser real o de prueba — Producción nunca registró proveedores, así que no hay nada que migrar hacia esta tabla, solo decidir si esa fila se conserva.

---

## 10. Inventario y movimientos

### 10.1 Saldos

El saldo real y vigente de cada material está en `p_material.Saldo` de Producción — es innegociable, es la fuente de verdad. El `Saldo` actual de Desarrollo para los materiales ya existentes (sección 9.1) es fruto de pruebas y debe ser **reemplazado**, no promediado ni conciliado.

### 10.2 Movimientos históricos (`registro_entradas` / `registro_salidas`)

Producción tiene 79 entradas / 268 líneas y 63 salidas / 462 líneas, todas entre el 12 y el 31 de agosto de 2026 (con hora exacta). Desarrollo tiene su propio historial paralelo (41/26 entradas, 64/50 salidas) construido enteramente sobre el catálogo de prueba. **Regla del prompt: no inventar movimientos.** Esto significa:

- El historial de Producción se **reproduce** ("replay") en Desarrollo insertando nuevas filas en `registro_entradas`/`registro_salidas`/sus detalles, con los mismos `Fecha`/`Hora`/`Usuario`, pero apuntando a los `MaterialID`, `DestinoID`, `RubroID`, `UbicacionID`, `ContratistaID`, `ProyectoID` ya remapeados a sus equivalentes (nuevos o existentes) en Desarrollo.
- Campos nuevos que Producción nunca tuvo (`ProveedorID`, `CostoUnitario`) se dejan en `NULL` para todo lo migrado — no se inventa un proveedor o un costo que Producción no registró.
- El historial de prueba que ya existe en Desarrollo (registros que referencian el catálogo de materiales de prueba, proyectos de prueba, etc.) debe **clasificarse y probablemente descartarse** una vez que el catálogo subyacente se reemplace, porque ya no tendrá contra qué material/ubicación real anclarse de forma coherente (ver sección 14).

### 10.3 Costo promedio

Producción nunca calculó costo. El campo `CostoPromedio` en Desarrollo, para los materiales que sean identificados como "reales" (sección 9.1), casi con certeza fue calculado sobre movimientos de prueba (con `CostoUnitario` ficticios) y **no representa un costo real de la empresa**. Recomendación: resetear a `NULL` en la migración y dejar que el costo promedio se reconstruya de forma natural a partir de las próximas entradas reales que se registren en el sistema ya desplegado — exactamente como ya lo hace `Material::insert()` para altas nuevas ("nace en null"). **Requiere confirmación:** esto implica que el negocio no tendrá costo promedio disponible el día 1 después del despliegue; es una consecuencia aceptada de que Producción nunca lo trackeó, no un error de la migración.

### 10.4 `ajuste_inventario`

Las 5 filas que hoy existen en Desarrollo (tipo PERDIDA/CONTEO/DAÑO) están todas sobre `MaterialID=1`, con `CostoUnitario` de prueba — son ajustes de prueba sobre el catálogo de prueba. No tienen ningún equivalente posible en Producción (esa tabla no existe ahí). **Se descartan junto con el resto de datos de prueba dependientes del catálogo viejo (sección 14), salvo que el usuario confirme que alguno de esos 5 ajustes fue una operación real y no una prueba.**

---

## 11. Solicitudes

`solicitud`/`solicitud_detalle` no existen en Producción — es una funcionalidad enteramente nueva. Las 3 solicitudes que hoy hay en Desarrollo están ligadas a `UsuarioSolicitaID=1` (Juan, real) pero a `ProyectoID` y `MaterialID` de prueba, y una de ellas ya está enlazada a un `RegistroSalidasID` de prueba que probablemente se va a reemplazar. **No hay nada que "traer" desde Producción aquí** (la funcionalidad no existía); solo hay que decidir si esas 3 solicitudes de prueba se conservan como historial de pruebas o se limpian junto con el resto de datos de prueba dependientes (sección 14).

---

## 12. Auditoría

`auditoria` tampoco existe en Producción — 100% nuevo. Las 81 filas actuales en Desarrollo son un log de las acciones hechas **durante las pruebas** del sistema (crear/editar Ubicaciones de prueba, RegistroEntradas de prueba, etc. — se ve claramente en la muestra: `Entidad='Ubicacion', EntidadID=17/18/19`, IDs que van a dejar de existir si se reconstruye el árbol de ubicación). Una vez que las entidades subyacentes se reemplacen (nuevos `ID` de material, ubicación, registro de entrada/salida), estas 81 filas de auditoría **quedan huérfanas y sin sentido** (apuntan a `EntidadID` que ya no correspondrán a nada real).

**Requiere decisión, por ser un log de auditoría (dato sensible por naturaleza):** ¿se borra este historial de pruebas porque no aporta trazabilidad real de negocio, o se conserva archivado (por ejemplo exportado a un archivo aparte) antes de vaciar la tabla, por si alguna vez hace falta revisar qué se probó? Recomiendo lo segundo (exportar y luego vaciar) porque es reversible y de bajo costo.

---

## 13. IDs — estrategia de correspondencia

Confirmado con datos reales: **ningún `ID` autoincremental coincide entre Producción y Desarrollo**, ni siquiera cuando el registro es "el mismo" (ejemplo: Juan Lopez es `usuario.ID=1` en ambos lados por coincidencia, pero Ciudadela Salamanca es `proyecto.ID=1` en Producción y `ID=3` en Desarrollo). La única clave estable identificada es:

- `usuario`: `Identificacion` (número de cédula/documento)
- `proyecto`, `contratista`, `destino`, `rubro`, `material`: `Descripcion` normalizada (y para `material`, además `Codigo` como señal de apoyo, nunca como única clave — sección 9.1)
- `ubicacion`: la tupla `(ProyectoID_dev, PadreID_dev, Nombre)` — que además ya está protegida por una `UNIQUE KEY` generada (`ClaveUnicidad`) en el propio esquema de Desarrollo

El plan de ejecución debe construir, para cada catálogo, una tabla temporal de mapeo `ProduccionID → DesarrolloID` (nueva o existente) antes de tocar cualquier tabla de movimientos, y usar esas tablas de mapeo para traducir las FK de `registro_entradas`/`registro_salidas` y sus detalles. Nunca se debe asumir `Producción.ID = Desarrollo.ID` en ningún `UPDATE`/`INSERT`.

---

## 14. Datos de prueba en Desarrollo — clasificación

| Dato | Veredicto | Motivo |
|---|---|---|
| Usuario "Pedro Perez" (Identificacion 123456789) | Prueba | Cédula ficticia, sin equivalente en Producción |
| Nodos `Torre/Piso/Apartamento` en `ubicacion` | Prueba | Modelo de negocio (edificio en altura) que no corresponde a Berdez (casas) |
| Proyecto "CC san pablo" (ID 57) | **Requiere decisión** | Podría ser un proyecto nuevo real aún no cargado en Producción |
| Rubros "Cajilla"/"Anden" (2 filas) | Probable prueba | No coinciden con ninguno de los 35 rubros reales de Producción |
| Proveedor "Andamios del sur" | **Requiere decisión** | Producción nunca trackeó proveedores; podría ser real |
| Los 64 `registro_salidas` / 41 `registro_entradas` actuales de Desarrollo | Prueba | Construidos sobre el catálogo de material de prueba; fechas/volumen no coinciden con la operación real de Producción |
| Las 3 `solicitud` actuales | Prueba | Ligadas a materiales/proyectos de prueba |
| Los 5 `ajuste_inventario` actuales | Prueba | Todos sobre `MaterialID=1` con `CostoUnitario` de prueba |
| Las 81 filas de `auditoria` | Prueba (log de testing) | Ver sección 12 |
| Los 193 materiales de Desarrollo sin match en Producción | **Requiere decisión caso por caso** | Ver sección 9.1 — no se puede automatizar |
| El único `notificacion_destinatario` activo hoy | **Real — conservar** | Es configuración operativa creada por el usuario real del sistema, no tiene equivalente en Producción porque es una feature nueva, y no depende de ningún catálogo que vaya a reemplazarse |
| `almacen` (Almacén Principal) | Real — conservar | Única fila, consistente con la operación real (un solo almacén físico) |

---

## 15. Estrategia final recomendada

**Opción C (staging) combinada con D (híbrida), no A ni B puras.**

- Un `TRUNCATE`/reconstrucción total (Opción B) es demasiado arriesgado para `material`, porque una parte de esa tabla ya es real y tiene relaciones con `material_almacen`/`ajuste_inventario`/movimientos que hay que decidir qué hacer con ellos, no borrar a ciegas.
- Un `INSERT/UPDATE` selectivo directo contra las tablas de Desarrollo (Opción A pura) es riesgoso para las tablas con `UNIQUE`/generated columns (`ubicacion.ClaveUnicidad`, `ajuste_inventario.AperturaUnica`) y para las FK en cascada de `registro_salidas`.

Por eso, la estrategia recomendada es:

1. **Etapa de staging:** cargar el dump real de Producción (sin retipear) en una base de datos temporal separada (igual que se hizo en este análisis), y ahí construir, con SQL, las tablas de mapeo `ProduccionID → DesarrolloID` para cada catálogo (usuario, proyecto, contratista, destino, rubro, material, y los 24 nodos de `ubicacion`).
2. **Etapa de decisiones humanas:** presentar al usuario cada fila marcada "requiere decisión" en este documento (secciones 3.5, 9.2, 9.5, 9.6, 12, 14) y registrar la respuesta antes de continuar.
3. **Etapa de limpieza de datos de prueba:** una vez con las decisiones tomadas, eliminar (con respaldo exportado primero) los datos de prueba confirmados como tales en la sección 14.
4. **Etapa de carga de catálogos:** `INSERT`/`UPDATE` en Desarrollo, en el orden de la sección 16, usando las tablas de mapeo.
5. **Etapa de replay de movimientos:** reconstruir `registro_entradas`/`registro_salidas` y sus detalles desde Producción, traduciendo cada FK con las tablas de mapeo, dejando en `NULL` los campos que Producción nunca tuvo.
6. **Etapa de sincronización derivada:** recalcular `material_almacen` desde el `material.Saldo` final (vía `Almacen::sincronizarSaldoPrincipal()`), y dejar `CostoPromedio` en `NULL` salvo decisión contraria.
7. **Etapa de validación:** correr las consultas de la sección 17 antes de dar por cerrada la migración.

---

## 16. Orden de ejecución seguro

```
1. usuario  (por Identificacion)              -- no depende de nada más
2. rol      (ya existe, solo se usa el mapeo) -- no se modifica
3. proyecto (por Descripcion normalizada)
4. contratista, destino, rubro, proveedor     -- catálogos independientes
5. material (por Descripcion normalizada)     -- depende de nada, pero es prerrequisito de:
6. material_almacen                           -- depende de material + almacen
7. ubicacion (árbol, sección 3)               -- depende de proyecto
8. registro_entradas                          -- depende de usuario, proveedor
9. material_registro_entradas                 -- depende de material, registro_entradas, destino
10. registro_salidas                          -- depende de usuario, contratista, proyecto
11. material_registro_salidas                 -- depende de material, registro_salidas, destino, rubro, ubicacion
12. ajuste_inventario, solicitud, solicitud_detalle, notificacion_destinatario, auditoria
                                               -- según lo decidido en la sección 14, al final
```

---

## 17. Validaciones posteriores (consultas de verificación)

```sql
-- 1. Todos los usuarios reales de Producción existen en Desarrollo por Identificacion
SELECT Identificacion FROM usuario WHERE Identificacion IN (1004035010, 83235047, 1070605738);
-- esperado: 3 filas

-- 2. Ningún usuario de prueba conocido sigue activo (si se decidió eliminarlo)
SELECT * FROM usuario WHERE Identificacion = 123456789;
-- esperado: 0 filas (si se decidió eliminar a Pedro Perez)

-- 3. Todo RolID de usuario referencia un rol existente
SELECT u.ID FROM usuario u LEFT JOIN rol r ON r.ID = u.RolID WHERE r.ID IS NULL;
-- esperado: 0 filas

-- 4. Todo movimiento de salida migrado tiene FK válidas (integridad referencial ya la exige InnoDB,
--    pero se valida además que no haya quedado ningún NULL donde no debería)
SELECT COUNT(*) FROM material_registro_salidas WHERE MaterialID IS NULL OR Registro_SalidasID IS NULL;
-- esperado: 0

-- 5. Los 24 combos de Etapa/Manzana/Casa de la sección 3.2 existen como nodos hoja en ubicacion
SELECT COUNT(*) FROM ubicacion WHERE Tipo IN ('ETAPA','MANZANA','CASA') AND ProyectoID IN (...);
-- comparar contra el conteo esperado por proyecto

-- 6. La suma de Saldo de material coincide, material por material, con Producción
--    (requiere la tabla de mapeo Produccion->Desarrollo generada en la etapa de staging)
SELECT d.ID, d.Descripcion, d.Saldo AS saldo_dev, map.saldo_prod
FROM material d JOIN <tabla_mapeo> map ON map.dev_id = d.ID
WHERE d.Saldo <> map.saldo_prod;
-- esperado: 0 filas

-- 7. material_almacen sincronizado con material.Saldo (un solo almacén activo)
SELECT m.ID FROM material m JOIN material_almacen ma ON ma.MaterialID = m.ID AND ma.AlmacenID = 1
WHERE ma.Saldo <> m.Saldo;
-- esperado: 0 filas

-- 8. Ausencia de duplicados en catálogos clave
SELECT Descripcion, COUNT(*) FROM material GROUP BY LOWER(TRIM(Descripcion)) HAVING COUNT(*) > 1;
SELECT Descripcion, COUNT(*) FROM proyecto GROUP BY LOWER(TRIM(Descripcion)) HAVING COUNT(*) > 1;

-- 9. Todo registro de entrada/salida migrado cae dentro del rango de fechas real de Producción
SELECT MIN(Fecha), MAX(Fecha) FROM registro_entradas;
SELECT MIN(Fecha), MAX(Fecha) FROM registro_salidas;
-- esperado: 2026-08-12 a 2026-08-31 (entradas), 2026-08-21 a 2026-09-01 (salidas), para los registros migrados

-- 10. rol_permiso de Desarrollo permanece intacto (no se tocó durante la migración de datos)
SELECT COUNT(*) FROM rol_permiso; -- esperado: 35 (el mismo valor que antes de migrar)
```

---

## 18. Pruebas funcionales post-migración

- **Login:** Juan, Reinaldo y Caterine pueden iniciar sesión con su clave real de Producción (ya rehasheada).
- **Usuarios/Roles/Permisos:** un usuario con rol Almacenero puede registrar entradas/salidas; no puede gestionar usuarios ni roles.
- **Materiales:** el saldo mostrado en el listado coincide con el saldo real conocido en Producción para una muestra representativa (al menos los 10 materiales de mayor movimiento).
- **Proyectos/Ubicaciones:** al registrar una salida nueva, el árbol de Ubicación permite navegar Proyecto → Etapa → Manzana → Casa para los 4 proyectos reales, sin encontrar nodos de Torre/Piso/Apartamento de prueba.
- **Entradas/Salidas históricas:** el Kardex de un material migrado muestra el historial replicado con las mismas fechas/cantidades que Producción.
- **Ajustes de inventario:** el módulo permite crear un ajuste nuevo sobre un material real sin conflicto con la `UNIQUE KEY` de apertura (`ajuste_inventario.AperturaUnica`).
- **Solicitudes:** el flujo de creación/aprobación/entrega de una solicitud nueva funciona sobre materiales y proyectos reales.
- **Informes:** los informes de entrada/salida por fecha, por material, y el informe general, no muestran ningún material o proyecto de prueba (Torre/Piso/Apartamento, "CC san pablo" si se decide eliminarlo, etc.).
- **Dashboard / Kardex por usuario:** los 3 usuarios reales aparecen con su actividad correcta.
- **Auditoría:** las acciones nuevas after-migración quedan registradas correctamente (el log de pruebas viejo ya fue archivado/limpiado según la decisión de la sección 12).
- **Correos de alerta de stock mínimo:** con el catálogo real cargado, provocar una salida que deje un material en su mínimo real y confirmar que la alerta se dispara al destinatario configurado en `notificacion_destinatario` (ver conversación previa sobre problemas de entrega de estos correos — validar spam/DKIM del dominio antes de confiar en que "no llegó" es un bug de esta migración).
- **PDFs:** el comprobante de salida (`SalidaMaterialPDF.php`) genera correctamente con contratista/proyecto/ubicación reales.

---

## 19. Riesgos

| Riesgo | Nivel | Mitigación |
|---|---|---|
| Emparejar `material` por `Codigo` en vez de por `Descripcion` y fusionar dos productos distintos | 🔴 Alto | Usar exclusivamente `Descripcion` normalizada como clave; `Codigo` solo como señal de apoyo, nunca de decisión (sección 9.1) |
| Falsos negativos en el emparejamiento de `material.Descripcion` por la normalización manual de comillas/tildes hecha en este análisis | 🟡 Medio | Repetir la comparación con el dump real de Producción, sin retipear, antes de ejecutar nada |
| Asumir `Producción.ID = Desarrollo.ID` en cualquier FK | 🔴 Alto | Tablas de mapeo explícitas por catálogo (sección 13), nunca copiar el ID tal cual |
| Perder trazabilidad de auditoría real al vaciar la tabla `auditoria` de prueba | 🟡 Medio | Exportar antes de vaciar (sección 12) |
| Reescribir `rol_permiso` con la matriz simplificada de Producción (que no la tiene) | 🔴 Alto si ocurriera | No tocar `rol_permiso` en absoluto durante esta migración (sección 6) |
| Borrar usuarios/proyectos/proveedores que en realidad son datos reales nuevos de Desarrollo, no de prueba | 🟡 Medio | Ningún borrado sin decisión explícita del usuario para cada ítem marcado "requiere decisión" |
| `ubicacion.ClaveUnicidad` (columna generada, `UNIQUE`) rechace un `INSERT` durante el replay si dos nodos migrados calzan sin querer | 🟢 Bajo | Es una protección del propio esquema; si dispara, es una señal correcta de duplicado a revisar, no un bug a saltarse |
| `ajuste_inventario.AperturaUnica` (única apertura por material) impida migrar un ajuste histórico de "APERTURA" si ya se insertó otro | 🟢 Bajo | Producción no tiene el concepto de ajustes; no debería generarse ningún conflicto salvo que se decida crear aperturas artificiales, lo cual el prompt prohíbe explícitamente (no inventar movimientos) |
| Contraseñas cortas/predecibles heredadas de Producción quedan vigentes tal cual | 🟢 Bajo | Fuera del alcance de esta migración de datos; recomendar cambio de clave como mejora de producto aparte |

---

## 20. Plan de rollback

1. **Antes de cualquier cambio:** tomar un `mysqldump` completo de la base `inventario` de Desarrollo tal como está hoy (incluyendo los datos de prueba, por si alguna decisión de "es dato real" resulta equivocada y hay que recuperarlo).
2. Ejecutar toda la migración dentro de una única transacción explícita por etapa (o, si el volumen de filas lo permite, una sola transacción para todo el proceso), de modo que un fallo a mitad de camino no deje la base en un estado mixto.
3. Si algo falla o una validación de la sección 17 no pasa: `ROLLBACK` de la transacción en curso; si ya se hizo `COMMIT` por etapas, restaurar el `mysqldump` del paso 1 completo (es una BD de Desarrollo local, no hay usuarios concurrentes que se vean afectados por el `DROP`/`RESTORE`).
4. Producción, en todo momento, permanece de solo lectura para este proceso — el rollback nunca la involucra porque nunca se le escribió nada.

---

## Resumen de puntos que requieren decisión del usuario antes de ejecutar

1. ¿Cómo se modelan `Urbanismo` y `Traspaso` en el árbol de `ubicacion` (sección 3.5)?
2. ¿Se reclasifica el movimiento anómalo de `Casa 4 / Etapa 4` a `Etapa 5` o se conserva tal cual (sección 3.5)?
3. ¿"Amin Narvaez" (Producción) y "Construcciones narvaez camacho" (Desarrollo) son el mismo contratista (sección 9.2)?
4. ¿"Local D1" (Producción) y "Tienda D1" (Desarrollo) son el mismo proyecto (sección 9.5)?
5. ¿El proyecto "CC san pablo" (solo en Desarrollo) es real o de prueba (sección 9.5 / 14)?
6. ¿El proveedor "Andamios del sur" (solo en Desarrollo) es real o de prueba (sección 9.6 / 14)?
7. Clasificación material por material de los 193 materiales de Desarrollo sin match en Producción (sección 9.1) — requiere revisión del negocio, no se puede automatizar.
8. ¿Se elimina o se desactiva el usuario de prueba "Pedro Perez" (sección 5)?
9. ¿Se exporta y vacía el log de `auditoria` de pruebas, o se conserva tal cual (sección 12)?
10. Confirmar el mapeo de rol "Almacenista" (Producción) → "Almacenero" (Desarrollo) (sección 6).
