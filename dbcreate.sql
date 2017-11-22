#database setup for scripts

CREATE DATABASE rdm;
CREATE USER 'rdm_admin'@'localhost' IDENTIFIED BY 'replacethis';
GRANT ALL PRIVILEGES ON sils_rdm.* TO 'rdm_admin'@'localhost' WITH GRANT OPTION;
CREATE USER 'rdm_reader'@'localhost' IDENTIFIED BY 'replacethisalso';
GRANT SELECT ON rdm.* TO 'rdm_admin'@'localhost';

CREATE TABLE `Groups` (
      Name VARCHAR(20) NOT NULL PRIMARY KEY,
      Email VARCHAR(32),
      ContactPerson VARCHAR(32),
      ContactPersonEmail VARCHAR(32) 
    );
INSERT INTO `Groups` VALUES ('Group1','contact@group1.org.com'),('group2','info@group2.com');

CREATE TABLE `RequestStatus` ( 
      Name VARCHAR(10) NOT NULL PRIMARY KEY
    );
INSERT INTO `RequestStatus` VALUES ('Denied'),('Pending'),('WaitData'),('Archived'),('Duplicated');


CREATE TABLE Tape ( 
      `Barcode` CHARACTER(6) NOT NULL PRIMARY KEY, 
      `Date` DATE, 
      `Group` VARCHAR(20),
      `Number` integer, 
      `Class` VARCHAR(20), 
      `TapeIsLabeled` BOOLEAN,
      `IsFull` BOOLEAN,
      FOREIGN KEY (`Group`) REFERENCES `Groups` (`Name`) ON UPDATE CASCADE ON DELETE RESTRICT
    );


CREATE TABLE Archive (
      `Name` VARCHAR(128) NOT NULL,
      `Barcode` CHARACTER(6) NOT NULL,
      `TapeLocation` INTEGER NOT NULL,
      `ExternalID`   VARCHAR(128),
      `ArchiveDate`  DATE NOT NULL,
      `RequestID`    int,
      PRIMARY KEY(Barcode,TapeLocation),
      FOREIGN KEY(`Barcode`) REFERENCES `Tape` (`Barcode`) ON UPDATE CASCADE ON DELETE RESTRICT,
      FOREIGN KEY(`RequestID`) REFERENCES `Request` (`ID`) ON UPDATE CASCADE ON DELETE RESTRICT
    );
      
      


CREATE TABLE Request (
       `ID`              int NOT NULL AUTO_INCREMENT,
       `Name`            VARCHAR(256),
       `Email`           VARCHAR(256),
       `Year`            INT,
       `Group`           VARCHAR(20),
       `ShortTitle`      VARCHAR(64),
       `FirstAuthor`     VARCHAR(64),
       `LastAuthor`      VARCHAR(64),
       `Institutions`    VARCHAR(128),
       `FundingAgencies` VARCHAR(128),
       `Confidentiality` VARCHAR(128),
       `Embargo`         VARCHAR(128),
       `PreservationPeriod` VARCHAR(128),
       `Remarks`         VARCHAR(8000),
       `Status`          VARCHAR(10),
       `CreationDate`    DATE,
       `MD5sum`          VARCHAR(32),
       PRIMARY KEY (ID),
       FOREIGN KEY (`Group`) REFERENCES `Groups` (`Name`) ON UPDATE CASCADE ON DELETE RESTRICT ,
       FOREIGN KEY (`Status`) REFERENCES `RequestStatus` (`Name`) ON UPDATE CASCADE ON DELETE RESTRICT 
    );

