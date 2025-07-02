-- FDMS Core Metadata Schema
-- This script creates the foundational tables for managing blueprints, users, and versions.

-- Enable PostGIS for geospatial data types, as planned in the architecture.
CREATE EXTENSION IF NOT EXISTS postgis;

-- Users and Authentication
CREATE TABLE users (
    id SERIAL PRIMARY KEY,
    email VARCHAR(255) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

CREATE TABLE groups (
    id SERIAL PRIMARY KEY,
    code VARCHAR(255) UNIQUE NOT NULL,
    title VARCHAR(255) NOT NULL,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

CREATE TABLE group_users (
    group_id INT NOT NULL REFERENCES groups(id) ON DELETE CASCADE,
    user_id INT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    PRIMARY KEY (group_id, user_id)
);

-- Blueprint Definition Tables
CREATE TABLE blueprints (
    id SERIAL PRIMARY KEY,
    code VARCHAR(255) UNIQUE NOT NULL,
    title VARCHAR(255) NOT NULL,
    table_name VARCHAR(255) UNIQUE NOT NULL,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);

CREATE TABLE blueprint_stages (
    id SERIAL PRIMARY KEY,
    blueprint_id INT NOT NULL REFERENCES blueprints(id) ON DELETE CASCADE,
    code VARCHAR(255) NOT NULL,
    title VARCHAR(255) NOT NULL,
    sequence INT NOT NULL DEFAULT 0,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    UNIQUE (blueprint_id, code)
);

CREATE TABLE blueprint_sections (
    id SERIAL PRIMARY KEY,
    blueprint_id INT NOT NULL REFERENCES blueprints(id) ON DELETE CASCADE,
    code VARCHAR(255) NOT NULL,
    title VARCHAR(255) NOT NULL,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    UNIQUE (blueprint_id, code)
);

CREATE TABLE blueprint_fields (
    id SERIAL PRIMARY KEY,
    blueprint_id INT NOT NULL REFERENCES blueprints(id) ON DELETE CASCADE,
    section_id INT REFERENCES blueprint_sections(id) ON DELETE SET NULL,
    code VARCHAR(255) NOT NULL,
    title VARCHAR(255) NOT NULL,
    config JSONB NOT NULL DEFAULT '{}', -- Stores data type, UI hints, validation, etc.
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    UNIQUE (blueprint_id, code)
);

-- Versioning Tables
CREATE TABLE blueprint_versions (
    id SERIAL PRIMARY KEY,
    blueprint_id INT NOT NULL REFERENCES blueprints(id) ON DELETE CASCADE,
    data JSONB NOT NULL, -- A complete snapshot of the blueprint structure
    diff TEXT, -- A textual description of the changes
    created_by INT REFERENCES users(id),
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);


-- Create indexes for foreign keys and commonly queried columns
CREATE INDEX idx_blueprint_stages_blueprint_id ON blueprint_stages(blueprint_id);
CREATE INDEX idx_blueprint_sections_blueprint_id ON blueprint_sections(blueprint_id);
CREATE INDEX idx_blueprint_fields_blueprint_id ON blueprint_fields(blueprint_id);
CREATE INDEX idx_blueprint_versions_blueprint_id ON blueprint_versions(blueprint_id);


-- CREATE TABLE %s_document_versions (
--     id SERIAL PRIMARY KEY,
--     document_id INT NOT NULL, -- The ID of the record in its own table
--     data JSONB NOT NULL, -- A complete snapshot of the document's data
--     diff JSONB, 
--     created_by INT REFERENCES users(id),
--     created_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
-- );
-- CREATE INDEX idx_%S_document_versions_table_id ON %s_document_versions(document_table_name, document_id);
