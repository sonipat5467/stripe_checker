// schema.sql - placeholder content for demonstration.
-- Table to store card checking logs
CREATE TABLE IF NOT EXISTS logs (
  id INT AUTO_INCREMENT PRIMARY KEY,
  card VARCHAR(32),
  status VARCHAR(16),
  bank VARCHAR(64),
  country VARCHAR(64),
  ip VARCHAR(45),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table for trap (honeypot) hits
CREATE TABLE IF NOT EXISTS traps (
  id INT AUTO_INCREMENT PRIMARY KEY,
  ip VARCHAR(45),
  user_agent TEXT,
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Optional: Table to track SK key usage or queue
CREATE TABLE IF NOT EXISTS tokens (
  id INT AUTO_INCREMENT PRIMARY KEY,
  sk_key TEXT,
  used INT DEFAULT 0,
  last_used TIMESTAMP
);
// schema.sql - placeholder content for demonstration.
