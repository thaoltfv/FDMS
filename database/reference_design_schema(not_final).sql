-- FDMS Database Schema
-- This schema supports the Fast Document Management System's unique architecture
-- where each document blueprint gets its own dedicated PostgreSQL table

-- Enable required extensions
CREATE EXTENSION IF NOT EXISTS "uuid-ossp";
CREATE EXTENSION IF NOT EXISTS "postgis";
CREATE EXTENSION IF NOT EXISTS "btree_gin"; -- For better JSONB indexing

-- Create schema for reference tables
CREATE SCHEMA IF NOT EXISTS references;

-- =============================================
-- Core System Tables
-- =============================================

-- Users table
CREATE TABLE IF NOT EXISTS users (
    id SERIAL PRIMARY KEY,
    uuid UUID DEFAULT uuid_generate_v4() UNIQUE,
    email VARCHAR(255) UNIQUE NOT NULL,
    password_hash VARCHAR(255) NOT NULL,
    full_name VARCHAR(255),
    is_active BOOLEAN DEFAULT true,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    last_login_at TIMESTAMP WITH TIME ZONE,
    deleted_at TIMESTAMP WITH TIME ZONE
);

-- Roles table (system-level access rules)
CREATE TABLE IF NOT EXISTS roles (
    id SERIAL PRIMARY KEY,
    code VARCHAR(50) UNIQUE NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    is_system BOOLEAN DEFAULT false, -- System roles cannot be deleted
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
);

-- Insert default system roles
INSERT INTO roles (code, title, description, is_system) VALUES
    ('super_admin', 'Super Administrator', 'Full system access, blueprint creation/deletion', true),
    ('blueprint_admin', 'Blueprint Administrator', 'Blueprint design and management', true),
    ('data_manager', 'Data Manager', 'Document CRUD within assigned blueprints', true),
    ('viewer', 'Viewer', 'Read-only access to permitted documents', true),
    ('auditor', 'Auditor', 'Access to activity logs and version history', true)
ON CONFLICT (code) DO NOTHING;

-- User-Role assignments
CREATE TABLE IF NOT EXISTS user_roles (
    id SERIAL PRIMARY KEY,
    user_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    role_id INTEGER NOT NULL REFERENCES roles(id) ON DELETE CASCADE,
    assigned_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    assigned_by INTEGER REFERENCES users(id),
    UNIQUE(user_id, role_id)
);

-- Groups table (document-level access rules)
CREATE TABLE IF NOT EXISTS groups (
    id SERIAL PRIMARY KEY,
    code VARCHAR(100) UNIQUE NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    parent_group_id INTEGER REFERENCES groups(id) ON DELETE SET NULL,
    metadata JSONB DEFAULT '{}'::jsonb,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    created_by INTEGER REFERENCES users(id),
    updated_by INTEGER REFERENCES users(id),
    deleted_at TIMESTAMP WITH TIME ZONE
);

-- User-Group assignments
CREATE TABLE IF NOT EXISTS group_users (
    id SERIAL PRIMARY KEY,
    group_id INTEGER NOT NULL REFERENCES groups(id) ON DELETE CASCADE,
    user_id INTEGER NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    created_by INTEGER REFERENCES users(id),
    expires_at TIMESTAMP WITH TIME ZONE, -- For time-bound access
    UNIQUE(group_id, user_id)
);

-- =============================================
-- Blueprint Management Tables
-- =============================================

-- Blueprint master table
CREATE TABLE IF NOT EXISTS blueprints (
    id SERIAL PRIMARY KEY,
    code VARCHAR(100) UNIQUE NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    table_name VARCHAR(63) UNIQUE NOT NULL, -- PostgreSQL table name limit
    category VARCHAR(100),
    tags TEXT[], -- Array of tags for organization
    is_active BOOLEAN DEFAULT true,
    is_locked BOOLEAN DEFAULT false, -- Prevent modifications
    metadata JSONB DEFAULT '{}'::jsonb,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    created_by INTEGER REFERENCES users(id),
    updated_by INTEGER REFERENCES users(id),
    deleted_at TIMESTAMP WITH TIME ZONE
);

-- Blueprint stages
CREATE TABLE IF NOT EXISTS blueprint_stages (
    id SERIAL PRIMARY KEY,
    blueprint_id INTEGER NOT NULL REFERENCES blueprints(id) ON DELETE CASCADE,
    code VARCHAR(50) NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    sequence INTEGER NOT NULL,
    is_initial BOOLEAN DEFAULT false,
    is_final BOOLEAN DEFAULT false,
    metadata JSONB DEFAULT '{}'::jsonb,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    UNIQUE(blueprint_id, code),
    UNIQUE(blueprint_id, sequence)
);

-- Blueprint sections
CREATE TABLE IF NOT EXISTS blueprint_sections (
    id SERIAL PRIMARY KEY,
    blueprint_id INTEGER NOT NULL REFERENCES blueprints(id) ON DELETE CASCADE,
    code VARCHAR(100) NOT NULL,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    sequence INTEGER NOT NULL,
    is_collapsible BOOLEAN DEFAULT true,
    is_visible BOOLEAN DEFAULT true,
    metadata JSONB DEFAULT '{}'::jsonb,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    created_by INTEGER REFERENCES users(id),
    updated_by INTEGER REFERENCES users(id),
    UNIQUE(blueprint_id, code),
    UNIQUE(blueprint_id, sequence)
);

-- Blueprint fields
CREATE TABLE IF NOT EXISTS blueprint_fields (
    id SERIAL PRIMARY KEY,
    blueprint_id INTEGER NOT NULL REFERENCES blueprints(id) ON DELETE CASCADE,
    section_id INTEGER REFERENCES blueprint_sections(id) ON DELETE SET NULL,
    code VARCHAR(63) NOT NULL, -- Will be column name in real table
    title VARCHAR(255) NOT NULL,
    description TEXT,
    field_type VARCHAR(50) NOT NULL, -- short_text, long_text, integer, decimal, etc.
    sequence INTEGER NOT NULL,
    is_required BOOLEAN DEFAULT false,
    is_unique BOOLEAN DEFAULT false,
    is_indexed BOOLEAN DEFAULT false,
    is_searchable BOOLEAN DEFAULT false,
    config JSONB NOT NULL DEFAULT '{}'::jsonb, -- Field-specific configuration
    validation JSONB DEFAULT '{}'::jsonb, -- Validation rules
    metadata JSONB DEFAULT '{}'::jsonb,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    created_by INTEGER REFERENCES users(id),
    updated_by INTEGER REFERENCES users(id),
    UNIQUE(blueprint_id, code)
);

-- Blueprint permissions
CREATE TABLE IF NOT EXISTS blueprint_permissions (
    id SERIAL PRIMARY KEY,
    blueprint_id INTEGER NOT NULL REFERENCES blueprints(id) ON DELETE CASCADE,
    group_id INTEGER NOT NULL REFERENCES groups(id) ON DELETE CASCADE,
    permission_code VARCHAR(100) NOT NULL, -- read, create, update, delete, manage_stages, etc.
    permission_level VARCHAR(50) NOT NULL, -- blueprint, document, stage, section, field
    target_code VARCHAR(100), -- For stage/section/field specific permissions
    metadata JSONB DEFAULT '{}'::jsonb,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    created_by INTEGER REFERENCES users(id),
    UNIQUE(blueprint_id, group_id, permission_code, permission_level, target_code)
);

-- Blueprint versions (for tracking schema changes)
CREATE TABLE IF NOT EXISTS blueprint_versions (
    id SERIAL PRIMARY KEY,
    blueprint_id INTEGER NOT NULL REFERENCES blueprints(id) ON DELETE CASCADE,
    version_number INTEGER NOT NULL,
    version_name VARCHAR(255),
    schema_snapshot JSONB NOT NULL, -- Complete snapshot of blueprint structure
    change_summary TEXT,
    migration_sql TEXT, -- SQL used for migration
    migration_status VARCHAR(50), -- pending, applied, failed, rolled_back
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    created_by INTEGER REFERENCES users(id),
    applied_at TIMESTAMP WITH TIME ZONE,
    UNIQUE(blueprint_id, version_number)
);

-- =============================================
-- Document Versioning Tables
-- =============================================

-- Generic document versions table (stores versions for all document types)
CREATE TABLE IF NOT EXISTS document_versions (
    id SERIAL PRIMARY KEY,
    document_id INTEGER NOT NULL,
    document_table_name VARCHAR(63) NOT NULL,
    blueprint_id INTEGER NOT NULL REFERENCES blueprints(id) ON DELETE CASCADE,
    version_number INTEGER NOT NULL,
    data_snapshot JSONB NOT NULL, -- Complete snapshot of document data
    diff JSONB, -- Structured diff from previous version
    stage_id INTEGER REFERENCES blueprint_stages(id),
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    created_by INTEGER REFERENCES users(id),
    change_summary TEXT,
    metadata JSONB DEFAULT '{}'::jsonb
);

-- =============================================
-- Activity Logging Tables
-- =============================================

-- Activity logs (partitioned by month for performance)
CREATE TABLE IF NOT EXISTS activity_logs (
    id BIGSERIAL,
    user_id INTEGER REFERENCES users(id),
    action VARCHAR(100) NOT NULL, -- create, read, update, delete, login, logout, etc.
    entity_type VARCHAR(100) NOT NULL, -- blueprint, document, user, group, etc.
    entity_id INTEGER,
    entity_table VARCHAR(63),
    ip_address INET,
    user_agent TEXT,
    request_id UUID,
    session_id UUID,
    data JSONB DEFAULT '{}'::jsonb, -- Additional context data
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP
) PARTITION BY RANGE (created_at);

-- Create initial partitions for activity logs
CREATE TABLE IF NOT EXISTS activity_logs_default PARTITION OF activity_logs DEFAULT;

-- =============================================
-- File Storage Metadata
-- =============================================

CREATE TABLE IF NOT EXISTS file_metadata (
    id SERIAL PRIMARY KEY,
    uuid UUID DEFAULT uuid_generate_v4() UNIQUE,
    filename VARCHAR(255) NOT NULL,
    original_filename VARCHAR(255),
    mime_type VARCHAR(255),
    size_bytes BIGINT,
    storage_path TEXT NOT NULL,
    storage_provider VARCHAR(50) DEFAULT 'garage',
    checksum VARCHAR(255),
    blueprint_id INTEGER REFERENCES blueprints(id) ON DELETE SET NULL,
    document_id INTEGER,
    document_table_name VARCHAR(63),
    field_code VARCHAR(63),
    uploaded_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    uploaded_by INTEGER REFERENCES users(id),
    deleted_at TIMESTAMP WITH TIME ZONE,
    metadata JSONB DEFAULT '{}'::jsonb
);

-- =============================================
-- System Configuration
-- =============================================

CREATE TABLE IF NOT EXISTS system_config (
    id SERIAL PRIMARY KEY,
    key VARCHAR(255) UNIQUE NOT NULL,
    value JSONB NOT NULL,
    description TEXT,
    is_public BOOLEAN DEFAULT false,
    created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
    updated_by INTEGER REFERENCES users(id)
);

-- =============================================
-- Indexes for Performance
-- =============================================

-- User indexes
CREATE INDEX idx_users_email ON users(email) WHERE deleted_at IS NULL;
CREATE INDEX idx_users_uuid ON users(uuid);

-- Group indexes
CREATE INDEX idx_groups_code ON groups(code) WHERE deleted_at IS NULL;
CREATE INDEX idx_groups_parent ON groups(parent_group_id) WHERE deleted_at IS NULL;

-- Blueprint indexes
CREATE INDEX idx_blueprints_code ON blueprints(code) WHERE deleted_at IS NULL;
CREATE INDEX idx_blueprints_table_name ON blueprints(table_name) WHERE deleted_at IS NULL;
CREATE INDEX idx_blueprints_category ON blueprints(category) WHERE deleted_at IS NULL;
CREATE INDEX idx_blueprints_tags ON blueprints USING gin(tags) WHERE deleted_at IS NULL;

-- Field indexes
CREATE INDEX idx_blueprint_fields_blueprint_id ON blueprint_fields(blueprint_id);
CREATE INDEX idx_blueprint_fields_section_id ON blueprint_fields(section_id);
CREATE INDEX idx_blueprint_fields_code ON blueprint_fields(blueprint_id, code);
CREATE INDEX idx_blueprint_fields_type ON blueprint_fields(field_type);

-- Permission indexes
CREATE INDEX idx_blueprint_permissions_blueprint_id ON blueprint_permissions(blueprint_id);
CREATE INDEX idx_blueprint_permissions_group_id ON blueprint_permissions(group_id);
CREATE INDEX idx_blueprint_permissions_code ON blueprint_permissions(permission_code);

-- Activity log indexes
CREATE INDEX idx_activity_logs_user_id ON activity_logs(user_id);
CREATE INDEX idx_activity_logs_action ON activity_logs(action);
CREATE INDEX idx_activity_logs_entity ON activity_logs(entity_type, entity_id);
CREATE INDEX idx_activity_logs_created_at ON activity_logs(created_at);

-- Document version indexes
CREATE INDEX idx_document_versions_document ON document_versions(document_table_name, document_id);
CREATE INDEX idx_document_versions_blueprint ON document_versions(blueprint_id);

-- File metadata indexes
CREATE INDEX idx_file_metadata_uuid ON file_metadata(uuid);
CREATE INDEX idx_file_metadata_document ON file_metadata(document_table_name, document_id) WHERE deleted_at IS NULL;

-- =============================================
-- Triggers for updated_at timestamps
-- =============================================

CREATE OR REPLACE FUNCTION update_updated_at_column()
RETURNS TRIGGER AS $$
BEGIN
    NEW.updated_at = CURRENT_TIMESTAMP;
    RETURN NEW;
END;
$$ language 'plpgsql';

CREATE TRIGGER update_users_updated_at BEFORE UPDATE ON users
    FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();

CREATE TRIGGER update_roles_updated_at BEFORE UPDATE ON roles
    FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();

CREATE TRIGGER update_groups_updated_at BEFORE UPDATE ON groups
    FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();

CREATE TRIGGER update_blueprints_updated_at BEFORE UPDATE ON blueprints
    FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();

CREATE TRIGGER update_blueprint_sections_updated_at BEFORE UPDATE ON blueprint_sections
    FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();

CREATE TRIGGER update_blueprint_stages_updated_at BEFORE UPDATE ON blueprint_stages
    FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();

CREATE TRIGGER update_blueprint_fields_updated_at BEFORE UPDATE ON blueprint_fields
    FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();

CREATE TRIGGER update_system_config_updated_at BEFORE UPDATE ON system_config
    FOR EACH ROW EXECUTE FUNCTION update_updated_at_column();

-- =============================================
-- Example Dynamic Tables (for reference)
-- These would be created dynamically by the application
-- =============================================

-- Example: employee_registration_documents table
-- CREATE TABLE employee_registration_documents (
--     id SERIAL PRIMARY KEY,
--     document_uuid UUID DEFAULT uuid_generate_v4() UNIQUE,
--     blueprint_id INTEGER NOT NULL REFERENCES blueprints(id),
--     stage_id INTEGER REFERENCES blueprint_stages(id),
--     
--     -- Dynamic fields based on blueprint definition
--     full_name VARCHAR(255),
--     email VARCHAR(255),
--     phone_number VARCHAR(50),
--     date_of_birth DATE,
--     employee_id VARCHAR(20),
--     department VARCHAR(100),
--     position VARCHAR(100),
--     salary NUMERIC(10, 2),
--     start_date DATE,
--     emergency_contact JSONB,
--     work_experience JSONB,
--     
--     -- Standard metadata fields
--     created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
--     updated_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
--     created_by INTEGER REFERENCES users(id),
--     updated_by INTEGER REFERENCES users(id),
--     submitted_at TIMESTAMP WITH TIME ZONE,
--     approved_at TIMESTAMP WITH TIME ZONE,
--     approved_by INTEGER REFERENCES users(id),
--     deleted_at TIMESTAMP WITH TIME ZONE,
--     metadata JSONB DEFAULT '{}'::jsonb
-- );

-- Example: employee_registration_versions table
-- CREATE TABLE employee_registration_versions (
--     id SERIAL PRIMARY KEY,
--     employee_registration_document_id INTEGER NOT NULL,
--     version_number INTEGER NOT NULL,
--     data_snapshot JSONB NOT NULL,
--     diff JSONB,
--     created_at TIMESTAMP WITH TIME ZONE DEFAULT CURRENT_TIMESTAMP,
--     created_by INTEGER REFERENCES users(id),
--     UNIQUE(employee_registration_document_id, version_number)
-- ); 