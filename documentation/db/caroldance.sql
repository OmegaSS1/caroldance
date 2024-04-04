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

INSERT INTO local (nome) VALUES ('BA - Bahia');

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
  nome VARCHAR(20) NOT NULL,
  sobrenome VARCHAR(20) NOT NULL,
  data_nascimento DATE NOT NULL,
  cpf VARCHAR(11) UNIQUE NOT NULL,
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
  atividade_aluno_id INT REFERENCES atividade_aluno(id),
  valor DECIMAL(10, 2) NOT NULL,
  mes SET('Janeiro', 'Fevereiro', 'Março', 'Abril', 'Maio', 'Junho', 'Julho', 'Agosto', 'Setembro', 'Outubro', 'Novembro', 'Dezembro'),
  dh_vencimento TIMESTAMP NOT NULL,
  dh_pagamento TIMESTAMP,
  status_pagamento SET('Pendente', 'Em processamento', 'Concluido', 'Falhou', 'Cancelado', 'Estornado'),
  observacoes VARCHAR(255),
  dh_criacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP(),
  dh_atualizacao TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP(),
  status BOOLEAN DEFAULT 1,
  FOREIGN KEY (aluno_id) REFERENCES aluno(id),
  FOREIGN KEY (atividade_aluno_id) REFERENCES atividade_aluno(id)
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS black_list (
  id INT AUTO_INCREMENT PRIMARY KEY,
  ip VARCHAR(20) NOT NULL,
  tentativa INT NOT NULL,
  bloqueio_permanente BOOLEAN DEFAULT 0,
  data_inclusao DATETIME NOT NULL,
  data_atualizacao DATETIME DEFAULT CURRENT_TIMESTAMP() ON UPDATE CURRENT_TIMESTAMP(),
  rota VARCHAR(255) NOT NULL,
  observacao VARCHAR(255) NULL
) ENGINE=InnoDB;