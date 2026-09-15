-- Migracion de la mejora "Logo configurable de la cabecera".
-- Segura de re-ejecutar (todas las sentencias son idempotentes).

CREATE TABLE IF NOT EXISTS configuracion (
  ID INT NOT NULL,
  LogoPath VARCHAR(255) DEFAULT NULL,
  PRIMARY KEY (ID)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO configuracion (ID, LogoPath)
SELECT 1, NULL FROM DUAL
WHERE NOT EXISTS (SELECT 1 FROM configuracion WHERE ID = 1);

INSERT INTO permiso (Codigo, Descripcion)
SELECT 'configuracion.gestionar', 'Administrar la configuracion general del sistema (logo)'
WHERE NOT EXISTS (SELECT 1 FROM permiso WHERE Codigo = 'configuracion.gestionar');

-- Se asigna solo al rol Administrador (ID=1).
INSERT INTO rol_permiso (RolID, PermisoID)
SELECT 1, p.ID FROM permiso p WHERE p.Codigo = 'configuracion.gestionar'
  AND NOT EXISTS (SELECT 1 FROM rol_permiso rp WHERE rp.RolID = 1 AND rp.PermisoID = p.ID);
