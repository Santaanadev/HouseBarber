-- Criação das tabelas necessárias para agendamentos e serviços
-- Ajuste tipos e engine conforme seu servidor (MySQL/MariaDB)

CREATE TABLE IF NOT EXISTS servicos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(150) NOT NULL,
  duracao_min INT NOT NULL DEFAULT 30,
  preco DECIMAL(10,2) DEFAULT 0.00,
  descricao TEXT,
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS agendamentos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  usuario_id INT NOT NULL,
  servico_id INT NOT NULL,
  data_agendamento DATE NOT NULL,
  horario VARCHAR(20) NOT NULL,
  status ENUM('agendado','confirmado','cancelado','concluido') DEFAULT 'agendado',
  created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
  FOREIGN KEY (servico_id) REFERENCES servicos(id) ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
