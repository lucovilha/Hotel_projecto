CREATE DATABASE IF NOT EXISTS hotel_db CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci; 
 USE hotel_db; 
 
 CREATE TABLE utilizadores ( 
     id INT AUTO_INCREMENT PRIMARY KEY, 
     nome VARCHAR(100) NOT NULL, 
     email VARCHAR(150) NOT NULL UNIQUE, 
     password_hash VARCHAR(255) NOT NULL, 
     role ENUM('cliente','rececionista','gestor') NOT NULL DEFAULT 'cliente', 
     ativo TINYINT(1) NOT NULL DEFAULT 1, 
     criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP 
 ); 
 
 CREATE TABLE tipos_quarto ( 
     id INT AUTO_INCREMENT PRIMARY KEY, 
     nome VARCHAR(80) NOT NULL, 
     capacidade_base INT NOT NULL, 
     capacidade_maxima INT NOT NULL, 
     preco_diaria DECIMAL(8,2) NOT NULL, 
     preco_hospede_extra DECIMAL(8,2) NOT NULL DEFAULT 0.00, 
     preco_pequeno_almoco DECIMAL(8,2) NOT NULL DEFAULT 0.00, 
     descricao TEXT, 
     ativo TINYINT(1) NOT NULL DEFAULT 1 
 ); 
 
 CREATE TABLE quartos ( 
     id INT AUTO_INCREMENT PRIMARY KEY, 
     numero VARCHAR(10) NOT NULL UNIQUE, 
     tipo_quarto_id INT NOT NULL, 
     estado ENUM('livre','ocupado') NOT NULL DEFAULT 'livre', 
     descricao TEXT, 
     FOREIGN KEY (tipo_quarto_id) REFERENCES tipos_quarto(id) 
 ); 
 
 CREATE TABLE hospedes ( 
     id INT AUTO_INCREMENT PRIMARY KEY, 
     utilizador_id INT NOT NULL, 
     nome_completo VARCHAR(100) NOT NULL, 
     doc_tipo ENUM('Cartão de Cidadão','Passaporte','Outro') NOT NULL, 
     doc_numero VARCHAR(50) NOT NULL, 
     nif VARCHAR(9) DEFAULT NULL, 
     telefone VARCHAR(20) DEFAULT NULL, 
     estado ENUM('ativo','inativo') NOT NULL DEFAULT 'ativo', 
     FOREIGN KEY (utilizador_id) REFERENCES utilizadores(id) 
 ); 

-- DADOS DE TESTE
INSERT INTO utilizadores (nome, email, password_hash, role) VALUES 
 ('Gestor Admin', 'gestor@hotel.pt', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'gestor'), 
 ('Ana Rececionista', 'rececionista@hotel.pt', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'rececionista'), 
 ('João Cliente', 'joao@email.pt', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'cliente'); 
 
 INSERT INTO tipos_quarto (nome, capacidade_base, capacidade_maxima, preco_diaria, preco_hospede_extra, preco_pequeno_almoco, descricao) VALUES 
 ('Single', 1, 1, 60.00, 0.00, 8.00, 'Quarto individual.'), 
 ('Duplo', 2, 2, 90.00, 0.00, 8.00, 'Quarto com duas camas.'), 
 ('Casal', 2, 2, 95.00, 0.00, 8.00, 'Quarto com cama de casal.'), 
 ('Familiar', 2, 4, 130.00, 20.00, 8.00, 'Quarto para famílias até 4 pessoas.'); 
 
 INSERT INTO quartos (numero, tipo_quarto_id) VALUES 
 ('101', 1), ('102', 1), 
 ('201', 2), ('202', 2), 
 ('301', 3), ('302', 3), 
 ('401', 4), ('402', 4); 
 
 INSERT INTO hospedes (utilizador_id, nome_completo, doc_tipo, doc_numero, nif, telefone) VALUES 
 (3, 'João Silva', 'Cartão de Cidadão', '12345678', '123456789', '912345678');
 
 CREATE TABLE reservas ( 
     id INT AUTO_INCREMENT PRIMARY KEY, 
     hospede_id INT NOT NULL, 
     tipo_quarto_id INT NOT NULL, 
     quarto_id INT DEFAULT NULL, 
     data_inicio DATE NOT NULL, 
     data_fim DATE NOT NULL, 
     num_hospedes INT NOT NULL DEFAULT 1, 
     pequeno_almoco TINYINT(1) NOT NULL DEFAULT 0, 
     nif_faturacao VARCHAR(9) DEFAULT NULL, 
     estado ENUM('pendente','ativa','cancelada','concluida') NOT NULL DEFAULT 'pendente', 
     checkin_feito TINYINT(1) NOT NULL DEFAULT 0, 
     checkout_feito TINYINT(1) NOT NULL DEFAULT 0, 
     total_estimado DECIMAL(10,2) NOT NULL DEFAULT 0.00, 
     criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, 
     FOREIGN KEY (hospede_id) REFERENCES hospedes(id), 
     FOREIGN KEY (tipo_quarto_id) REFERENCES tipos_quarto(id), 
     FOREIGN KEY (quarto_id) REFERENCES quartos(id) 
 ); 
 
 CREATE TABLE pagamentos ( 
     id INT AUTO_INCREMENT PRIMARY KEY, 
     reserva_id INT NOT NULL, 
     montante DECIMAL(10,2) NOT NULL, 
     tipo ENUM('parcial','total') NOT NULL, 
     metodo ENUM('numerario','cartao','transferencia') NOT NULL DEFAULT 'numerario', 
     data DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, 
     operador_id INT NOT NULL, 
     notas TEXT, 
     FOREIGN KEY (reserva_id) REFERENCES reservas(id), 
     FOREIGN KEY (operador_id) REFERENCES utilizadores(id) 
 ); 
 
 CREATE TABLE logs ( 
     id INT AUTO_INCREMENT PRIMARY KEY, 
     acao VARCHAR(80) NOT NULL, 
     descricao TEXT NOT NULL, 
     utilizador_id INT DEFAULT NULL, 
     referencia_id INT DEFAULT NULL, 
     referencia_tipo VARCHAR(50) DEFAULT NULL, 
     criado_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP, 
     FOREIGN KEY (utilizador_id) REFERENCES utilizadores(id) 
 );