-- =============================================================================
-- SCRIPT DE INICIALIZAÇÃO DO CLUSTER POSTGRESQL
-- Banco principal: main (base para replicação e aplicação)
-- Executado automaticamente na PRIMEIRA inicialização do container primário
-- =============================================================================

-- ---------------------------------------------------------------------------
-- EXTENSÕES no banco padrão (postgres)
-- ---------------------------------------------------------------------------
CREATE EXTENSION IF NOT EXISTS "uuid-ossp";

CREATE EXTENSION IF NOT EXISTS "pg_stat_statements";

-- ---------------------------------------------------------------------------
-- ROLE DE REPLICAÇÃO (usada pelo container réplica para se conectar)
-- ---------------------------------------------------------------------------
DO $$
BEGIN
  IF NOT EXISTS (SELECT FROM pg_roles WHERE rolname = 'replicator') THEN
    CREATE ROLE replicator WITH REPLICATION LOGIN PASSWORD 'Replicator@2025!';
  END IF;
END
$$;

-- ---------------------------------------------------------------------------
-- ROLE DA APLICAÇÃO
-- ---------------------------------------------------------------------------
DO $$
BEGIN
  IF NOT EXISTS (SELECT FROM pg_roles WHERE rolname = 'senac') THEN
    CREATE ROLE senac LOGIN PASSWORD 'senac';
  END IF;
END
$$;

-- ---------------------------------------------------------------------------
-- BANCO PRINCIPAL — será o banco base da aplicação e da replicação
-- ---------------------------------------------------------------------------
CREATE DATABASE main
  OWNER senac
  ENCODING 'UTF8'
  LC_COLLATE 'pt_BR.UTF-8'
  LC_CTYPE 'pt_BR.UTF-8'
  TEMPLATE template0;

COMMENT ON DATABASE main IS 'Banco de dados principal da aplicação — fonte da replicação';

-- ---------------------------------------------------------------------------
-- BANCOS AUXILIARES
-- ---------------------------------------------------------------------------
CREATE DATABASE development_db
  OWNER senac
  ENCODING 'UTF8'
  LC_COLLATE 'pt_BR.UTF-8'
  LC_CTYPE 'pt_BR.UTF-8'
  TEMPLATE template0;

CREATE DATABASE testing_db
  OWNER senac
  ENCODING 'UTF8'
  LC_COLLATE 'pt_BR.UTF-8'
  LC_CTYPE 'pt_BR.UTF-8'
  TEMPLATE template0;

CREATE DATABASE production_db
  OWNER senac
  ENCODING 'UTF8'
  LC_COLLATE 'pt_BR.UTF-8'
  LC_CTYPE 'pt_BR.UTF-8'
  TEMPLATE template0;

-- ---------------------------------------------------------------------------
-- EXTENSÕES NOS BANCOS DA APLICAÇÃO
-- ---------------------------------------------------------------------------
\connect main
CREATE EXTENSION IF NOT EXISTS "uuid-ossp";
CREATE EXTENSION IF NOT EXISTS "pg_stat_statements";

\connect development_db
CREATE EXTENSION IF NOT EXISTS "uuid-ossp";

\connect testing_db
CREATE EXTENSION IF NOT EXISTS "uuid-ossp";

\connect production_db
CREATE EXTENSION IF NOT EXISTS "uuid-ossp";