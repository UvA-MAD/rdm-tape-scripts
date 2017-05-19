#database setup for scripts

CREATE DATABASE rdm;
CREATE USER 'rdm_admin'@'localhost' IDENTIFIED BY 'replacethis';
GRANT ALL PRIVILEGES ON sils_rdm.* TO 'rdm_admin'@'localhost' WITH GRANT OPTION;
CREATE USER 'rdm_reader'@'localhost' IDENTIFIED BY 'replacethisalso';
GRANT SELECT ON rdm.* TO 'rdm_admin'@'localhost';

CREATE TABLE `Groups` ( 
      Name VARCHAR(20) NOT NULL PRIMARY KEY
    );
INSERT INTO `Groups` VALUES ('Group1'),('Group2'), ('Group3');

CREATE TABLE Tape ( 
      `Barcode` CHARACTER(6) NOT NULL PRIMARY KEY, 
      `Date` DATE, 
      `Group` VARCHAR(20),
      `Number` integer, 
      `Class` VARCHAR(20), 
      `TapeIsLabeled` BOOLEAN,
      FOREIGN KEY (`Group`) REFERENCES `Groups` (`Name`) ON UPDATE CASCADE ON DELETE RESTRICT
    );


CREATE TABLE Archive (
      `Name` VARCHAR(128) NOT NULL,
      `Barcode` CHARACTER(6) NOT NULL,
      `TapeLocation` INTEGER NOT NULL,
      `ExternalID`   VARCHAR(128),
      `ArchiveDate`    DATE NOT NULL,
      `MD5sum`       VARCHAR(32),
      FOREIGN KEY(`Barcode`) REFERENCES `Tape` (`Barcode`) ON UPDATE CASCADE ON DELETE RESTRICT,
      PRIMARY KEY(Barcode,TapeLocation)
    );
      


