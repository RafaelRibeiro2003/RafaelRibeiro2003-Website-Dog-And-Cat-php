CREATE DATABASE Dogandcat;

USE Dogandcat;

/*----------------------------------------------------------------------------------*/
/*Criação de Tabelas*/
CREATE TABLE utilizadores (
	username VARCHAR(30) PRIMARY KEY,
	imagem VARCHAR(30) NOT NULL DEFAULT 'default.png',
	pass VARCHAR(100) NOT NULL DEFAULT '123',
	telemovel INT(9),
	cargo INT NOT NULL DEFAULT 0,
	competencia VARCHAR(200) NOT NULL DEFAULT ''
);

CREATE TABLE reservas(
	id INT AUTO_INCREMENT PRIMARY KEY,
	cliente VARCHAR(30) NOT NULL,
	funcionario VARCHAR(30) NOT NULL,
	marcacao INT NOT NULL,
	animal VARCHAR(10) NOT NULL,
	data_comb DATE NOT NULL,
	hora_comb TIME NOT NULL,
	hora_concl TIME NOT NULL	/*a hora_concl deve corresponder à soma de hora_comb com a duração da marcação*/
);


/*----------------------------------------------------------------------------------*/
/*Criação de Chaves fronteira*/
ALTER TABLE reservas ADD CONSTRAINT FK_reservas_cliente FOREIGN KEY (cliente) REFERENCES utilizadores (username);
ALTER TABLE reservas ADD CONSTRAINT FK_reservas_funcionario FOREIGN KEY (funcionario) REFERENCES utilizadores (username);
/*----------------------------------------------------------------------------------*/
/*Inserção de registos nas tabelas*/


INSERT INTO utilizadores VALUES
	('admin', 'admin.png', md5('admin'), '919034050', 4, '1.gato,2.cao'),
	('joao', 'joao.png', md5('joao'), '917654855', 3, '1.cao,1.gato,2.gato,2.cao'),
	('joana', 'joana.jfif', md5('joana'), '917824691', 3, '2.gato,1.gato,1.cao'),
	('maria', 'maria.jpg', md5('maria'), '912357424', 3, '1.gato,1.cao'),
	('cliente','cliente.jfif', md5('cliente'),'913034043',2, '');

INSERT INTO reservas VALUES
	(1, 'cliente', 'admin', 1, 'gato', '2024-10-15','18:00', '18:30'),
	(2, 'cliente', 'joao', 1, 'gato', '2024-11-17','15:00', '15:30'),
	(3, 'cliente', 'maria', 1, 'gato', '2024-10-21','14:30', '15:00'),
	(4, 'cliente', 'joana', 1, 'cao', '2024-10-22','13:00', '13:30'),
	(5, 'cliente', 'admin', 2, 'cao', '2023-04-03','13:30', '14:30'),	
	(6, 'cliente', 'joao', 2, 'cao', '2023-04-02','13:00', '14:00'),
	(7, 'cliente', 'admin', 1, 'gato', '2023-08-12','13:30', '14:00'),
	(8, 'cliente', 'maria', 1, 'cao', '2023-10-11','09:30', '10:00'),
	(9, 'cliente', 'joana', 2, 'gato', '2023-10-11','09:30', '10:30'),
	(10, 'cliente', 'joana', 1, 'gato', '2023-3-11','09:30', '10:00');
