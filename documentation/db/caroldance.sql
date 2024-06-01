CREATE DATABASE caroldance DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;

DROP TABLE IF EXISTS token_csrf;
CREATE TABLE IF NOT EXISTS token_csrf (
  id INT AUTO_INCREMENT PRIMARY KEY,
  ip VARCHAR(50) NOT NULL,
  token VARCHAR(255) NOT NULL
) ENGINE=InnoDB;

DROP TABLE IF EXISTS perfil_usuario;
CREATE TABLE IF NOT EXISTS perfil_usuario (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(50) NOT NULL,
  usuario_dashboard BOOLEAN DEFAULT 0,
  usuario_aluno BOOLEAN DEFAULT 0,
  adm_dashboard BOOLEAN DEFAULT 0,
  adm_calendario BOOLEAN DEFAULT 0,
  adm_cadastro_aluno BOOLEAN DEFAULT 0,
  adm_cadastro_usuario BOOLEAN DEFAULT 0,
  adm_cadastro_atividade BOOLEAN DEFAULT 0,
  adm_relatorio_aluno BOOLEAN DEFAULT 0,
  adm_relatorio_usuario BOOLEAN DEFAULT 0,
  adm_relatorio_balancete BOOLEAN DEFAULT 0,
  adm_grafico_atividade_mensal BOOLEAN DEFAULT 0,
  adm_grafico_mensalidade_mes BOOLEAN DEFAULT 0,
  adm_grafico_atividade BOOLEAN DEFAULT 0,
  dh_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP(),
  dh_atualizacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP(),
  status BOOLEAN DEFAULT 1
) ENGINE=InnoDB;

INSERT INTO perfil_usuario (nome) VALUES ('Visitante');
INSERT INTO perfil_usuario (nome, usuario_dashboard, usuario_aluno) VALUES ('Usuario', 1, 1);
INSERT INTO perfil_usuario (nome, adm_dashboard, adm_calendario) VALUES ('Administrador', 1, 1);
INSERT INTO perfil_usuario (nome, adm_dashboard, adm_calendario, adm_cadastro_aluno, adm_cadastro_usuario, adm_cadastro_atividade, adm_relatorio_aluno, adm_relatorio_usuario, adm_relatorio_balancete, adm_grafico_atividade_mensal, adm_grafico_mensalidade_mes) VALUES ('Administrador Master', 1, 1, 1, 1, 1, 1, 1, 1, 1, 1);

DROP TABLE IF EXISTS local;
CREATE TABLE IF NOT EXISTS local (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(50) NOT NULL,
  dh_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP(),
  dh_atualizacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP(),
  status BOOLEAN DEFAULT 1
) ENGINE=InnoDB;

INSERT INTO local (nome) VALUES ('Studio Carol Dance'), ('Studio Mussurunga Dance'), ('Experimental');

DROP TABLE IF EXISTS usuario;
CREATE TABLE IF NOT EXISTS usuario (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(20) NOT NULL,
  sobrenome VARCHAR(20) NOT NULL,
  data_nascimento DATE NOT NULL,
  email VARCHAR(70) NOT NULL,
  cpf VARCHAR(11) UNIQUE NOT NULL,
  perfil_usuario_id INT REFERENCES perfil_usuario(id),
  telefone_whatsapp VARCHAR(20) NOT NULL,
  telefone_recado VARCHAR(20) NULL,
  senha VARCHAR(100) NOT NULL,
  token_redefinicao_senha VARCHAR(255) NULL,
  dh_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP(),
  dh_atualizacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP() ON UPDATE CURRENT_TIMESTAMP(),
  status BOOLEAN DEFAULT 1,
  FOREIGN KEY (perfil_usuario_id) REFERENCES perfil_usuario(id) ON DELETE SET NULL
) ENGINE=InnoDB;

DROP TABLE IF EXISTS atividade_aluno;
CREATE TABLE IF NOT EXISTS atividade_aluno (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(50) NOT NULL,
  valor DECIMAL(12, 2) NOT NULL CHECK (valor > 0.00),
  segunda BOOLEAN DEFAULT 0,
  terca BOOLEAN DEFAULT 0,
  quarta BOOLEAN DEFAULT 0,
  quinta BOOLEAN DEFAULT 0,
  sexta BOOLEAN DEFAULT 0,
  sabado BOOLEAN DEFAULT 0,
  domingo BOOLEAN DEFAULT 0,
  h_inicial TIME NOT NULL,
  h_final TIME NOT NULL,
  usuario_id INT REFERENCES usuario(id),
  local_id INT REFERENCES local(id),
  dh_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP(),
  dh_atualizacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP(),
  status BOOLEAN DEFAULT 1,
  FOREIGN KEY (usuario_id) REFERENCES usuario(id),
  FOREIGN KEY (local_id) REFERENCES local(id)
) ENGINE=InnoDB;

DROP TABLE IF EXISTS parentesco;
CREATE TABLE IF NOT EXISTS parentesco (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(50) NOT NULL,
  dh_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP(),
  dh_atualizacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP(),
  status BOOLEAN DEFAULT 1
) ENGINE=InnoDB;

INSERT INTO parentesco (nome) VALUES ('Pai'), ('Mãe'), ('Irmão'), ('Irmã'), ('Avô'), ('Avó'), ('Tio'), ('Tia'), ('Primo'), ('Prima'), ('Bisavô'), ('Bisavó');

DROP TABLE IF EXISTS aluno;
CREATE TABLE IF NOT EXISTS aluno (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(50) NOT NULL,
  sobrenome VARCHAR(100) NOT NULL,
  data_nascimento DATE,
  cpf VARCHAR(11) UNIQUE,
  atividade_aluno_id INT REFERENCES atividade_aluno(id),
  dh_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP(),
  dh_atualizacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP() ON UPDATE CURRENT_TIMESTAMP(),
  status BOOLEAN DEFAULT 1,
  FOREIGN KEY (atividade_aluno_id) REFERENCES atividade_aluno(id) ON DELETE SET NULL
) ENGINE=InnoDB;

DROP TABLE IF EXISTS responsavel;
CREATE TABLE IF NOT EXISTS responsavel (
  id INT AUTO_INCREMENT PRIMARY KEY,
  usuario_id INT REFERENCES usuario(id),
  aluno_id INT REFERENCES aluno(id),
  parentesco_id INT REFERENCES parentesco(id),
  dh_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP(),
  dh_atualizacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP(),
  status BOOLEAN DEFAULT 1,
  FOREIGN KEY (usuario_id) REFERENCES usuario(id) ON DELETE CASCADE,
  FOREIGN KEY (aluno_id) REFERENCES aluno(id) ON DELETE CASCADE,
  FOREIGN KEY (parentesco_id) REFERENCES parentesco(id) ON DELETE SET NULL
) ENGINE=InnoDB;

DROP TABLE IF EXISTS mensalidade;
CREATE TABLE IF NOT EXISTS mensalidade (
  id INT AUTO_INCREMENT PRIMARY KEY,
  aluno_id INT REFERENCES aluno(id),
  mes SET('1', '2', '3', '4', '5', '6', '7', '8', '9', '10', '11', '12'),
  dh_vencimento TIMESTAMP NOT NULL,
  dh_pagamento TIMESTAMP,
  status_pagamento SET('Pendente', 'Em processamento', 'Concluido', 'Falhou', 'Cancelado', 'Estornado'),
  observacoes VARCHAR(255),
  dh_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP(),
  dh_atualizacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP(),
  status BOOLEAN DEFAULT 1,
  FOREIGN KEY (aluno_id) REFERENCES aluno(id)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS black_list (
  id INT AUTO_INCREMENT PRIMARY KEY,
  ip VARCHAR(50) NOT NULL,
  tentativa INT NOT NULL,
  bloqueio_permanente BOOLEAN DEFAULT 0,
  dh_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP(),
  dh_atualizacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP() ON UPDATE CURRENT_TIMESTAMP(),
  rota VARCHAR(255) NOT NULL,
  observacao VARCHAR(255) NULL
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS cliente_ingresso (
  id INT AUTO_INCREMENT PRIMARY KEY,
  aluno_id INT REFERENCES aluno(id),
  nome VARCHAR(255) NOT NULL,
  cpf VARCHAR(11) NOT NULL,
  email VARCHAR(70) NOT NULL,
  ingresso_id INT REFERENCES ingressos(id),
  valor INT DEFAULT 0,
  tipo SET('Pago', 'Cortesia'),
  periodo VARCHAR(50) NOT NULL,
  status_pagamento SET('Pendente', 'Em processamento', 'Concluido', 'Falhou', 'Cancelado', 'Estornado') DEFAULT 'Pendente',
  estacionamento BOOLEAN DEFAULT 0,
  dh_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP(),
  dh_atualizacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP() ON UPDATE CURRENT_TIMESTAMP(),
  status BOOLEAN DEFAULT 1,
  FOREIGN KEY (aluno_id) REFERENCES aluno(id),
  FOREIGN KEY (ingresso_id) REFERENCES ingressos(id)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS ingressos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  letra CHAR(1) NOT NULL,
  assento CHAR(3) NOT NULL,
  dh_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP(),
  dh_atualizacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP() ON UPDATE CURRENT_TIMESTAMP()
) ENGINE=InnoDB;

INSERT INTO ingressos (letra, assento) VALUES
('A', 'A1'), ('A', 'A2'), ('A', 'A3'), ('A', 'A4'), ('A', 'A5'), ('A', 'A6'), ('A', 'A7'), ('A', 'A8'), ('A', 'A9'), ('A', 'A10'), 
('A', 'A11'), ('A', 'A12'), ('A', 'A13'), ('A', 'A14'), ('A', 'A15'), ('A', 'A16'), ('A', 'A17'), ('A', 'A18'), ('A', 'A19'), 
('A', 'A20'), ('A', 'A21'), ('A', 'A22'), 
('B', 'B1'), ('B', 'B2'), ('B', 'B3'), ('B', 'B4'), ('B', 'B5'), ('B', 'B6'), ('B', 'B7'), ('B', 'B8'), ('B', 'B9'), ('B', 'B10'), 
('B', 'B11'), ('B', 'B12'), ('B', 'B13'), ('B', 'B14'), ('B', 'B15'), ('B', 'B16'), ('B', 'B17'), ('B', 'B18'), ('B', 'B19'), 
('B', 'B20'), ('B', 'B21'), ('B', 'B22'), ('B', 'B23'), ('B', 'B24'), 
('C', 'C1'), ('C', 'C2'), ('C', 'C3'), ('C', 'C4'), ('C', 'C5'), ('C', 'C6'), ('C', 'C7'), ('C', 'C8'), ('C', 'C9'), ('C', 'C10'), 
('C', 'C11'), ('C', 'C12'), ('C', 'C13'), ('C', 'C14'), ('C', 'C15'), ('C', 'C16'), ('C', 'C17'), ('C', 'C18'), ('C', 'C19'), 
('C', 'C20'), ('C', 'C21'), ('C', 'C22'), 
('D', 'D1'), ('D', 'D2'), ('D', 'D3'), ('D', 'D4'), ('D', 'D5'), ('D', 'D6'), ('D', 'D7'), ('D', 'D8'), ('D', 'D9'), ('D', 'D10'), 
('D', 'D11'), ('D', 'D12'), ('D', 'D13'), ('D', 'D14'), ('D', 'D15'), ('D', 'D16'), ('D', 'D17'), ('D', 'D18'), ('D', 'D19'), 
('D', 'D20'), ('D', 'D21'), ('D', 'D22'), ('D', 'D23'), ('D', 'D24'), 
('E', 'E1'), ('E', 'E2'), ('E', 'E3'), ('E', 'E4'), ('E', 'E5'), ('E', 'E6'), ('E', 'E7'), ('E', 'E8'), ('E', 'E9'), ('E', 'E10'), 
('E', 'E11'), ('E', 'E12'), ('E', 'E13'), ('E', 'E14'), ('E', 'E15'), ('E', 'E16'), ('E', 'E17'), ('E', 'E18'), ('E', 'E19'), 
('E', 'E20'), ('E', 'E21'), ('E', 'E22'), 
('F', 'F1'), ('F', 'F2'), ('F', 'F3'), ('F', 'F4'), ('F', 'F5'), ('F', 'F6'), ('F', 'F7'), ('F', 'F8'), ('F', 'F9'), ('F', 'F10'), 
('F', 'F11'), ('F', 'F12'), ('F', 'F13'), ('F', 'F14'), ('F', 'F15'), ('F', 'F16'), ('F', 'F17'), ('F', 'F18'), ('F', 'F19'), 
('F', 'F20'), ('F', 'F21'), ('F', 'F22'), ('F', 'F23'), ('F', 'F24'), 
('G', 'G1'), ('G', 'G2'), ('G', 'G3'), ('G', 'G4'), ('G', 'G5'), ('G', 'G6'), ('G', 'G7'), ('G', 'G8'), ('G', 'G9'), ('G', 'G10'), 
('G', 'G11'), ('G', 'G12'), ('G', 'G13'), ('G', 'G14'), ('G', 'G15'), ('G', 'G16'), ('G', 'G17'), ('G', 'G18'), ('G', 'G19'), 
('G', 'G20'), ('G', 'G21'), ('G', 'G22'), 
('H', 'H1'), ('H', 'H2'), ('H', 'H3'), ('H', 'H4'), ('H', 'H5'), ('H', 'H6'), ('H', 'H7'), ('H', 'H8'), ('H', 'H9'), ('H', 'H10'), 
('H', 'H11'), ('H', 'H12'), ('H', 'H13'), ('H', 'H14'), ('H', 'H15'), ('H', 'H16'), ('H', 'H17'), ('H', 'H18'), ('H', 'H19'), 
('H', 'H20'), ('H', 'H21'), ('H', 'H22'), ('H', 'H23'), ('H', 'H24'), 
('I', 'I1'), ('I', 'I2'), ('I', 'I3'), ('I', 'I4'), ('I', 'I5'), ('I', 'I6'), ('I', 'I7'), ('I', 'I8'), ('I', 'I9'), ('I', 'I10'), 
('I', 'I11'), ('I', 'I12'), ('I', 'I13'), ('I', 'I14'), ('I', 'I15'), ('I', 'I16'), ('I', 'I17'), ('I', 'I18'), ('I', 'I19'), 
('I', 'I20'), ('I', 'I21'), ('I', 'I22'), 
('J', 'J1'), ('J', 'J2'), ('J', 'J3'), ('J', 'J4'), ('J', 'J5'), ('J', 'J6'), ('J', 'J7'), ('J', 'J8'), ('J', 'J9'), ('J', 'J10'), 
('J', 'J11'), ('J', 'J12'), ('J', 'J13'), ('J', 'J14'), ('J', 'J15'), ('J', 'J16'), ('J', 'J17'), ('J', 'J18'), ('J', 'J19'), 
('J', 'J20'), ('J', 'J21'), ('J', 'J22'), ('J', 'J23'), ('J', 'J24'), 
('L', 'L1'), ('L', 'L2'), ('L', 'L3'), ('L', 'L4'), ('L', 'L5'), ('L', 'L6'), ('L', 'L7'), ('L', 'L8'), ('L', 'L9'), ('L', 'L10'), 
('L', 'L11'), ('L', 'L12'), ('L', 'L13'), ('L', 'L14'), ('L', 'L15'), ('L', 'L16'), ('L', 'L17'), ('L', 'L18'), ('L', 'L19'), 
('L', 'L20'), ('L', 'L21'), ('L', 'L22'), 
('M', 'M1'), ('M', 'M2'), ('M', 'M3'), ('M', 'M4'), ('M', 'M5'), ('M', 'M6'), ('M', 'M7'), ('M', 'M8'), ('M', 'M9'), ('M', 'M10'), 
('M', 'M11'), ('M', 'M12'), ('M', 'M13'), ('M', 'M14'), ('M', 'M15'), ('M', 'M16'), ('M', 'M17'), ('M', 'M18'), ('M', 'M19'), 
('M', 'M20'), ('M', 'M21'), ('M', 'M22'), ('M', 'M23'), ('M', 'L24'),
('N', 'N1'), ('N', 'N2'), ('N', 'N3'), ('N', 'N4'), ('N', 'N5'), ('N', 'N6'), ('N', 'N7'), ('N', 'N8'), ('N', 'N9'), ('N', 'N10'), 
('N', 'N11'), ('N', 'N12'), ('N', 'N13'), ('N', 'N14'), ('N', 'N15'), ('N', 'N16'), ('N', 'N17'), ('N', 'N18'), ('N', 'N19'), 
('N', 'N20'), ('N', 'N21'), ('N', 'N22'), 
('O', 'O1'), ('O', 'O2'), ('O', 'O3'), ('O', 'O4'), ('O', 'O5'), ('O', 'O6'), ('O', 'O7'), ('O', 'O8'), ('O', 'O9'), ('O', 'O10'), 
('O', 'O11'), ('O', 'O12'), ('O', 'O13'), ('O', 'O14'), ('O', 'O15'), ('O', 'O16'), ('O', 'O17'), ('O', 'O18'), ('O', 'O19'), 
('O', 'O20'), ('O', 'O21'), ('O', 'O22'), ('O', 'O23'), ('O', 'O24'), 
('P', 'P1'), ('P', 'P2'), ('P', 'P3'), ('P', 'P4'), ('P', 'P5'), ('P', 'P6'), ('P', 'P7'), ('P', 'P8'), ('P', 'P9'), ('P', 'P10'), 
('P', 'P11'), ('P', 'P12'), ('P', 'P13'), ('P', 'P14'), ('P', 'P15'), ('P', 'P16'), ('P', 'P17'), ('P', 'P18'), ('P', 'P19'), 
('P', 'P20'), ('P', 'P21'), ('P', 'P22'), 
('Q', 'Q1'), ('Q', 'Q2'), ('Q', 'Q3'), ('Q', 'Q4'), ('Q', 'Q5'), ('Q', 'Q6'), ('Q', 'Q7'), ('Q', 'Q8'), ('Q', 'Q9'), ('Q', 'Q10'), 
('Q', 'Q11'), ('Q', 'Q12'), ('Q', 'Q13'), ('Q', 'Q14'), ('Q', 'Q15'), ('Q', 'Q16'), ('Q', 'Q17'), ('Q', 'Q18'), ('Q', 'Q19'), 
('Q', 'Q20'), ('Q', 'Q21'), ('Q', 'Q22'), ('Q', 'Q23'), ('Q', 'Q24'), 
('R', 'R1'), ('R', 'R2'), ('R', 'R3'), ('R', 'R4'), ('R', 'R5'), ('R', 'R6'), ('R', 'R7'), ('R', 'R8'), ('R', 'R9'), ('R', 'R10'), 
('R', 'R11'), ('R', 'R12'), ('R', 'R13'), ('R', 'R14'), ('R', 'R15'), ('R', 'R16'), ('R', 'R17'), ('R', 'R18'), ('R', 'R19'), 
('R', 'R20'), ('R', 'R21'), ('R', 'R22'), 
('S', 'S1'), ('S', 'S2'), ('S', 'S3'), ('S', 'S4'), ('S', 'S5'), ('S', 'S6'), ('S', 'S7'), ('S', 'S8'), ('S', 'S9'), ('S', 'S10'), 
('S', 'S11'), ('S', 'S12'), ('S', 'S13'), ('S', 'S14'), ('S', 'S15'), ('S', 'S16'), ('S', 'S17'), ('S', 'S18'), ('S', 'S19'), 
('S', 'S20'), ('S', 'S21'), ('S', 'S22'), ('S', 'S23'), ('S', 'S24'),
('T', 'T1'), ('T', 'T2'), ('T', 'T3'), ('T', 'T4'), ('T', 'T5'), ('T', 'T6'), ('T', 'T7'), ('T', 'T8'), ('T', 'T9'), ('T', 'T10'), 
('T', 'T11'), ('T', 'T12'), ('T', 'T13'), ('T', 'T14'), ('T', 'T15'), ('T', 'T16'), ('T', 'T17'), ('T', 'T18'), ('T', 'T19'), 
('T', 'T20'), ('T', 'T21'), ('T', 'T22'), 
('U', 'U1'), ('U', 'U2'), ('U', 'U3'), ('U', 'U4'), ('U', 'U5'), ('U', 'U6'), ('U', 'U7'), ('U', 'U8'), ('U', 'U9'), ('U', 'U10'), 
('U', 'U11'), ('U', 'U12'), ('U', 'U13'), ('U', 'U14'), ('U', 'U15'), ('U', 'U16'), ('U', 'U17'), ('U', 'U18'), ('U', 'U19'), 
('U', 'U20'), ('U', 'U21'), ('U', 'U22'), ('U', 'U23'), ('U', 'U24'),
('V', 'V1'), ('V', 'V2'), ('V', 'V3'), ('V', 'V4'), ('V', 'V5'), ('V', 'V6'), ('V', 'V7'), ('V', 'V8'), ('V', 'V9'), ('V', 'V10'), 
('V', 'V11'), ('V', 'V12'), ('V', 'V13'), ('V', 'V14'), ('V', 'V15'), ('V', 'V16'), ('V', 'V17'), ('V', 'V18'), ('V', 'V19'), 
('V', 'V20'), ('V', 'V21'), ('V', 'V22');


CREATE TABLE IF NOT EXISTS estacionamento_ingresso (
  id INT AUTO_INCREMENT PRIMARY KEY,
  aluno_id INT REFERENCES aluno(id),
  periodo VARCHAR(100) NOT NULL,
  nome VARCHAR(100) NOT NULL,
  cpf VARCHAR(11) NOT NULL,
  email VARCHAR(100) NOT NULL,
  valor INT DEFAULT 0,
  status_pagamento SET('Pendente', 'Em processamento', 'Concluido', 'Falhou', 'Cancelado', 'Estornado') DEFAULT 'Pendente',
  dh_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP(),
  dh_atualizacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP() ON UPDATE CURRENT_TIMESTAMP(),
  FOREIGN KEY (aluno_id) REFERENCES aluno(id)
) ENGINE=InnoDB;
