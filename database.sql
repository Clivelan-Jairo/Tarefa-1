-- Criação de tabela para notas
CREATE TABLE IF NOT EXISTS notes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  title VARCHAR(120) NOT NULL,
  content TEXT NOT NULL,
  image VARCHAR(255), -- Nova coluna para URLs de imagens
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Alteração para adicionar a coluna 'image' em tabelas existentes
ALTER TABLE notes ADD COLUMN IF NOT EXISTS image VARCHAR(255);
