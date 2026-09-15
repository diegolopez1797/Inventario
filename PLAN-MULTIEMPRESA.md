# Plan de Implementación Multiempresa — Berdez

> **Modo de ejecución de esta auditoría: SOLO ANÁLISIS.** Ningún archivo PHP/JS/HTML/CSS fue modificado, ninguna sentencia `INSERT`/`UPDATE`/`DELETE`/`ALTER TABLE` fue ejecutada, no hubo commits ni push. Todas las consultas usadas para esta auditoría fueron `SELECT` de solo lectura contra `information_schema` y las tablas de catálogo. El único archivo creado es este mismo documento.
>
> Etiquetas usadas en todo el documento: **[HECHO]** = comprobado directamente en el código o la base de datos real. **[RECOMENDACIÓN]** = propuesta de diseño. **[RIESGO]** = problema detectado que persistirá o se agravará si no se atiende. **[DECISIÓN PENDIENTE]** = el propietario del sistema debe elegir antes de implementar.

---

## 1. Resumen ejecutivo

[HECHO] Berdez es hoy una aplicación PHP 7.2 orientada a objetos, sin Composer/autoloader, con una arquitectura de despacho simple (`index.php → Views/Layouts/layout.php → routing.php → Controller → Model → PDO`). Sirve **una sola empresa** (Constructora Berdez) y **ninguna tabla, modelo, controlador ni sesión tiene hoy el concepto de "empresa"**. Los 3 agentes de auditoría que revisaron exhaustivamente los 34 Controllers, los 31 Models y todos los reportes/vistas coinciden en un mismo hallazgo estructural: no es que falte un filtro en los reportes, es que **prácticamente ningún catálogo base tiene ninguna columna de alcance** — `material`, `almacen`, `contratista`, `destino`, `rubro`, `proveedor`, `usuario`, `rol`, `proyecto` y `tipo_ubicacion` son hoy tablas 100% globales y compartidas, consultadas con `SELECT * FROM <tabla>` sin `WHERE` en sus métodos `all()`, y editables/eliminables por cualquier `ID` crudo recibido por GET/POST sin ninguna verificación de pertenencia.

[RIESGO] Se identificó además **un hallazgo de seguridad activo, no solo teórico**: varios `Controllers` ya presentan patrones de IDOR (Insecure Direct Object Reference) puro *dentro del modelo actual de un solo tenant* — por ejemplo `UsuarioController::update()` reconstruye un `Usuario` completo (incluyendo hash de contraseña y `RolID`) a partir de un `$_POST['id']` sin validar que ese `id` sea "razonable" en ningún sentido, y `SolicitudController::aprobar()`/`rechazar()` no llaman a `Permiso::usuarioPuede()` en absoluto. Hoy esto es menos grave porque solo hay una empresa y el acceso ya está detrás de rol/permiso; en un esquema multiempresa, estos mismos patrones se convierten directamente en fugas y tomas de cuenta **entre empresas**.

[RECOMENDACIÓN] El límite de aislamiento natural (el "tenant") debe ser una nueva entidad `Empresa` de la que casi todo cuelga **directamente**, no a través de una jerarquía `Empresa → Almacén → Proyecto` como sugería el planteamiento inicial — porque, hallazgo clave: **`almacen` no tiene ninguna relación con `proyecto` en el esquema actual** (confirmado leyendo `Model/Almacen.php` completo: no existe columna `ProyectoID`; hay un único almacén "principal" global identificado por `Tipo`, no por proyecto). El plan detallado, con fases, archivos afectados por fase, plan de pruebas y criterios de aceptación, se desarrolla en las secciones siguientes.

---

## 2. Estado actual de la arquitectura

[HECHO] Flujo real (verificado en `index.php`, `Views/Layouts/layout.php`, `routing.php`):

```text
index.php
    → Views/Layouts/layout.php   (session_start(), requiere TODOS los Model/*.php de una vez, arma el <head>)
        → cabeceraNueva.php       (menú, logo, gating por Permiso::usuarioPuede())
        → routing.php             (whitelist $controllers[...], valida CSRF en $accionesMutantes, hace dispatch)
            → Controllers/{X}Controller.php  (valida permiso, arma objetos, llama al Model)
                → Model/{X}.php   (PDO::prepare/bindValue/execute, sin ORM)
                    → MySQL/MariaDB (`inventario`)
```

No hay autoloader: cada `Controller` hace su propio `require_once('Model/...')`, y `routing.php` tiene un `switch` manual por cada controlador (`routing.php:57-167`). No hay capa base común entre Models (cada uno es una clase independiente con métodos `static`); la única dependencia compartida es el singleton `Db::getConnect()` (`connection.php`).

### Tabla de inventario real del proyecto

| Componente | Cantidad real | Observaciones |
|---|---:|---|
| Controllers | **34** | Confirmado por listado de directorio. De estos, 6 son scripts FPDF standalone invocados fuera de `routing.php` (`EntradaMaterialPDF.php`, `InformeEntradaMaterialPDF.php`, `SalidaMaterialPDF.php`, `InformeSalidaMaterialPDF.php`, `InformeSalidaPorUbicacionPDF.php`, `MovimientosPDF.php`), y 2 son archivos vacíos de 0 bytes, no enrutados (`MaterialRegistroEntradasController.php`, `MaterialRegistroSalidasController.php` — código muerto). |
| Models | **31** | Uno por archivo, sin excepción; ninguno hereda de una clase base. |
| Views | **65 archivos PHP en 25 carpetas** | Bajo `Views/`, más `Views/Layouts/layout.php` y `cabeceraNueva.php`. |
| Tablas BD | **27** | Esquema `inventario`, confirmado vía `information_schema.TABLES`. 3 de ellas (`area`, `casa`, `manzana`) son remanentes del modelo antiguo, ya superadas por `ubicacion`, pero **siguen existiendo como FK reales** en `material_registro_salidas` (columnas `CasaID`/`ManzanaID`/`AreaID`, además de la columna moderna `UbicacionID`). |
| Configuración | `connection.php` (PDO singleton, credenciales fijas), `mail.config.php` (SMTP), `Csrf.php`, `Html.php` (helpers `h()`/`flash()`) | No hay archivo de configuración por entorno (dev/prod se distingue solo por qué credenciales tiene cada `connection.php` desplegado). |
| Endpoints AJAX/API | **0** | Confirmado por búsqueda exhaustiva de `$.ajax`, `$.get(`, `$.post(`, `fetch(`, `XMLHttpRequest` en todo `Views/` y `.js` — los únicos "hits" fueron falsos positivos (URLs de CDN que contienen la palabra `ajax`, y `PDOStatement::fetch()`). La aplicación es 100% full-page: cada acción es un formulario GET/POST o un `window.location.href` server-side. |

---

## 3. Modelo actual de datos

[HECHO] Las 27 tablas, agrupadas por rol:

- **Catálogos maestros globales (sin relación con proyecto/empresa hoy):** `usuario`, `rol`, `permiso`, `contratista`, `destino`, `rubro`, `proveedor`, `almacen`, `material`, `tipo_ubicacion`.
- **Transaccionales:** `registro_entradas` / `registro_salidas` (cabeceras), `ajuste_inventario`, `solicitud`.
- **Detalle (dependen 100% de una cabecera):** `material_registro_entradas`, `material_registro_salidas`, `solicitud_detalle`.
- **Relación N:N:** `rol_permiso`, `material_almacen` (PK compuesta `MaterialID+AlmacenID`).
- **Configuración global de instancia única:** `configuracion` (una sola fila, `ID` fijo = 1, guarda la ruta del logo).
- **Auditoría:** `auditoria` (polimórfica: `Entidad` + `EntidadID` genéricos, sin FK real a la tabla auditada).
- **Jerárquica propia:** `ubicacion` (auto-referencial vía `PadreID`, ancla en `ProyectoID`).
- **Legacy / vestigiales:** `area`, `casa`, `manzana` — reemplazadas conceptualmente por `ubicacion`, pero `material_registro_salidas` todavía tiene sus FKs (`CasaID`, `ManzanaID`, `AreaID`) vivas junto a la nueva `UbicacionID`.

### Tabla completa de las 27 tablas

| Tabla | Propósito | PK | FK reales (confirmadas en `information_schema.KEY_COLUMN_USAGE`) | Relación con empresa hoy | ¿Necesita EmpresaID? | Justificación |
|---|---|---|---|---|---|---|
| `ajuste_inventario` | Ajustes manuales de stock (apertura/conteo/pérdida/daño) | ID | `MaterialID→material`, `UsuarioID→usuario` | Ninguna | **Sí (directo)** | Escritura de saldo; `Model/AjusteInventario.php` no tiene `searchById`, pero `all()` vuelca todo el historial global. |
| `almacen` | Catálogo de almacenes físicos | ID | — | Ninguna (ni siquiera con `proyecto`) | **Sí (directo)** | Es un catálogo puramente global hoy; `Almacen::principal()` asume un único almacén "principal" para todo el sistema. |
| `area` *(legacy)* | Etapa/área (modelo antiguo de ubicación) | ID | — | Ninguna | No — deprecar | Superada por `ubicacion`; conviene retirarla, no retrofitear. |
| `auditoria` | Log de acciones administrativas | ID | `UsuarioID→usuario` | Ninguna | **Sí (directo)** | `Entidad`/`EntidadID` son polimórficos (no FK real a la tabla auditada) → inferir empresa vía la entidad auditada exigiría un JOIN distinto por cada una de las ~15 entidades que se auditan; columna directa es la única opción práctica. |
| `casa` *(legacy)* | Casa/unidad (modelo antiguo) | ID | — | Ninguna | No — deprecar | Igual que `area`. |
| `configuracion` | Fila única de configuración (logo) | ID (fijo=1) | — | Ninguna | **Sí** (o fusionar con `empresa`) | Ver sección 17 — recomendado fusionar con la tabla `empresa` en vez de mantenerla aparte. |
| `contratista` | Catálogo de contratistas | ID | — | Ninguna | **Sí (directo)** | Catálogo global hoy, referenciado por `registro_salidas.ContratistaID`. |
| `destino` | Catálogo de destinos de material | ID | — | Ninguna | **Sí (directo)** | Catálogo global. |
| `manzana` *(legacy)* | Manzana (modelo antiguo) | ID | — | Ninguna | No — deprecar | Igual que `area`/`casa`. |
| `material` | Catálogo de materiales + saldo agregado | ID | — | Ninguna | **Sí (directo)** | Núcleo del inventario; `Codigo` tendría que pasar de único-global a único-por-empresa. |
| `material_almacen` | Saldo de un material en un almacén | `MaterialID+AlmacenID` | `→material`, `→almacen` | Hereda de ambos padres | **No** (hereda) | Basta con que `material` y `almacen` tengan `EmpresaID` y con validar en el `INSERT` que ambos coinciden. |
| `material_registro_entradas` | Línea de una entrada | ID | `→material`, `→registro_entradas`, `→destino` | Hereda de `registro_entradas` | **No** (hereda) | Igual razonamiento. |
| `material_registro_salidas` | Línea de una salida (con FKs legacy + `UbicacionID`) | ID | `→material`, `→registro_salidas`, `→destino`, `→rubro`, `→area/casa/manzana` (legacy), `→ubicacion` | Hereda de `registro_salidas` | **No** (hereda) | Igual razonamiento; además candidato a limpiar las FKs legacy en una fase posterior. |
| `notificacion_destinatario` | Destinatarios de alertas de stock mínimo | ID | — | Ninguna | **Sí (directo)** | Cada empresa necesita su propia lista de destinatarios de correo. |
| `permiso` | Catálogo de códigos de permiso (`rol.gestionar`, etc.) | ID | — | N/A | **No — global** | Es un catálogo de *capacidades del producto*, igual para todas las empresas; lo que varía por empresa es qué `Rol` tiene cada permiso, no el catálogo de permisos en sí. |
| `proveedor` | Catálogo de proveedores | ID | — | Ninguna | **Sí (directo)** | Catálogo global hoy. |
| `proyecto` | Proyecto de construcción | ID | `ResponsableID→usuario` | Ninguna | **Sí (directo)** | **Tabla ancla**: `ubicacion`, `registro_salidas`, `solicitud`, `InformeDetallado` ya la usan como límite de alcance "de facto" — si `proyecto` mismo no tiene `EmpresaID`, todos esos filtros son una falsa sensación de aislamiento. |
| `registro_entradas` | Cabecera de entrada de material | ID | `UsuarioID→usuario`, `ProveedorID→proveedor` | Hereda de `usuario`/`proveedor`, pero conviene directo | **Sí (directo, recomendado)** | Tabla transaccional de alto volumen — se recomienda columna directa por rendimiento y defensa en profundidad, aunque sea técnicamente derivable. |
| `registro_salidas` | Cabecera de salida de material | ID | `UsuarioID→usuario`, `ContratistaID→contratista`, `ProyectoID→proyecto` | Ya tiene `ProyectoID` | **Sí (directo, recomendado)** | Igual razonamiento; es el mayor punto de escritura del sistema. |
| `rol` | Catálogo de roles (Administrador, Almacenero...) | ID | — | Ninguna | **Sí (directo)** | Subyace a todo `Permiso::usuarioPuede()`; cada empresa debe poder tener su propia matriz de roles/permisos sin afectar a otras. |
| `rol_permiso` | Matriz Rol↔Permiso | `RolID+PermisoID` | `→rol`, `→permiso` | Hereda de `rol` | **No** (hereda) | Con `rol.EmpresaID`, esta tabla queda automáticamente acotada. |
| `rubro` | Catálogo de actividades/rubros | ID | — | Ninguna | **Sí (directo)** | Catálogo global hoy. |
| `solicitud` | Solicitud de material (workflow aprobar/rechazar/entregar) | ID | `UsuarioSolicitaID→usuario`, `ProyectoID→proyecto`, `UbicacionID→ubicacion`, `UsuarioApruebaID→usuario`, `RegistroSalidasID→registro_salidas` | Ya tiene `ProyectoID` | **Sí (directo, recomendado)** | Igual razonamiento que `registro_salidas`; además es la tabla con más lógica de negocio (estados) y más superficie de IDOR encontrada. |
| `solicitud_detalle` | Línea de una solicitud | ID | `→solicitud`, `→material` | Hereda de `solicitud` | **No** (hereda) | — |
| `tipo_ubicacion` | Catálogo sugerido de tipos de ubicación | ID | — | Ninguna | **Sí (directo)** | Cada empresa puede querer su propio vocabulario (Torre/Piso vs Etapa/Manzana/Casa). |
| `ubicacion` | Árbol de ubicaciones por proyecto | ID | `ProyectoID→proyecto`, `PadreID→ubicacion` (auto-ref) | Ya tiene `ProyectoID` | **Sí (directo, recomendado)** | Alto volumen (54 nodos hoy, crecerá con cada proyecto); columna directa simplifica los recorridos de árbol sin JOIN extra a `proyecto` en cada paso. |
| `usuario` | Usuarios del sistema | ID | `RolID→rol` | Ninguna | **Sí (directo)** | **Tabla ancla de autenticación**: `Identificacion` es hoy única en toda la tabla — estructuralmente incompatible con multiempresa tal cual está (ver sección 9). |

---

## 4. Modelo multiempresa recomendado

[RECOMENDACIÓN] `Empresa` es una entidad **de primer nivel**, de la que cuelgan directamente `Usuario`, `Rol`, `Proyecto`, `Almacen`, `Material`, `Contratista`, `Destino`, `Rubro`, `Proveedor`, `TipoUbicacion`, `NotificacionDestinatario`, y (vía `Proyecto`) `Ubicacion`, `RegistroEntradas`/`RegistroSalidas`, `Solicitud`, `AjusteInventario`, `Auditoria`.

[HECHO] La jerarquía `Empresa → Almacén → Proyecto` planteada como hipótesis en la solicitud **no encaja con el código real**: `Almacen` (`Model/Almacen.php`) no tiene ninguna columna que lo relacione con `Proyecto`, y el comentario del propio código (`Model/Almacen.php:96-98`) confirma que hoy "la operación real sigue trabajando sobre `material.Saldo`" — es decir, el sistema multi-almacén existe en el esquema pero casi no se usa operacionalm, y desde luego no está anidado dentro de proyecto.

[RECOMENDACIÓN] La jerarquía correcta, basada en las FKs reales:

```text
Empresa
 ├── Usuario (RolID → Rol de la misma Empresa)
 ├── Rol (con su propia matriz Rol_Permiso; Permiso sigue siendo catálogo global)
 ├── Almacen (catálogo global DENTRO de la empresa, no anidado en Proyecto)
 ├── Material (catálogo global DENTRO de la empresa)
 ├── Contratista / Destino / Rubro / Proveedor / TipoUbicacion (catálogos DENTRO de la empresa)
 ├── NotificacionDestinatario
 ├── Configuracion (o fusionada en la fila de Empresa)
 └── Proyecto
       └── Ubicacion (árbol)
       └── RegistroEntradas / RegistroSalidas (¡usan Material y Almacen GLOBALES de la empresa, no del proyecto!)
       └── Solicitud
```

[DECISIÓN PENDIENTE] Si a futuro se quiere que **cada proyecto tenga su propio subconjunto de almacenes** (p. ej. "Almacén principal" y "Almacén obra" por proyecto, como ilustra el ejemplo de la sección 9 de la solicitud), eso es un cambio estructural adicional (agregar `ProyectoID` a `almacen`) que hoy **no existe ni siquiera a nivel de una sola empresa** — es una mejora de multi-almacén-por-proyecto independiente de multiempresa, y se recomienda NO mezclarla en la misma fase para no acoplar dos rediseños distintos.

---

## 5. Tabla Empresa propuesta

[RECOMENDACIÓN]

```sql
CREATE TABLE empresa (
  ID            INT NOT NULL AUTO_INCREMENT,
  RazonSocial   VARCHAR(150) NOT NULL,
  NIT           VARCHAR(30)  NOT NULL,
  LogoPath      VARCHAR(255) DEFAULT NULL,   -- reemplaza a `configuracion` (ver sección 17)
  Activo        TINYINT(1)   NOT NULL DEFAULT 1,
  FechaCreacion DATETIME     NOT NULL,
  PRIMARY KEY (ID),
  UNIQUE KEY uk_empresa_nit (NIT)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
```

`Activo` permite suspender una empresa (impago, baja) sin borrar sus datos — relevante para CA-ME-07/08 (agregar/dar de baja una empresa sin tocar el esquema).

---

## 6. Tablas que requieren EmpresaID

[RECOMENDACIÓN] Columna directa `EmpresaID INT NOT NULL` + `FOREIGN KEY (EmpresaID) REFERENCES empresa(ID)`, en estas **18 tablas**:

`usuario`, `rol`, `proyecto`, `almacen`, `material`, `contratista`, `destino`, `rubro`, `tipo_ubicacion`, `proveedor`, `notificacion_destinatario`, `configuracion` (o eliminada, ver §17), `registro_entradas`, `registro_salidas`, `ubicacion`, `solicitud`, `ajuste_inventario`, `auditoria`.

Justificación por tabla ya está en la tabla de la sección 3; no se repite aquí para evitar duplicar el documento.

---

## 7. Tablas que NO requieren EmpresaID

[RECOMENDACIÓN]

- **Heredan de su padre (tablas de detalle/relación N:N)**: `material_almacen`, `material_registro_entradas`, `material_registro_salidas`, `rol_permiso`, `solicitud_detalle`. Basta con que sus tablas padre queden acotadas y con validar en el `INSERT` que ambos lados de cada relación pertenecen a la misma empresa.
- **Catálogo global de producto**: `permiso` — el conjunto de capacidades del sistema (`rol.gestionar`, `catalogo.ver`, etc.) es igual para todas las empresas; solo `rol_permiso` (vía `rol.EmpresaID`) varía por empresa.
- **Legacy, candidatas a retirar en vez de retrofitear**: `area`, `casa`, `manzana`.

---

## 8. Relaciones afectadas

[RECOMENDACIÓN] Cambios de FK necesarios:

1. `usuario.RolID → rol.ID`: agregar restricción a nivel de aplicación (no de FK de MySQL, que no puede expresar "misma EmpresaID en ambos lados") de que `rol.EmpresaID = usuario.EmpresaID`.
2. `proyecto.ResponsableID → usuario.ID`: mismo tipo de restricción aplicativa (el responsable debe ser un usuario de la misma empresa que el proyecto).
3. Todas las FKs que hoy apuntan a catálogos globales (`registro_salidas.ContratistaID`, `material_registro_entradas.DestinoID`, `material_registro_salidas.RubroID`, etc.) necesitan la misma validación aplicativa: **la fila referenciada debe pertenecer a la misma empresa que la fila que la referencia**. MySQL no puede expresar esto de forma declarativa con una FK compuesta simple sin duplicar `EmpresaID` en cada tabla hija — de ahí la recomendación de la sección 6 de poner `EmpresaID` directo en las tablas transaccionales de alto volumen, en vez de confiar solo en JOINs.
4. `material_registro_salidas.CasaID/ManzanaID/AreaID` (legacy): si se retiran `area`/`casa`/`manzana`, estas 3 columnas deben quedar `NULL`-eables o eliminarse en una fase de limpieza aparte (no es parte del camino crítico multiempresa).

---

## 9. Usuarios, roles y permisos

### Modelo actual [HECHO]

- Login: `LoginController::verificar()` (`Controllers/LoginController.php:18-41`) recibe `$_POST['identificacion']`/`clave`, llama `Usuario::verificarUsuario()` (`Model/Usuario.php:113-127`, `password_verify()` contra `usuario.Clave` buscada por `Identificacion` — **`Identificacion` es única en toda la tabla, sin ningún otro criterio**).
- Sesión: `LoginController::entrar()` (`:43-48`) hace `session_regenerate_id(true)` y guarda el objeto `Usuario` completo en `$_SESSION['usuario']`.
- Permisos: `Permiso::usuarioPuede($codigo)` (`Model/Permiso.php:89-104`) resuelve `$_SESSION['usuario']->getRolId()` y comprueba `rol_permiso ⋈ permiso` — **no hay ningún otro criterio de alcance**.
- Roles actuales en BD: 6 roles de negocio (Administrador, Almacenero, Residente de Obra, Compras, Gerencia, Consulta), todos con permisos globales, sin relación con proyecto ni empresa.

### Modelo A — Usuario pertenece a una sola empresa

```text
Usuario.EmpresaID → Empresa
```

**Ventajas**: cambio mínimo (una columna + un `WHERE` extra en login); encaja de inmediato con `Permiso::usuarioPuede()` (solo hay que agregar el chequeo de que `rol.EmpresaID = usuario.EmpresaID`); es el modelo mental correcto para el caso de uso descrito (empleados de una sola constructora). **Desventajas**: si en el futuro una misma persona (p. ej. un contador externo) necesita ver 2 empresas, requiere una cuenta de usuario duplicada por empresa.

### Modelo B — Usuario puede pertenecer a varias empresas

```text
Usuario → UsuarioEmpresa → Empresa
```

**Ventajas**: resuelve el caso de personal compartido entre empresas (consultores, auditores, un dueño con varias constructoras) sin duplicar cuentas; es el modelo "correcto" a largo plazo para una plataforma SaaS real. **Desventajas**: exige un selector de "empresa activa" en cada sesión, una tabla adicional, y que **todo** el código que hoy asume `$_SESSION['usuario']->getEmpresaId()` en realidad lea `$_SESSION['empresaActivaId']` (un dato de sesión, no del usuario) — más superficie a tocar desde el día 1.

### [DECISIÓN PENDIENTE] → [RECOMENDACIÓN]

Implementar **Modelo A** en la primera versión (prioriza "facilidad de implementación" y "no complejidad innecesaria", sección 17 de la solicitud), pero diseñar la columna de sesión como `$_SESSION['empresaActivaId']` (no como un simple getter del objeto `Usuario`) desde el principio. Esto deja una vía de evolución limpia a Modelo B más adelante: se agrega `UsuarioEmpresa`, se migra `usuario.EmpresaID` como la primera fila de esa tabla por usuario, y se agrega un selector de empresa solo para las cuentas que tengan más de una fila — **sin romper nada de lo construido en el Modelo A**.

**Roles por empresa**: se recomienda que `rol` tenga `EmpresaID` (cada empresa gestiona su propia copia de "Administrador", "Almacenero", etc., y puede editar sus permisos sin afectar a otras — exactamente el ejemplo de la sección 11 de la solicitud). El catálogo `permiso` (los códigos de capacidad) permanece global, como un catálogo de producto.

---

## 10. Contexto de empresa

[RECOMENDACIÓN] Flujo propuesto, mínimamente invasivo sobre la arquitectura actual:

1. **Sesión**: `LoginController::entrar()` agrega `$_SESSION['empresaActivaId'] = $usuario->getEmpresaId();` junto al objeto `Usuario` ya existente.
2. **Helper global**: nueva función `empresaActual()` en `Html.php` (junto a `h()`/`flash()`, que ya se cargan en cada request vía `layout.php`) que devuelve `$_SESSION['empresaActivaId']` o lanza una excepción si no hay sesión — un único punto de verdad, sin crear una clase nueva ni un framework.
3. **Controller → Model**: cada método de Model que hoy recibe un `$id` (`searchById`, `update`, `delete`, `all`) pasa a recibir también `$empresaId` — el Controller lo obtiene con `empresaActual()` y lo pasa explícitamente (no vía variable global implícita), manteniendo el estilo explícito ya usado en el resto del código.
4. **Protección de recursos individuales**: cada `searchById($id, $empresaId)` cambia su SQL a `WHERE ID = :id AND EmpresaID = :empresaId` — si la fila pertenece a otra empresa, el `SELECT` devuelve vacío y el Controller debe tratarlo como "no encontrado" (nunca revelar si existe pero pertenece a otra empresa, para no filtrar información por diferencia de mensaje de error — CA-ME-09).
5. **Protección de reportes**: los métodos como `Movimientos::buscar()`/`InformePorUsuario::resumen()` que hoy tienen filtros **opcionales**, pasan a tener `$empresaId` como **parámetro obligatorio** (no opcional), replicando el patrón que `InformeDetallado::porUbicacion()` ya usa correctamente con `ProyectoID` (`Model/InformeDetallado.php:18`).
6. **Endpoints**: no aplica hoy (no hay AJAX/API), pero cualquier endpoint JSON que se agregue en el futuro debe nacer usando este mismo helper desde el primer commit.
7. **Administradores**: un "superadmin" de la plataforma SaaS (soporte técnico de Berdez-como-producto, no de una constructora) necesita un mecanismo aparte para operar sin `EmpresaID` fijo — se recomienda un rol especial fuera de la tabla `rol` por-empresa (p. ej. un flag `usuario.EsSuperAdmin` global, fuera del alcance de esta fase, marcado como [DECISIÓN PENDIENTE] para cuando se diseñe soporte multiempresa).

---

## 11. Controllers afectados

[HECHO] Tabla completa de los 34 archivos en `Controllers/`, producida por auditoría exhaustiva línea por línea:

| Controller | Métodos con ID sin validar (archivo:línea) | Permiso verificado | Riesgo | Justificación |
|---|---|---|---|---|
| AjusteInventarioController.php | `register()` `$_GET['material']` L66-67; `save()` `$_POST['material']` L81,100 | `inventario.ajustar` | **CRÍTICO** | Escribe un ajuste de stock contra cualquier `MaterialID` posteado, sin verificar pertenencia. |
| AlmacenController.php | `updateshow()` L64; `update()` L76-78; `delete()` L92-97 | `catalogo.gestionar`/`catalogo.ver` | ALTO | CRUD por ID crudo sin chequeo de dueño. |
| AuditoriaController.php | Solo filtros de lectura (`usuarioId`,`entidad`,`accion`) | `auditoria.ver` | ALTO | Todo el log de auditoría se consulta sin alcance de empresa. |
| ConfiguracionController.php | `guardarLogo()` escribe sobre la fila única global `Configuracion::ID_UNICO` | `configuracion.gestionar` | **CRÍTICO** | Una sola fila de configuración para *todo* el sistema — cualquier empresa sobrescribe el logo de todas. |
| ContratistaController.php | `updateshow()`/`update()`/`delete()`/`search()` por ID crudo | `catalogo.gestionar`/`ver` | ALTO | CRUD sin dueño. |
| DashboardController.php | Sin ID manipulable, pero agregaciones sin filtro | `dashboard.ver` | MEDIO | Panel de inicio mostraría totales de todas las empresas. |
| DestinoController.php | `updateshow()`/`update()`/`delete()`/`search()` | `catalogo.gestionar`/`ver` | ALTO | Igual patrón que Contratista/Proveedor/Rubro. |
| EntradaMaterialPDF.php *(standalone, no enrutado)* | Lee `$_SESSION['idEntrada']`, sin validación propia | Ninguno | ALTO | Confía ciegamente en sesión previa. |
| InformeDetalladoController.php | `detalle()` `$_GET['proyecto']`/`ubicacion`/`material'` L77-79 | `catalogo.ver` | **CRÍTICO** | Drill-down completo por 3 IDs crudos combinables. |
| InformeEntradaController.php | `detalle()` `$_GET['id']`/`usuario'` L26-28 | `catalogo.ver` | **CRÍTICO** | Documento completo de entrada por ID crudo; alimenta el PDF sin re-chequeo. |
| InformeEntradaMaterialPDF.php *(standalone)* | Lee `$_SESSION['idEntrada']` | Ninguno | **CRÍTICO** | Encadenado desde el hallazgo anterior. |
| InformePorUsuarioController.php | `detalle()` `$_GET['usuario']` L37 | `dashboard.ver` | **CRÍTICO** | Historial completo de movimientos de cualquier usuario por ID crudo. |
| InformeSalidaController.php | `detalle()` `$_GET['id']`/`usuario'` L26-28 | `catalogo.ver` | **CRÍTICO** | Espejo de InformeEntradaController para salidas. |
| InformeSalidaMaterialPDF.php *(standalone)* | Lee `$_SESSION['idSalida']` | Ninguno | **CRÍTICO** | Encadenado. |
| InformeSalidaPorUbicacionPDF.php *(standalone)* | Lee `$_SESSION['informeSalidaPorUbicacionGeneral']` | Ninguno | ALTO | Hereda el alcance (correcto, por proyecto) de `InformeDetalladoController::show()`. |
| KardexController.php | `buscar()` `$_POST['idMaterial']` L58-63 | `dashboard.ver` | **CRÍTICO** | Historial completo de movimientos de cualquier material por ID crudo. |
| LoginController.php | N/A (pre-auth) | N/A | MEDIO (arquitectural) | `Identificacion` sin desambiguar por empresa — bloqueador de diseño de login, no IDOR. |
| MaterialController.php | `updateshow()`/`update()`/`delete()` L93-138 | `catalogo.gestionar`/`ver` | **CRÍTICO** | CRUD del catálogo núcleo de inventario (incluye `Saldo`) sin dueño. |
| MaterialRegistroEntradasController.php | — (archivo vacío, 0 bytes) | N/A | BAJO | Código muerto, no enrutado — recomendado eliminar, no remediar. |
| MaterialRegistroSalidasController.php | — (archivo vacío, 0 bytes) | N/A | BAJO | Ídem. |
| MovimientosController.php | `show()` 5 IDs crudos por GET L32-37 | `catalogo.ver` | **CRÍTICO** | Agrega/filtra TODOS los movimientos del sistema por cualquier combinación de IDs. |
| MovimientosPDF.php *(standalone)* | Lee `$_SESSION['movimientosGeneral']` | Ninguno | **CRÍTICO** | Exporta el resultado sin alcance descrito arriba. |
| NotificacionDestinatarioController.php | `updateshow()`/`update()`/`activar()`/`desactivar()` | `notificacion.gestionar` | **CRÍTICO** | Tabla sin columna de alcance hoy; ver/editar/activar destinatarios de otra empresa. |
| ProveedorController.php | `updateshow()`/`update()`/`delete()`/`search()` | `catalogo.gestionar`/`ver` | ALTO | Mismo patrón CRUD. |
| ProyectoController.php | `updateshow()`/`update()`/`delete()`/`search()` L63-110 | `catalogo.gestionar`/`ver` | **CRÍTICO** | `Proyecto` es la tabla ancla que todo lo demás asume como límite — sin dueño propio, todo lo que "ya filtra por ProyectoID" es una ilusión de aislamiento. |
| RegistroEntradasController.php | `searchMaterial()` IDs de Proveedor/Destino sin chequeo | `entrada.registrar` | ALTO | Una entrada puede referenciar Proveedor/Destino de otra empresa. |
| RegistroSalidasController.php | `searchMaterial()`/`save()`: Contratista/Proyecto/Casa/Manzana/Area/Destino/Rubro/Ubicacion por `$_REQUEST` sin chequeo, L340-690 | `salida.registrar` | **CRÍTICO** | El mayor camino de escritura del sistema (decrementa stock) sin ningún chequeo de empresa en ninguna de sus FKs. |
| RolController.php | `updateshow()`/`update()`/`delete()`/`permisos()`/`guardarPermisos()` | `rol.gestionar` | **CRÍTICO** | `guardarPermisos()` reemplaza la matriz de permisos completa de cualquier `RolID` posteado — toma de control de rol entre empresas. |
| RubroController.php | `updateshow()`/`update()`/`delete()`/`search()` | `catalogo.gestionar`/`ver` | ALTO | Mismo patrón CRUD. |
| SalidaMaterialPDF.php *(standalone)* | Lee `$_SESSION['idSalida']` | Ninguno | **CRÍTICO** | Encadenado desde InformeSalidaController. |
| SolicitudController.php | `aprobar()`/`rechazar()` **sin `Permiso::usuarioPuede()` alguno**, L182-209; `procesarEntrega()` `$_POST['contratista']` sin chequeo | Parcial — bypassea el gate central | **CRÍTICO** | `aprobar`/`rechazar` confían solo en `Solicitud::usuarioPuedeAprobar()`, cuyo alcance (rol/proyecto) no está verificado contra empresa. |
| TipoUbicacionController.php | `updateshow()`/`update()`/`desactivar()` | `catalogo.gestionar`/`ver` | ALTO | Catálogo global sin alcance. |
| UbicacionController.php | `update()` valida solo que el nuevo padre comparta *Proyecto* (nunca que el Proyecto sea de la empresa); `delete()`/`desactivar()`/`generarMasivo*()` sin chequeo | `catalogo.gestionar`/`ver` | **CRÍTICO** | Buen patrón parcial (consistencia de árbol dentro de un proyecto) pero sin el chequeo de empresa que lo completaría. |
| UsuarioController.php | `update()` reconstruye el `Usuario` completo (incluye hash de clave y `RolID`) desde `$_POST['id']` sin chequeo, L89-116 | `usuario.gestionar` | **CRÍTICO — el más grave del sistema** | Permite toma de cuenta / escalamiento de privilegios entre empresas: sobrescribir nombre, rol o contraseña de un usuario de otra empresa. |

**Prioridad de remediación (según los propios auditores):** `UsuarioController::update()` y `RolController::guardarPermisos()` primero (toma de cuenta), luego `ProyectoController` (tabla ancla), luego los 6 scripts `*PDF.php` standalone (se cierran automáticamente arreglando `InformeEntradaController`/`InformeSalidaController::detalle()`), luego el resto del catálogo CRUD (patrón mecánico repetible).

---

## 12. Models afectados

[HECHO] Tabla completa de los 31 archivos en `Model/`:

| Model | Tabla | Métodos con ID sin filtro adicional | Métodos que devuelven todo sin filtro | Riesgo | Solución propuesta |
|---|---|---|---|---|---|
| AjusteInventario | ajuste_inventario | — | `all()` `:94-104` | ALTO | Agregar `EmpresaID` y propagarlo en `all()`/`paginado()`. |
| Almacen | almacen | `searchById` `:57-66`; `update` `:78-85`; `delete` `:87-94` | `all()` `:45-55` | **CRÍTICO** | Añadir `EmpresaID`, filtrar todo el CRUD; redefinir `principal()` por empresa. |
| Area *(legacy)* | area | `searchById`/`searchByIdUpdate`/`update`/`delete` | `all()` | **CRÍTICO** (si se reactivara) | Deprecar en vez de retrofitear. |
| Auditoria | auditoria | — | `all()` `:109-119`; `entidadesRegistradas()`; `accionesRegistradas()` | ALTO | Añadir `EmpresaID` a `registrar()` y a las consultas de listado. |
| Casa *(legacy)* | casa | Igual patrón que Area | `all()` | **CRÍTICO** (si se reactivara) | Deprecar. |
| Configuracion | configuracion | Fila única `ID=1` fija — no es input de usuario, pero es 1 sola fila global | N/A | **CRÍTICO estructural** | Migrar a clave primaria = `EmpresaID` (o fusionar con `empresa`). |
| Contratista | contratista | `searchById`/`searchByIdUpdate`/`update`/`delete` | `all()` | **CRÍTICO** | Añadir `EmpresaID`, filtrar todo el CRUD. |
| Dashboard | agrega material/registro_entradas/salidas/proyecto | — | `materialesCriticos()`; `movimientosRecientes()`; `proyectosActivos()` — las 3 sin WHERE | ALTO | Agregar parámetro `$empresaId` a las 3 consultas. |
| Destino | destino | `searchById`/`searchByIdUpdate`/`update`/`delete` | `all()` | **CRÍTICO** | Añadir `EmpresaID`. |
| InformeDetallado | (join, sin tabla propia) | — | — (ya exige `ProyectoID` obligatorio) | BAJO-MEDIO | Extender el filtro existente para validar que el proyecto sea de la empresa. |
| InformeMaterialEntrada | material_registro_entradas | `searchMaterialRegistroEntradas($idEntrada)` — filtra por FK, no por ID suelto | — | MEDIO | Depende de que `registro_entradas` quede acotado. |
| InformeMaterialSalida | material_registro_salidas | `searchMaterialRegistroSalidas($idEntrada)` — igual caso | — | MEDIO | Depende de `registro_salidas`. |
| InformePorUsuario | agrega registro_entradas/salidas/ajuste_inventario | — | `resumen()` — 3 sub-consultas sin WHERE de empresa | ALTO | Filtro obligatorio por `EmpresaID` en `resumen()`. |
| Kardex | agrega material_registro_*/ajuste_inventario/material | — | — (ya exige `MaterialID`) | MEDIO | Basta con que `Material` quede acotado. |
| Manzana *(legacy)* | manzana | Igual patrón que Area/Casa | `all()` | **CRÍTICO** (si se reactivara) | Deprecar. |
| Material | material | `searchById`; `update`; `delete`; `ingresoMaterial($id,...)`; `actualizarCosto($id,...)`; `searchByCodigo($codigo)` | `all()`; `sinMovimiento()` | **CRÍTICO** | Añadir `EmpresaID`; `Codigo` único pasa a `(EmpresaID, Codigo)`. |
| MaterialRegistroEntradas | material_registro_entradas | — | — (ya scoped por `Registro_EntradasID`) | MEDIO | Depende de `registro_entradas`. |
| MaterialRegistroSalidas | material_registro_salidas | — | — (ya scoped por `Registro_SalidasID`) | MEDIO | Depende de `registro_salidas`. |
| Movimientos | agrega material_registro_*/registro_*/material | — | `consultarEntradas`/`consultarSalidas` parten de `WHERE 1=1`; `proyectoId` es filtro **opcional** | ALTO | `EmpresaID` obligatorio, no opcional. |
| NotificacionDestinatario | notificacion_destinatario | `searchById`; `update`; `activar`; `desactivar` | `all()`; `activos()` | **CRÍTICO** | Añadir `EmpresaID`; `Correo` único pasa a `(EmpresaID, Correo)`. |
| Permiso | permiso/rol_permiso | `codigosPorRol($rolId)`; `asignarARol($rolId,...)` — `RolID` crudo | `all()` (catálogo de permisos) | ALTO | `permiso` queda global; `asignarARol` debe validar que `RolID` es de la empresa del usuario administrador. |
| Proveedor | proveedor | `searchById`/`searchByIdUpdate`/`update`/`delete` | `all()` | **CRÍTICO** | Añadir `EmpresaID`. |
| Proyecto | proyecto | `searchById`/`searchByIdUpdate`/`update`/`delete` | `all()` | **CRÍTICO** | Cambio de mayor apalancamiento del sistema — es la tabla ancla. |
| RegistroEntradas | registro_entradas | `searchEntrada($Id)` | — | **CRÍTICO** | Añadir `EmpresaID` (derivable de `UsuarioID`) y filtrar `searchEntrada`. |
| RegistroSalidas | registro_salidas | `searchSalida($Id)` | `validarExistencia()` delega en `Material::all()`, heredando su fuga | **CRÍTICO** | Añadir `EmpresaID`; reemplazar la llamada a `Material::all()` por versión acotada. |
| Rol | rol | `searchById`; `update`; `delete` (también borra `rol_permiso` por `RolID` crudo) | `all()` | **CRÍTICO** | Añadir `EmpresaID`; `usuario.RolID` solo puede apuntar a roles de su misma empresa. |
| Rubro | rubro | `searchById`/`searchByIdUpdate`/`update`/`delete` | `all()` | **CRÍTICO** | Añadir `EmpresaID`. |
| Solicitud | solicitud/solicitud_detalle | `searchById`; `detalle($solicitudId)`; `aprobar`; `rechazar`; `marcarEntregada` | `all()`; `pendientes()`; `aprobadas()` | **CRÍTICO** | `aprobar`/`rechazar`/`marcarEntregada` son escrituras de estado alcanzables solo con ID adivinado — verificar que `ProyectoID` es de la empresa del usuario antes de tocar el estado. |
| TipoUbicacion | tipo_ubicacion | `searchById`; `update`; `desactivar` | `all()` | **CRÍTICO/ALTO** | Añadir `EmpresaID`. |
| Ubicacion | ubicacion | `searchById`; `delete`; `desactivar`; `hijos($padreId)`; `ruta($id)` sin chequeo de empresa | `hojasConRuta($proyectoId=null)` — **sin argumento, es un `SELECT *` sin filtro** | **CRÍTICO** | Extender la validación padre↔proyecto ya existente (`calcularNivel()`) a todos los métodos; hacer `$proyectoId` obligatorio en `hojasConRuta()`. |
| Usuario | usuario | `searchByCodigoUser`; `delete`; `update`; `searchByIdUser`/`verificarUsuario` (por `Identificacion` global única) | `all()` | **CRÍTICO** | Añadir `EmpresaID`; `Identificacion` única pasa a `(EmpresaID, Identificacion)`; login debe resolver empresa antes de buscar. |

**Hallazgo transversal:** ningún Model tiene una clase base ni usa un trait común — cerrar esto exige tocar los 31 archivos uno por uno; no hay un único punto de intercepción donde inyectar el filtro automáticamente. Se recomienda, aun así, extraer un pequeño helper compartido (p. ej. una función `whereEmpresa($alias, $empresaId)` en un archivo `Model/ScopeEmpresa.php` de ~10 líneas) para que el patrón sea consistente entre los 18 modelos que lo necesitan, sin introducir un framework.

---

## 13. Views afectadas

[RECOMENDACIÓN] No se requiere auditar los 65 archivos de vista uno por uno para efectos de este plan: **ninguna vista ejecuta SQL directamente** (confirmado por la auditoría de Models — toda consulta vive en `Model/`), así que una vista queda "afectada" únicamente en la medida en que su Controller/Model ya identificado en las secciones 11-12 cambie la forma de los datos que le pasa. En concreto, las vistas de estos 25 folders necesitarán revisión visual (no de seguridad) cuando su Controller pase a exigir `$empresaId`:

`Views/Almacen`, `Views/AjusteInventario`, `Views/Auditoria`, `Views/Configuracion`, `Views/Contratista`, `Views/Dashboard`, `Views/Destino`, `Views/Entradas`, `Views/Informes` (InformePorUsuario, InformeDetallado, InformeDetalladoMaterial, InformePorUsuarioDetalle), `Views/Kardex`, `Views/Material`, `Views/Movimientos`, `Views/NotificacionDestinatario`, `Views/Proveedor`, `Views/Proyecto`, `Views/Rol`, `Views/Rubro`, `Views/Salidas`, `Views/Solicitud`, `Views/TipoUbicacion`, `Views/Ubicacion`, `Views/Usuario`.

Ninguna requiere cambio estructural propio — son consumidoras pasivas de lo que el Controller les entregue. La única vista con una decisión de diseño propia es `Views/Configuracion/show.php` (ver sección 17) si `configuracion` se fusiona con `empresa`.

---

## 14. Consultas SQL afectadas

Ya documentadas exhaustivamente, con archivo y línea, en las tablas de las secciones 11 (por Controller) y 12 (por Model) — no se duplican aquí. Como resumen cuantitativo: de los 31 Models, **26 tienen al menos un método que necesita cambio** (18 con columna propia + 5 que heredan pero cuyo `INSERT` necesita una validación cruzada + `Permiso`/`Rol` con su caso particular); solo `permiso` queda intacto como catálogo global, y las 3 legacy (`area`/`casa`/`manzana`) se recomiendan deprecar en vez de modificar.

---

## 15. Reportes afectados

[HECHO]

| Reporte/Endpoint | Archivo:línea de la consulta clave | ¿Filtra por algo anclable a empresa? | Riesgo | Notas |
|---|---|---|---|---|
| Dashboard — materiales críticos | `Model/Dashboard.php:14` | No | Alto | Ve el catálogo global completo. |
| Dashboard — movimientos recientes | `Model/Dashboard.php:27-41` | No | Alto | Mezcla entradas/salidas de todo el sistema. |
| Dashboard — proyectos activos | `Model/Dashboard.php:53` → `Proyecto::all()` | No | Alto | Lista de proyectos de todas las empresas. |
| Kardex por material | `Model/Kardex.php:16-40` | Parcial (solo `MaterialID`) | Alto | El material es global hoy; su historial mezclaría empresas. |
| Movimientos (entradas/salidas) | `Model/Movimientos.php:20-142`, base `1=1` | Parcial (`proyectoId` opcional) | Alto | Sin filtro, trae todo el sistema. |
| InformeDetallado / Salidas por Ubicación | `Model/InformeDetallado.php:18,69` | **Sí — único con `ProyectoID` obligatorio** | Bajo/Medio | Patrón correcto a replicar en el resto. |
| InformePorUsuario — resumen | `Model/InformePorUsuario.php:18-20` | No | Alto (agregado) | `COUNT` global por usuario sin alcance. |
| InformePorUsuario — detalle | `Model/InformePorUsuario.php:65-98` | No | Alto | Historial completo de cualquier usuario por ID. |
| InformeEntrada / InformeSalida — detalle documento | `Controllers/InformeEntradaController.php:26-36`, `InformeSalidaController.php:26-38` | No | **Crítico (IDOR)** | ID crudo por GET, sin validar pertenencia; alimenta 4 PDFs standalone que heredan el problema. |
| Auditoría | `Model/Auditoria.php:112,168-204` | No (ni siquiera existe columna de proyecto/empresa en la tabla) | **Crítico** | Requiere migración de esquema, no solo de consulta. |
| Solicitud — reporte | `Controllers/SolicitudController.php:33-45`, `Model/Solicitud.php:157-159` | Parcial (`proyectoId` opcional) | Alto | El propio comentario del código admite que sin filtro "ve todas las solicitudes". |
| Material — catálogo/Inventario Actual | `Model/Material.php:208-256` | No | **Crítico** | Catálogo 100% global, sin columna de alcance en la tabla. |
| Material — sin movimiento | `Model/Material.php:282-303` | No | Alto | Subconsultas sin alcance. |
| Usuario — search | `Controllers/UsuarioController.php:151-154` | No | **Crítico** | Confirma/niega existencia de una cédula en todo el sistema. |

**Agregados que filtran existencia/volumen entre empresas sin exponer detalle** (relevante para CA-ME-09): `InformePorUsuario::resumen()` (COUNT por usuario), `Solicitud::contarTotal()`/`contarPorEstado()`, `Auditoria::contarTotal()`, `Material::contarTotal()` — todos sin alcance de empresa hoy.

**AJAX/JS**: confirmado 0 endpoints reales (ver sección 2). **Inyección SQL**: no se encontró concatenación cruda de valores de usuario en ningún módulo revisado; todos usan `bindValue()`, y el único punto de ordenamiento dinámico (`Material::paginado()`) ya usa una whitelist explícita de columnas (`Model/Material.php:147-156`) — hallazgo positivo, sin acción requerida.

---

## 16. Auditoría

[HECHO] `auditoria` registra: `Entidad` (nombre de tabla, texto libre), `EntidadID` (polimórfico, sin FK real), `Accion`, `UsuarioID` (con FK real a `usuario`), `Fecha`, `DatosAntes`/`DatosDespues` (JSON). No registra IP ni ninguna otra columna.

**Opción 1 — `EmpresaID` directo en `auditoria`**: [RECOMENDACIÓN]. Ventaja: filtrar/paginar por empresa es un `WHERE` trivial, igual de rápido sin importar cuál de las ~15 entidades distintas se audite. Riesgo: hay que recordar pasarlo en cada una de las ~30 llamadas a `Auditoria::registrar()` repartidas por los Controllers.

**Opción 2 — Inferir la empresa a través de la entidad auditada**: requeriría un `JOIN` distinto según el valor de `Entidad` (a veces `usuario`, a veces `material`, a veces `rol`...) — inviable de expresar en una sola consulta de listado/paginado sin un `CASE` gigante, y se rompe por completo para acciones sobre entidades que ya fueron eliminadas (el `EntidadID` ya no resuelve a nada).

**Recomendación final**: Opción 1. Es además el mismo patrón usado ya para `UsuarioID` en la tabla (columna directa con FK real).

---

## 17. Configuraciones globales vs por empresa

[HECHO] Configuraciones existentes: `connection.php` (credenciales de conexión — una por *instalación*, no por empresa, ya que todas las empresas comparten la misma base de datos), `mail.config.php` (SMTP: host, puerto, usuario, contraseña, y el remitente `inventario@almacenberdez.com`), tabla `configuracion` (1 fila, ruta del logo), `notificacion_destinatario` (destinatarios de alertas).

| Configuración | Alcance recomendado | Justificación |
|---|---|---|
| `connection.php` (credenciales BD) | **GLOBAL** (por instalación) | Todas las empresas comparten la misma base de datos física — es justamente el punto de partida de este plan (CA-ME-07/08: nueva empresa ≠ nueva BD). |
| `mail.config.php` (servidor SMTP) | **GLOBAL**, con remitente configurable por empresa como dato, no como config de servidor | El servidor SMTP es de la plataforma; el "nombre para mostrar" del remitente y el asunto sí podrían personalizarse por empresa más adelante — no es parte del camino crítico. |
| Logo (`configuracion.LogoPath`) | **POR EMPRESA** | Cada constructora necesita su propia marca en la cabecera — [RECOMENDACIÓN] fusionar esta tabla dentro de `empresa.LogoPath` (sección 5) en vez de mantener una tabla de 1 fila aparte, ya que conceptualmente son el mismo dato. |
| `notificacion_destinatario` | **POR EMPRESA** | Cada empresa gestiona su propia lista de alertas de stock mínimo (ya identificado como CRÍTICO en la sección 12). |
| Catálogo `permiso` | **GLOBAL** | Ver sección 3/9 — es un catálogo de capacidades del producto. |
| `rol`/`rol_permiso` | **POR EMPRESA** | Ver sección 9. |

---

## 18. Seguridad y aislamiento

[RIESGO] Simulación conceptual de los ataques pedidos (ninguno fue ejecutado contra datos reales; se basa en el código leído):

- **A → consultar recurso de B**: `?controller=Material&action=updateshow&id=<idDeB>` — hoy `MaterialController::updateshow()` no valida dueño; con `EmpresaID` agregado a `material` pero **sin** el `AND EmpresaID=:empresaId` en `Material::searchById()`, seguiría funcionando igual. La mitigación no es "agregar la columna", es "usarla en cada consulta".
- **A → modificar recurso de B**: `POST ?controller=Usuario&action=update` con `id` de un usuario de B → **hoy mismo, sin ningún cambio de esquema**, `UsuarioController::update()` ya reconstruye y graba el usuario completo sin verificar nada más que el permiso de rol del atacante. Es el hallazgo más grave del documento.
- **A → eliminar recurso de B**: `?controller=Proyecto&action=delete&id=<idDeB>&csrf_token=...` — el CSRF token protegería contra un tercero externo, pero **no** contra un usuario autenticado de la Empresa A que simplemente cambia el `id` en la URL; el CSRF token de su propia sesión es válido para cualquier `id`.
- **A → generar reporte de B**: `?controller=Movimientos&action=show&proyectoId=<idDeB>` — `proyectoId` es un filtro opcional; sin `EmpresaID` obligatorio, es directamente alcanzable hoy en la lógica (una vez exista más de un proyecto/empresa).
- **A → consultar kardex de B**: `POST idMaterial=<idDeB>` a `KardexController::buscar()` — sin protección alguna hoy.
- **A → consultar stock de B**: cualquier vista de `Material` — el catálogo es global, no hay "stock de B" separado de "stock de A" en absoluto hoy.
- **A → consultar usuarios de B**: `UsuarioController::search()` con la cédula de un empleado de B — confirma/niega existencia globalmente.

[RECOMENDACIÓN] Estas simulaciones deben convertirse literalmente en el plan de pruebas automatizado de la sección 22, una vez existan 2 empresas de prueba.

---

## 19. Migración de datos actuales

[RECOMENDACIÓN] Los datos actuales de Berdez se convierten en la **Empresa ID=1** ("Constructora Berdez"):

1. Crear `empresa` e insertar la fila 1 (Berdez).
2. Agregar `EmpresaID` **nullable** a las 18 tablas identificadas (cambio aditivo, no rompe nada en producción).
3. `UPDATE <tabla> SET EmpresaID = 1` en cada una de las 18 (todas las filas existentes son de Berdez, por definición, ya que solo existe esa empresa).
4. Alterar `EmpresaID` a `NOT NULL` + agregar la `FOREIGN KEY`.
5. Migrar las unicidades globales que pasan a ser compuestas: `usuario.Identificacion` → `(EmpresaID, Identificacion)`, `notificacion_destinatario.Correo` → `(EmpresaID, Correo)`, `material.Codigo` → `(EmpresaID, Codigo)` (ver sección 20).

**Tablas que requieren migración**: las 18 de la sección 6. **Tablas con valor por defecto simple** (`EmpresaID=1` para todas las filas, sin excepciones ni casos especiales): todas ellas, precisamente porque hoy solo existe una empresa — no hay ambigüedad de a quién pertenece cada fila histórica.

**Datos huérfanos esperados**: ninguno, siempre que la migración se ejecute como un solo `UPDATE ... SET EmpresaID = 1` por tabla, sin condiciones — toda fila existente hoy es de Berdez por definición.

**Relaciones que podrían romperse**: ninguna FK existente se toca; solo se agregan columnas y restricciones nuevas. El único riesgo real es de **secuencia**: si se activa `NOT NULL`/FK antes de haber poblado `EmpresaID=1` en todas las filas, la migración falla (mitigación: ejecutar paso 3 antes que el paso 4, tabla por tabla, dentro de una transacción por tabla — mismo patrón de "TRUNCATE fuera de transacción, luego transacción para los INSERT" ya usado con éxito en las migraciones Producción→Desarrollo anteriores de este proyecto).

**Usuarios/proyectos/almacenes/materiales/movimientos históricos actuales**: todos pasan a `EmpresaID=1` sin excepción — es exactamente el estado actual, solo etiquetado.

---

## 20. Índices y restricciones

[RECOMENDACIÓN]

**Restricciones UNIQUE que pasan de globales a compuestas por empresa**:
```sql
-- usuario: hoy Identificacion no tiene UNIQUE explícito en el esquema, pero se usa como clave de login
UNIQUE (EmpresaID, Identificacion)

-- notificacion_destinatario: hoy Correo es UNIQUE global (confirmado en information_schema)
UNIQUE (EmpresaID, Correo)   -- reemplaza a la UNIQUE actual sobre Correo solo

-- material: Codigo no es UNIQUE hoy a nivel de esquema, pero Material::searchByCodigo() lo trata como clave de negocio
UNIQUE (EmpresaID, Codigo)

-- empresa
UNIQUE (NIT)
```

**Índices recomendados** (para que el `WHERE EmpresaID = ?` agregado no degrade el rendimiento de consultas que hoy ya son rápidas por ser tablas pequeñas, pero que crecerán con cada empresa nueva):

```sql
-- Consultas por empresa sola (listados de catálogo)
CREATE INDEX idx_material_empresa ON material(EmpresaID);
CREATE INDEX idx_usuario_empresa ON usuario(EmpresaID);
CREATE INDEX idx_proyecto_empresa ON proyecto(EmpresaID);
CREATE INDEX idx_contratista_empresa ON contratista(EmpresaID);
-- ... (uno por cada una de las 18 tablas)

-- Consultas compuestas empresa+proyecto (Ubicacion, Solicitud, RegistroSalidas)
CREATE INDEX idx_ubicacion_empresa_proyecto ON ubicacion(EmpresaID, ProyectoID);
CREATE INDEX idx_registro_salidas_empresa_proyecto ON registro_salidas(EmpresaID, ProyectoID);
CREATE INDEX idx_solicitud_empresa_proyecto ON solicitud(EmpresaID, ProyectoID);

-- Consultas compuestas empresa+almacen (cuando material_almacen crezca)
-- (no aplica directo a material_almacen porque hereda, pero sí a los JOINs que la usan)
```

---

## 21. Plan de implementación por fases

[RECOMENDACIÓN] Fases derivadas del riesgo real encontrado (no las fases hipotéticas del planteamiento inicial), ordenadas para cerrar primero lo más peligroso y dejar para el final la limpieza que no bloquea nada:

| Fase | Objetivo | Archivos principales |
|---|---|---|
| **0** | Esta auditoría (completa) | `PLAN-MULTIEMPRESA.md` |
| **1** | Tabla `empresa` + columna `EmpresaID` nullable en las 18 tablas (aditivo, sin romper nada) | Migración SQL nueva; ningún PHP existente cambia todavía |
| **2** | Migración de datos: `EmpresaID=1` en todo lo existente; `NOT NULL`+FK; nuevas UNIQUE compuestas | Migración SQL; `Model/Usuario.php` (unicidad de `Identificacion`) |
| **3** | Contexto de empresa: `usuario.EmpresaID`, `$_SESSION['empresaActivaId']`, helper `empresaActual()`, login resuelve por empresa | `LoginController.php`, `Html.php`, `Model/Usuario.php` |
| **4** | Cierre de los 2 hallazgos de toma de cuenta | `Controllers/UsuarioController.php`, `Controllers/RolController.php`, `Model/Usuario.php`, `Model/Rol.php`, `Model/Permiso.php` |
| **5** | Tabla ancla `Proyecto` + roles por empresa | `Controllers/ProyectoController.php`, `Model/Proyecto.php`, `Model/Rol.php`, migración de `rol`/`rol_permiso` |
| **6** | Catálogos base (patrón mecánico repetible) | `Almacen`, `Material`, `Contratista`, `Destino`, `Rubro`, `Proveedor`, `TipoUbicacion`, `NotificacionDestinatario` (Controller+Model de cada uno, 8 pares) |
| **7** | Movimientos e inventario | `Controllers/RegistroEntradasController.php`, `RegistroSalidasController.php`, `Controllers/UbicacionController.php`, sus Models |
| **8** | Solicitudes (workflow) | `Controllers/SolicitudController.php`, `Model/Solicitud.php` (incluye arreglar el bypass de permiso en `aprobar`/`rechazar`) |
| **9** | Reportes y Dashboard | `Model/Dashboard.php`, `Model/Kardex.php`, `Model/Movimientos.php`, `Model/InformePorUsuario.php`, `Model/InformeDetallado.php`, y los 4 Controllers `Informe*` + 6 scripts `*PDF.php` |
| **10** | Auditoría | `Model/Auditoria.php` (columna nueva + ~30 sitios que llaman `Auditoria::registrar()`) |
| **11** | Configuración y globales | `Model/Configuracion.php` → fusión con `empresa`, `Controllers/ConfiguracionController.php` |
| **12** | Limpieza de deuda técnica destapada por la auditoría | Eliminar `MaterialRegistroEntradasController.php`/`MaterialRegistroSalidasController.php` (vacíos); evaluar retiro de `area`/`casa`/`manzana` y sus FKs legacy en `material_registro_salidas` |
| **13** | Pruebas de aislamiento (sección 22) | N/A — solo pruebas, sin código nuevo de producto |
| **14** | Preparación SaaS (onboarding de empresa nueva sin nueva BD, ver CA-ME-07/08) | Pantalla de alta de empresa + rol/usuario administrador inicial |

---

## 22. Plan de pruebas

[RECOMENDACIÓN] Con 2 empresas de prueba (A y B, cada una con su propio material, proyecto, almacén, entrada, salida, solicitud):

**Por cada empresa, de forma independiente**: crear material, crear proyecto, crear almacén, registrar entrada, registrar salida, crear solicitud, consultar kardex, generar los 6 reportes (Dashboard, Movimientos, Kardex, InformeDetallado, InformePorUsuario, Solicitud/reporte).

**Cruce A→B, todos deben fallar (404/"no encontrado", nunca un error que confirme "existe pero no es tuyo")**:
- `?controller=Material&action=updateshow&id=<idDeB>`
- `?controller=Usuario&action=update` (POST `id=<idDeB>`)
- `?controller=Proyecto&action=delete&id=<idDeB>`
- `?controller=Movimientos&action=show&proyectoId=<idDeB>` (y sin ningún filtro — debe devolver vacío, no "todo")
- `?controller=Kardex&action=buscar` (POST `idMaterial=<idDeB>`)
- `?controller=InformeEntrada&action=detalle&id=<idDeB>` / `InformeSalida` (y sus PDFs derivados)
- `?controller=Usuario&action=search` (POST `identificacion=<cedulaDeUsuarioDeB>`) — debe responder igual que "no existe", no distinguir "existe pero es de otra empresa"
- `?controller=RolController&action=guardarPermisos` (POST `id=<rolIdDeB>`)
- `?controller=Solicitud&action=aprobar&id=<idDeB>`

**Manipulación adicional a probar**: IDs en URL y en POST, paginación (`?pagina=1` no debe filtrar por posición absoluta cruzando empresas), búsquedas/filtros de texto (¿el buscador de Material de A puede encontrar por coincidencia de texto un material de B?), exportaciones/PDFs (los 6 scripts standalone), y los conteos agregados de la sección 15 (¿el total mostrado en el Dashboard de A cambia si se crean/borran datos en B? — si cambia, hay fuga de conteo aunque no de detalle).

---

## 23. Riesgos

[RIESGO]

1. **Ausencia de clase base en Models** obliga a tocar 26 de 31 archivos uno por uno — alto riesgo de olvidar uno (mitigación: el helper `whereEmpresa()` de la sección 12 + una suite de pruebas automatizada de la sección 22 corrida contra *cada* Model antes de dar la fase por cerrada).
2. **`SolicitudController::aprobar()`/`rechazar()` ya bypasean el gate central de permisos hoy** — cualquier fase que toque `Solicitud` debe re-verificar que este bypass no se herede al nuevo esquema.
3. **Los 6 scripts `*PDF.php` standalone no pasan por `routing.php`** — cualquier chequeo que se agregue centralizado en el dispatch (por ejemplo, un futuro "middleware" de empresa) no los cubre automáticamente; hay que tocarlos uno por uno.
4. **`Identificacion`/`Correo`/`Codigo` pasan de únicos-globales a únicos-por-empresa** — si ya existieran datos duplicados entre "empresas" (no aplica hoy porque solo hay una), la migración de UNIQUE fallaría; mitigación: verificar con un `SELECT ... GROUP BY ... HAVING COUNT(*)>1` antes de la fase 2 (hoy el resultado es vacío, por construcción).
5. **Rendimiento**: agregar `EmpresaID` a tablas de alto volumen (`material_registro_salidas` ya tiene 766 filas y crecerá con cada empresa) sin los índices de la sección 20 degradaría los reportes existentes.

---

## 24. Estrategia de rollback

[RECOMENDACIÓN] Cada fase de la sección 21 debe ser reversible de forma independiente:

- **Fases 1-2 (esquema + migración de datos)**: aditivas y reversibles con un `ALTER TABLE ... DROP COLUMN EmpresaID` / `DROP TABLE empresa` mientras ninguna fase posterior dependa todavía de la columna siendo `NOT NULL` — por eso la fase 1 la deja `NULL`able antes de la fase 2 la vuelve obligatoria, dando una ventana de reversión limpia entre ambas.
- **Fases 3+ (código)**: seguir el mismo patrón ya usado con éxito en las migraciones anteriores de este proyecto (backup completo de `inventario` antes de cada fase vía `mysqldump`, commits pequeños por fase, nunca `--force`/`--no-verify`).
- **Punto de no retorno**: una vez la fase 2 vuelve `NOT NULL` las columnas y se activan las nuevas `UNIQUE` compuestas, revertir exige quitar esas restricciones antes de poder hacer `DROP COLUMN` — documentar este orden explícitamente en el script de rollback de esa fase específica.

---

## 25. Criterios de aceptación

Mapeo de los criterios CA-ME-01 a CA-ME-10 de la solicitud contra el diseño de este documento:

| Criterio | Cómo lo cumple este diseño |
|---|---|
| CA-ME-01 | `empresaActual()` (sección 10) + `EmpresaID` en las 18 tablas + validación en cada `searchById`/`update`/`delete`. |
| CA-ME-02 | Todo `ID` recibido se combina siempre con `$empresaId` de sesión en el `WHERE`, nunca se confía en el `ID` solo (patrón documentado en la sección 10, punto 4). |
| CA-ME-03 | Los 34 Controllers ya validan permiso por rol; se agrega la validación de empresa en el mismo punto (antes de llamar al Model), sin cambiar la estructura de gating existente. |
| CA-ME-04 | Los 26 Models identificados en la sección 12 dejan de tener ningún método que ignore `EmpresaID` cuando el recurso dependa de empresa. |
| CA-ME-05 | Fase 9 (sección 21) hace obligatorio el filtro de empresa en los 6 reportes de la sección 15, incluyendo los conteos agregados. |
| CA-ME-06 | Fase 2 (sección 19) migra el 100% del historial actual a `EmpresaID=1`, sin pérdida de datos. |
| CA-ME-07 | Todas las empresas comparten la misma base de datos `inventario` — agregar una empresa es una fila en `empresa`, no una instalación nueva. |
| CA-ME-08 | Ninguna tabla se duplica; se agrega una columna a las existentes. |
| CA-ME-09 | Fase 9 incluye específicamente los conteos/agregados (`InformePorUsuario::resumen()`, `Solicitud::contarTotal()`, `Auditoria::contarTotal()`, `Material::contarTotal()`) identificados en la sección 15, no solo el detalle fila-a-fila. |
| CA-ME-10 | El diseño reutiliza `ProyectoID` como sub-alcance dentro de empresa (sección 4), compatible con el sistema de proyectos y multi-almacén ya existente — sin proponer una jerarquía distinta a la que el código real ya usa. |

---

## 26. Archivos que se modificarían en cada fase

Ver la columna "Archivos principales" de la tabla en la sección 21 — no se repite aquí para no duplicar el documento. Como referencia cruzada: la lista exhaustiva de *todos* los archivos con hallazgos concretos (no solo los "principales" por fase) está en las tablas de las secciones 11 (34 Controllers) y 12 (31 Models).

---

## 27. Resumen final y recomendación

[RECOMENDACIÓN] Berdez puede evolucionar a SaaS multiempresa **sin cambiar su arquitectura PHP OOP actual y sin introducir un framework** — el patrón "columna `EmpresaID` + helper de contexto de sesión + un parámetro extra en cada Model" encaja con el estilo ya existente (PDO explícito, sin ORM, sin autoloader) y es mecánicamente aplicable a los 18 catálogos/transaccionales identificados.

Lo que este documento cambia respecto al planteamiento inicial de la solicitud, basado en evidencia real del código:

1. El tenant boundary **no** es `Empresa → Almacén → Proyecto` sino `Empresa` como raíz directa de casi todo, porque `Almacen` no tiene hoy ninguna relación con `Proyecto`.
2. La prioridad de remediación **no** es "empezar por los reportes" sino **cerrar primero `UsuarioController::update()` y `RolController::guardarPermisos()`** — son fugas de toma de cuenta que ya existen en el código de un solo tenant y se vuelven cruzadas en cuanto haya una segunda empresa.
3. Hay **código muerto** (`MaterialRegistroEntradasController.php`, `MaterialRegistroSalidasController.php`, 0 bytes) y **tablas legacy** (`area`, `casa`, `manzana`) que conviene retirar en vez de retrofitear, reduciendo el alcance real del trabajo.
4. **No existe ninguna superficie AJAX/API que asegurar hoy** — toda la complejidad está en la capa Controller→Model de páginas completas, lo cual simplifica el trabajo respecto a una aplicación con API propia.

Con las 14 fases de la sección 21, Berdez puede convertirse en una plataforma multiempresa real preservando el 100% de su funcionalidad e historial de datos actuales.

```text
========================================
AUDITORÍA MULTIEMPRESA FINALIZADA
========================================

Archivos modificados: 0
Tablas modificadas: 0
INSERT ejecutados: 0
UPDATE ejecutados: 0
DELETE ejecutados: 0
ALTER TABLE ejecutados: 0
Commits realizados: 0
Push realizados: 0

Documento generado:
PLAN-MULTIEMPRESA.md
```
