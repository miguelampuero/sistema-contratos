CREATE DATABASE sistema_contratos;

USE sistema_contratos;

CREATE TABLE contratos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre_empresa VARCHAR(255),
  ruc VARCHAR(20),
  telefono VARCHAR(20),
  fecha DATE,
  total DECIMAL(10,2),
  a_cuenta DECIMAL(10,2),
  saldo DECIMAL(10,2),
  estado ENUM('Proceso', 'Finalizado') DEFAULT 'Proceso'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;


CREATE TABLE items_contrato (
  id INT AUTO_INCREMENT PRIMARY KEY,
  contrato_id INT NOT NULL,
  cantidad INT NOT NULL,
  descripcion TEXT NOT NULL,
  importe_unitario DECIMAL(10,2) NOT NULL,
  importe_total DECIMAL(10,2) AS (cantidad * importe_unitario) STORED,
  FOREIGN KEY (contrato_id) REFERENCES contratos(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_spanish_ci;
