CREATE DATABASE IF NOT EXISTS projectDB;
USE projectDB;

CREATE TABLE admin (
  id INT AUTO_INCREMENT PRIMARY KEY,
  username VARCHAR(50) UNIQUE NOT NULL,
  password VARCHAR(255) NOT NULL
);

CREATE TABLE departments (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL
);

CREATE TABLE doctors (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(100) UNIQUE NOT NULL,
  password VARCHAR(255) NOT NULL,
  department_id INT,
  FOREIGN KEY(department_id) REFERENCES departments(id) ON DELETE SET NULL
);

CREATE TABLE patients (
  id INT AUTO_INCREMENT PRIMARY KEY,
  name VARCHAR(100) NOT NULL,
  email VARCHAR(100) UNIQUE NOT NULL,
  age INT,
  gender ENUM('M','F','O'),
  address TEXT
);

CREATE TABLE appointments (
  id INT AUTO_INCREMENT PRIMARY KEY,
  doctor_id INT,
  patient_id INT,
  appt_date DATE,
  appt_time TIME,
  status ENUM('Scheduled','Completed','Cancelled') DEFAULT 'Scheduled',
  FOREIGN KEY(doctor_id) REFERENCES doctors(id) ON DELETE CASCADE,
  FOREIGN KEY(patient_id) REFERENCES patients(id) ON DELETE CASCADE
);
INSERT INTO admin (username,password) VALUES ('admin', SHA2('admin123',256));
INSERT INTO departments (name) VALUES ('Cardiology'),('Neurology'),('Pediatrics');