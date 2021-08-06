?heaas9bWtNozyDA

CREATE TABLE Material (
  ID          int(10) NOT NULL AUTO_INCREMENT, 
  Codigo      int(10), 
  Descripcion varchar(100), 
  Unidad      varchar(30), 
  Saldo       int(10) UNSIGNED DEFAULT 0, 
  PRIMARY KEY (ID));
CREATE TABLE Registro_Entradas (
  ID        int(10) NOT NULL AUTO_INCREMENT, 
  Fecha     date,
  Hora      time,
  UsuarioID int(10) NOT NULL, 
  PRIMARY KEY (ID));
CREATE TABLE Material_Registro_Entradas (
  ID                  int(10) NOT NULL AUTO_INCREMENT,
  MaterialID          int(10) NOT NULL, 
  Registro_EntradasID int(10) NOT NULL, 
  Cantidad            int(10), 
  PRIMARY KEY (ID));
CREATE TABLE Registro_Salidas (
  ID            int(10) NOT NULL AUTO_INCREMENT,
  Fecha         date, 
  Hora          time, 
  UsuarioID    int(10) NOT NULL,
  ContratistaID int(10) NOT NULL,
  ProyectoID int(10) NOT NULL,
  PRIMARY KEY (ID));
CREATE TABLE Material_Registro_Salidas (
  ID                 int(10) NOT NULL AUTO_INCREMENT,
  MaterialID         int(10) NOT NULL, 
  Registro_SalidasID int(10) NOT NULL, 
  Cantidad           int(10),
  CasaID        int(10) NOT NULL, 
  ManzanaID     int(10) NOT NULL, 
  DestinoID     int(10) NOT NULL,  
  AreaID        int(10) NOT NULL, 
  PRIMARY KEY (ID));
CREATE TABLE Contratista (
  ID          int(10) NOT NULL AUTO_INCREMENT, 
  Descripcion varchar(60), 
  PRIMARY KEY (ID));
CREATE TABLE Proyecto (
  ID          int(10) NOT NULL AUTO_INCREMENT, 
  Descripcion varchar(60), 
  PRIMARY KEY (ID));
CREATE TABLE Usuario (
  ID          int(10) NOT NULL AUTO_INCREMENT, 
  Identificacion     int(10) NOT NULL, 
  Nombre         varchar(30), 
  Apellido       varchar(30), 
  Clave     varchar(10), 
  RolID          int(10) NOT NULL, 
  PRIMARY KEY (ID));
CREATE TABLE Destino (
  ID          int(10) NOT NULL AUTO_INCREMENT, 
  Descripcion varchar(60), 
  PRIMARY KEY (ID));
CREATE TABLE Area (
  ID          int(10) NOT NULL AUTO_INCREMENT, 
  Descripcion varchar(60), 
  PRIMARY KEY (ID));
CREATE TABLE Manzana (
  ID          int(10) NOT NULL AUTO_INCREMENT, 
  Descripcion varchar(60), 
  PRIMARY KEY (ID));
CREATE TABLE Casa (
  ID          int(10) NOT NULL AUTO_INCREMENT, 
  Descripcion varchar(60), 
  PRIMARY KEY (ID));
CREATE TABLE Rol (
  ID          int(10) NOT NULL AUTO_INCREMENT, 
  Descripcion varchar(20), 
  PRIMARY KEY (ID));
ALTER TABLE Material_Registro_Entradas ADD CONSTRAINT FKMaterial_R356246 FOREIGN KEY (MaterialID) REFERENCES Material (ID);
ALTER TABLE Material_Registro_Entradas ADD CONSTRAINT FKMaterial_R660572 FOREIGN KEY (Registro_EntradasID) REFERENCES Registro_Entradas (ID);
ALTER TABLE Material_Registro_Salidas ADD CONSTRAINT FKMaterial_R456168 FOREIGN KEY (MaterialID) REFERENCES Material (ID);
ALTER TABLE Material_Registro_Salidas ADD CONSTRAINT FKMaterial_R116924 FOREIGN KEY (Registro_SalidasID) REFERENCES Registro_Salidas (ID);
ALTER TABLE Registro_Entradas ADD CONSTRAINT FKRegistro_E578564 FOREIGN KEY (UsuarioID) REFERENCES Usuario (ID);
ALTER TABLE Registro_Salidas ADD CONSTRAINT FKRegistro_S59635 FOREIGN KEY (UsuarioID) REFERENCES Usuario (ID);
ALTER TABLE Registro_Salidas ADD CONSTRAINT FKRegistro_S536051 FOREIGN KEY (ContratistaID) REFERENCES Contratista (ID);
ALTER TABLE Registro_Salidas ADD CONSTRAINT FKRegistro_S45085 FOREIGN KEY (ProyectoID) REFERENCES Proyecto (ID);
ALTER TABLE Material_Registro_Salidas ADD CONSTRAINT FKMaterial_R349492 FOREIGN KEY (DestinoID) REFERENCES Destino (ID);
ALTER TABLE Material_Registro_Salidas ADD CONSTRAINT FKMaterial_R249940 FOREIGN KEY (AreaID) REFERENCES Area (ID);
ALTER TABLE Material_Registro_Salidas ADD CONSTRAINT FKMaterial_R690450 FOREIGN KEY (ManzanaID) REFERENCES Manzana (ID);
ALTER TABLE Material_Registro_Salidas ADD CONSTRAINT FKMaterial_R269180 FOREIGN KEY (CasaID) REFERENCES Casa (ID);
ALTER TABLE Usuario ADD CONSTRAINT FKUsuario36461 FOREIGN KEY (RolID) REFERENCES Rol (ID);