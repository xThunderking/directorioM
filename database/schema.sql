CREATE DATABASE IF NOT EXISTS directorio_medico
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE directorio_medico;

SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS doctores;
DROP TABLE IF EXISTS especialidades;
SET FOREIGN_KEY_CHECKS = 1;

CREATE TABLE IF NOT EXISTS especialidades (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_especialidades_nombre (nombre)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS doctores (
    id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre_completo VARCHAR(150) NOT NULL,
    telefono VARCHAR(30) NOT NULL,
    id_especialidad INT UNSIGNED NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_nombre_completo (nombre_completo),
    INDEX idx_id_especialidad (id_especialidad),
    CONSTRAINT fk_doctores_especialidades
        FOREIGN KEY (id_especialidad)
        REFERENCES especialidades(id)
        ON UPDATE CASCADE
        ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TEMPORARY TABLE tmp_doctores_seed (
    nombre_completo VARCHAR(150) NOT NULL,
    telefono VARCHAR(30) NOT NULL,
    especialidad VARCHAR(100) NOT NULL
);

INSERT INTO tmp_doctores_seed (nombre_completo, telefono, especialidad) VALUES
('Cobo Morales Jose Francisco', '4423433958', 'Angiologia'),
('Blando Ramirez Juan Salvador', '4422693289', 'Angiologia'),
('Garcia Andreu Jorge', '4422196995', 'Angiologia y Cuidados Paliativos'),
('Castro Montes Eleodoro', '4422262544', 'Cardiologia'),
('Guerrero Manon Cesar', '4423599949', 'Cardiologia'),
('Jose Miguel Villalaz Morales', '4426105025', 'Cardiologia'),
('Pombo Bartelt Jose Ernesto', '4423536845', 'Cardiologia'),
('Gabriel Fernandez Yanez', '4424269039', 'Cardiologia Intervencionista'),
('Vera Urquiza Rafael', '4426051358', 'Cardiologia Intervencionista'),
('Perez Rios Oswaldo', '5525664279', 'Cirugia Cardio-Toracica'),
('Romero Osio Mario', '4422302675', 'Cirugia Cardio-Toracica'),
('Fernandez Vazquez Mellado Luis Alberto', '4423430698', 'Cirugia General'),
('Hernandez Palero Juan Carlos', '4422301533', 'Cirugia General'),
('Lara Zavala Rogelio', '4422501650', 'Cirugia General'),
('Lorena Lina Lopez', '4421923049', 'Cirugia General'),
('Robles Davila Israel', '4422701210', 'Cirugia General'),
('Vazquez Carpizo Jorge Alejandro', '4422264698', 'Cirugia General'),
('Velazquez Ojeda Martin', '4448484809', 'Cirugia General'),
('Yahuaca Mendoza Jorge E.', '1900999', 'Cirugia General'),
('Rodriguez Guerrero Luis', '4422812960', 'Cirugia Maxilofacial'),
('Adame Garduno Mario Humberto', '4421577423', 'Cirugia Pediatrica'),
('Cristian Guadalupe Godinez Borrego', '4421389891', 'Cirugia Pediatrica'),
('Velazco Villanueva Sergio', '4421284826', 'Cirugia Pediatrica'),
('Gallegos Bayesteros Hugo', '4421577423', 'Cirugia Plastica'),
('Matabuena Tamez Roberto', '4422262001', 'Cirugia Plastica'),
('Orozco Grados Jose de Jesus', '4422498252', 'Cirugia Plastica'),
('Aguilar Alvarez Jorge', '4423560901', 'Endoscopia'),
('Jose Aguilar Mendoza', '4421121625', 'Endoscopia'),
('Robles Davila Israel', '4422701210', 'Endoscopia'),
('Guasco Guasti Eduardo', '4421900249', 'Gastroenterologia'),
('Hernandez Palero Juan Carlos', '4422301533', 'Gastroenterologia'),
('Jose Aguilar Mendoza', '4421121625', 'Gastroenterologia'),
('Robles Davila Israel', '4422701210', 'Gastroenterologia'),
('Alcocer Mulguia Jaime', '4422497792', 'Gineco-Obstetricia'),
('Ana Izela Garcia Saldana', '4425760997 / 4481230587', 'Gineco-Obstetricia'),
('Andrade Ruano Claudia Yadira', '3314637715', 'Gineco-Obstetricia'),
('Gudino Molina Francisco Javier', '5554054474', 'Gineco-Obstetricia'),
('Sepulveda Mendoza Denise Lorena', '8117647823', 'Gineco-Obstetricia'),
('Villalobos Cid Marco Antonio', '442260647', 'Gineco-Obstetricia'),
('Loarca Pina Luis Martin', '4422196817', 'Hematologia'),
('Alvarez Baeza Carlos', '4423268309', 'Infectologia'),
('Copado Gutierrez Jose Luis', '8120712914', 'Infectologia'),
('Perez Aguinaga Maria Eugenia', '4421753515', 'Infectologia'),
('Banales Ham Manuel Benjamin', '4421903198', 'Medicina Interna'),
('Damian Salazar Erika', '4427479595', 'Medicina Interna'),
('Julio Cesar Casillas', '4423235579', 'Medicina Interna'),
('Padilla Avila Susana', '4422744558', 'Medicina Interna'),
('Pulido Sanchez Francisco Javier', '4431731513', 'Medicina Interna'),
('Baca Baca Roberto', '4421280114', 'Nefrologia'),
('Mayorga Madrigal Hector Jose', '4421704494', 'Nefrologia'),
('Sabath Silva Ernesto', '4423277191', 'Nefrologia'),
('Gonzalez Juarez Francisco', '4421607310', 'Neumologia'),
('Jassen Avenaneda Krizia Jeannette', '4142196500', 'Neumologia'),
('Silos Garcia Ramon', '4422262116', 'Neumologia'),
('Carrillo Pichardo Roberto Hector', '4422265223', 'Neurocirugia'),
('Guerrero Sanchez Enrique', '4422262122', 'Neurocirugia'),
('Lechuga Rodriguez Gerardo Salvador', '7767678402', 'Neurocirugia'),
('Malo Camacho Victor Hugo', '4422191194', 'Neurocirugia'),
('Mejia Valencia Mario Ivan', '4422273230', 'Neurocirugia'),
('Oscar Malo Macias', '4425596815', 'Neurocirugia'),
('Stefanoni Galeazzi Domingo', '4422264357', 'Neurocirugia'),
('Cruz Reyes Nephtali', '4441749281', 'Neurologia'),
('Fabiola Pena Guani', '4422812567', 'Oftalmologia'),
('Valera Gress Roberto', '4424468948', 'Oftalmologia'),
('Espejel Valdes Cesar', '5547318452', 'Oncologia'),
('Juan Guillermo Sanchez Curtidor', '4421923032', 'Oncologia'),
('Martinez Gasperin Jose', '2721115920', 'Oncologia'),
('Perez Gomez Gustavo Francisco', '4423301093', 'Oncologia'),
('Abraham Jesus Alvarado Perez', '8711866219', 'Ortopedia y Traumatologia'),
('Alcocer Manrique Jose Luis', '4427046564', 'Ortopedia y Traumatologia'),
('Arroyo Gonzalez Abraham', '4431985246', 'Ortopedia y Traumatologia'),
('Bravo Maytorena Cesar H.', '4423441044', 'Ortopedia y Traumatologia'),
('Carlos Gamiz Mejia', '4421175735', 'Ortopedia y Traumatologia'),
('Casto Olvera Damian', '4422190521', 'Ortopedia y Traumatologia'),
('Castro Villar Jose Miguel', '4421757993', 'Ortopedia y Traumatologia'),
('Corres Franco Ivan Alfredo', '4421272419', 'Ortopedia y Traumatologia'),
('Cristian Lopez', '4421419000', 'Ortopedia y Traumatologia'),
('Dobarganes', '4422810981', 'Ortopedia y Traumatologia'),
('Garcini Munguia Franco Alberto', '4426106680', 'Ortopedia y Traumatologia'),
('Humberto Martinez', '4421755898', 'Ortopedia y Traumatologia'),
('Larranaga Martinez Mauricio', '4421561020', 'Ortopedia y Traumatologia'),
('Lopez Villers Alejandro', '4421208986', 'Ortopedia y Traumatologia'),
('Luis Tomas Llano Rodriguez', '4425691898', 'Ortopedia y Traumatologia'),
('Macedo Gutierrez', '4423602237', 'Ortopedia y Traumatologia'),
('Villegas Macedo Luis Jesus', '7715678070', 'Ortopedia y Traumatologia'),
('Curi Garcia Anuar', '4423225694', 'Otorrinolaringologia'),
('Garcina Pablos Velez Carlos', '4423430550', 'Otorrinolaringologia'),
('Laguna Barcenas Sara del Carmen', '4612345928', 'Otorrinolaringologia'),
('Nunez Martinez Josue', '4423477871', 'Otorrinolaringologia'),
('Avila Dominguez Alejandra', '5548803095', 'Pediatria'),
('Copado Gutierrez Jose Luis', '8120712914', 'Pediatria'),
('Hinojosa Garza Gabriela', '8112120585', 'Pediatria'),
('Nicolas Paredes Melesio', '4421817260', 'Pediatria'),
('Reynoso Navarret Elia Yunuen', '4421540039', 'Pediatria'),
('Ricardo Ugalde', '4422263301', 'Pediatria'),
('Esquivel Herrera Miguel Luis', '4422499209', 'Proctologia'),
('Almeida Montes Luis Guillermo', '4422242487', 'Psiquiatria'),
('Basurto Mendez Rafael', '4421215374', 'Psiquiatria'),
('Garcia Rodriguez Diego', '4421717403', 'Reumatologia'),
('Moreno Lopez Pedraza Lars Armando', '4421232423', 'Traumatologia Pediatrica'),
('Osornio Ruiz Jose Luis', '4422474914', 'Traumatologia Pediatrica'),
('Pacheco Ferruzca Martin', '4425227062', 'Traumatologia Pediatrica'),
('Soto Garcia Hazael', '4421686824', 'Traumatologia Pediatrica'),
('Aboytes Martinez Agustin', '4422009907', 'Urologia'),
('Ayax Salazar Nando', '4421728534', 'Urologia'),
('Beltran Martinez Luis Alfredo', '4432206292', 'Urologia'),
('Garcia Navarro Leonardo', '4422009111', 'Urologia'),
('Jeffrey de Antunano', '5521332701', 'Urologia'),
('Merizalde Palomino Walter', '4422191422', 'Urologia');

INSERT INTO especialidades (nombre)
SELECT DISTINCT especialidad
FROM tmp_doctores_seed
ORDER BY especialidad ASC;

INSERT INTO doctores (nombre_completo, telefono, id_especialidad)
SELECT
    t.nombre_completo,
    t.telefono,
    e.id
FROM tmp_doctores_seed t
INNER JOIN especialidades e ON e.nombre = t.especialidad
ORDER BY e.nombre ASC, t.nombre_completo ASC;

DROP TEMPORARY TABLE IF EXISTS tmp_doctores_seed;
