-- ================================================================
--  FILE: create_db.sql
--  STEP: Run this FIRST in phpMyAdmin before opening any page
--  HOW:  phpMyAdmin -> SQL tab -> paste all this -> click Go
-- ================================================================

-- ---------------------------------------------------------------
-- DATABASE 1: PatientData  (used by hospital.php - Task 1)
-- ---------------------------------------------------------------
CREATE DATABASE IF NOT EXISTS PatientData;
USE PatientData;

DROP TABLE IF EXISTS patients;

CREATE TABLE patients (
    id         INT(11)      NOT NULL AUTO_INCREMENT,
    firstname  VARCHAR(100) NOT NULL,
    lastname   VARCHAR(100) NOT NULL,
    idnumber   VARCHAR(20)  NOT NULL,
    gender     VARCHAR(10)  NOT NULL,
    diagnosis  VARCHAR(200) NOT NULL,
    drug       VARCHAR(200) NOT NULL,
    date_added DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
);

-- 4 sample patients required by Task 1 instruction (f)
INSERT INTO patients (firstname, lastname, idnumber, gender, diagnosis, drug) VALUES
('James',  'Omondi',  '12345678', 'Male',   'Malaria',    'Coartem'),
('Grace',  'Wanjiru', '23456789', 'Female', 'Typhoid',    'Ciprofloxacin'),
('Peter',  'Kamau',   '34567890', 'Male',   'Pneumonia',  'Amoxicillin'),
('Fatuma', 'Hassan',  '45678901', 'Female', 'Diabetes',   'Metformin');

-- ---------------------------------------------------------------
-- DATABASE 2: malaria_research  (used by registration.php - Task 2)
-- ---------------------------------------------------------------
CREATE DATABASE IF NOT EXISTS malaria_research;
USE malaria_research;

DROP TABLE IF EXISTS registrations;

CREATE TABLE registrations (
    id            INT(11)      NOT NULL AUTO_INCREMENT,
    firstname     VARCHAR(100) NOT NULL,
    secondname    VARCHAR(100) NOT NULL,
    phone         VARCHAR(20)  NOT NULL,
    gender        VARCHAR(10)  NOT NULL,
    age           INT(3)       NOT NULL,
    password      VARCHAR(255) NOT NULL,
    registered_at DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
);
