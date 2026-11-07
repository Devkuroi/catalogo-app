-- =========================================================
--  PromoCatálogo · Base de datos
--  Importar en phpMyAdmin o:  mysql -u root < sql/database.sql
--  ⚠️ Recrea las tablas (borra los datos anteriores).
-- =========================================================
CREATE DATABASE IF NOT EXISTS catalogo_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE catalogo_db;

DROP TABLE IF EXISTS usuarios;
DROP TABLE IF EXISTS productos;

-- ---------- Usuarios ----------
CREATE TABLE usuarios (
    id               INT AUTO_INCREMENT PRIMARY KEY,
    numero_documento VARCHAR(20)  NOT NULL UNIQUE,
    nombre           VARCHAR(100) NOT NULL,
    apellidos        VARCHAR(100) NOT NULL,
    correo           VARCHAR(150) NOT NULL UNIQUE,
    ciudad           VARCHAR(100) NOT NULL,
    pais             VARCHAR(100) NOT NULL,
    password         VARCHAR(255) NOT NULL,
    rol              ENUM('admin', 'usuario') NOT NULL DEFAULT 'usuario',
    fecha_registro   TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------- Productos ----------
CREATE TABLE productos (
    id               INT AUTO_INCREMENT PRIMARY KEY,
    nombre           VARCHAR(150) NOT NULL,
    descripcion      TEXT,
    precio_original  DECIMAL(10, 2) NOT NULL,
    precio_promocion DECIMAL(10, 2) NOT NULL,
    imagen           VARCHAR(255),
    categoria        VARCHAR(100),
    fecha_inicio     DATE,
    fecha_fin        DATE,
    activo           TINYINT(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------- Cuentas de prueba ----------
--  Administrador:  admin@catalogo.com   /  Admin123*
--  Usuario:        usuario@catalogo.com /  Usuario123*
INSERT INTO usuarios (numero_documento, nombre, apellidos, correo, ciudad, pais, password, rol) VALUES
('1000000001', 'Ana',   'Administradora', 'admin@catalogo.com',   'Cali',   'Colombia', '$2y$10$M/dLyYdKB87WtVM37su41OInoS6DNUXQXxCGjBkqCXf04CFiTQwPe', 'admin'),
('RM12345678', 'Marco', 'Rossi',          'usuario@catalogo.com', 'Roma',   'Italia',   '$2y$10$WzMJlzJi78UdXgXQnBRPCevoFAbFkBaw.8OSodTXrNba/TDXnikKG', 'usuario');

-- ---------- Productos de ejemplo ----------
INSERT INTO productos (nombre, descripcion, precio_original, precio_promocion, imagen, categoria, fecha_inicio, fecha_fin) VALUES
('Laptop Gaming Pro',     'Laptop de alto rendimiento con 16GB RAM y 512GB SSD', 1200.00, 899.99, 'laptop.jpg',      'Electrónica', '2026-09-01', '2026-10-31'),
('Smartphone Ultra',      'Teléfono inteligente con cámara de 108MP',             899.00, 699.99, 'smartphone.jpg',  'Electrónica', '2026-09-15', '2026-12-15'),
('Auriculares Bluetooth', 'Auriculares inalámbricos con cancelación de ruido',    199.99, 129.99, 'auriculares.jpg', 'Accesorios',  '2026-09-01', '2026-11-30'),
('Smartwatch Fitness',    'Reloj inteligente con monitor de salud',               299.00, 199.99, 'smartwatch.jpg',  'Wearables',   '2026-09-20', '2027-01-20'),
('Cámara Digital 4K',     'Cámara profesional con grabación 4K',                  799.00, 599.99, 'camara.jpg',      'Fotografía',  '2026-09-01', '2026-12-31'),
('Tablet Pro 11"',        'Tablet con pantalla de 11 pulgadas y stylus',          649.00, 499.99, 'tablet.jpg',      'Electrónica', '2026-09-10', '2027-02-10');
