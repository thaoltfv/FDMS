# FDMS - System Architecture Document

## 1. Overview

This document outlines the system architecture for the Fast Document Management System (FDMS). The primary goal of FDMS is to provide a highly performant and queryable system for managing structured documents.

The core architectural philosophy is to prioritize **raw query performance, data integrity, and direct database-level analytics**. This is achieved by dynamically creating a dedicated, schema-compliant PostgreSQL table for each Document Blueprint, rather than using a single-table JSONB approach.

This decision acknowledges that the FDMS application itself will function as a specialized **Database Administration Tool**, empowering administrators to define and manage data schemas directly.

## 2. Core Architectural Principles

- **Separate Table per Blueprint:** Each blueprint will correspond to a physical table in the database (e.g., a "Registration" blueprint maps to a `registration_documents` table). This provides maximum performance and allows for direct SQL queries and BI tool integration.
- **Dynamic Schema Management:** The application backend is responsible for generating and executing Data Definition Language (DDL) statements (`CREATE TABLE`, `ALTER TABLE`) to keep the physical database schema in sync with the blueprint definitions.
- **Backend-Driven Logic:** All business logic, permission checks, and workflow management are centralized in the backend application. The frontend is a presentation layer that consumes the backend API.
- **Stateless JWT Authentication:** Authentication is handled via a self-contained system using JSON Web Tokens (JWTs), managed entirely within the backend.
- **Versioning as a Safety Net:** Both blueprint schemas and document data will be versioned. This is critical to prevent data loss during potentially destructive schema migrations (e.g., changing a column's data type).

## 3. System Components

The system is composed of three main parts:

```
+--------------------------+          +---------------------------+          +----------------------+
|                          |          |                           |          |                      |
|   Frontend Application   |  <-----> |    Backend Application    |  <-----> |  PostgreSQL Database |
|         (Vue 3)          | (API)    |         (Fastify)         | (SQL)    |                      |
|                          |          |                           |          |                      |
+--------------------------+          +---------------------------+          +----------------------+
                                                 |
                                                 | (S3 API)
                                                 V
                                     +--------------------------+
                                     |     File Storage         |
                                     |   (Garage - Self-hosted) |
                                     +--------------------------+
```

## 4. Backend Architecture (Fastify)

The Fastify application is the heart of the system. It is modular by design to handle the system's complexity.

### Key Modules:

- **`AuthModule`**:
    - Handles user registration and login.
    - Issues and validates JWTs.
    - Provides `AuthGuard` to protect routes.

- **`BlueprintModule`**:
    - Manages the metadata for blueprints, sections, fields, and stages.
    - Contains the **`SchemaManagerService`**, the most critical component for this architecture.
        - **Responsibility**: Translates blueprint definitions into SQL DDL.
        - **Execution**: Executes `CREATE` and `ALTER` statements against the database.
        - **Migration Strategy**:
            1.  **Analyze Change**: Determines the type of schema modification required.
            2.  **Safe Alteration**: For non-destructive changes (e.g., `VARCHAR` lengthening), it generates a simple `ALTER TABLE`.
            3.  **Type Casting**: For compatible type changes (e.g., `INT` to `BIGINT`), it uses `ALTER TABLE ... USING (column::new_type)`.
            4.  **Incompatible "Drop/Recreate"**: For incompatible changes (e.g., `TEXT` to `INT`), it will **rename the old column** (`ALTER TABLE ... RENAME COLUMN ...`) and **create a new column** with the correct name and type, preserving the old data in the renamed column. This action relies on document versioning as the primary data recovery mechanism.

- **`DocumentModule`**:
    - Provides a generic API for all CRUD operations on documents.
    - Contains a **`DynamicDocumentService`**.
        - **Responsibility**: This service is a factory for data access. It does **not** use a static ORM (like TypeORM's standard entity decorators) for document tables.
        - **Implementation**: It will use a lower-level **query builder (e.g., Knex.js)** to dynamically construct and execute SQL queries (`INSERT`, `SELECT`, `UPDATE`) against the correct blueprint table based on the request.

- **`FileStorageModule`**:
    - Provides an abstraction layer for file operations (upload, download, delete).
    - Will be configured to work with the self-hosted **Garage** S3-compatible storage service.

## 5. Core Database Schema (Metadata Tables)

The following tables form the core metadata structure of the application.

- `users` (id, email, password_hash, ...)
- `roles` / `groups` (for permissions)
- `blueprints` (id, code, title, table_name)
- `blueprint_sections` (id, blueprint_id, code, title)
- `blueprint_stages` (id, blueprint_id, code, title, sequence)
- `blueprint_fields` (id, blueprint_id, section_id, code, title, config JSONB)
    - The `config` column will store metadata like data type, UI hints, validation rules, etc.
- `blueprint_versions` (id, blueprint_id, data JSONB, diff TEXT)
    - Stores a snapshot of the blueprint schema whenever it's changed.
- `document_versions` (id, document_id, document_table_name, data JSONB, diff TEXT)
    - Stores a snapshot of a document's data before it's updated.

## 6. Key Workflows

### Workflow 1: Admin Creates a New Blueprint

1.  Admin defines a blueprint with fields (e.g., `full_name: TEXT`, `age: INT`) in the UI.
2.  Frontend sends the blueprint definition to the Fastify API (`POST /api/blueprints`).
3.  The `SchemaManagerService` generates a `CREATE TABLE registration_documents (id SERIAL PRIMARY KEY, full_name TEXT, age INT, ...)` statement.
4.  The DDL is executed against the PostgreSQL database.
5.  The blueprint metadata is saved to the `blueprints` and `blueprint_fields` tables.

### Workflow 2: User Creates a Document

1.  User navigates to the "Registration" blueprint form.
2.  Frontend fetches the blueprint definition and dynamically renders the form.
3.  User submits the form data.
4.  Request hits the Fastify API (`POST /api/documents/registration_documents`).
5.  The `DynamicDocumentService` validates the data against the blueprint rules.
6.  It uses a query builder to construct an `INSERT INTO registration_documents (full_name, age) VALUES (...)` statement.
7.  The new document is created.

## 7. Technology Stack Summary

- **Backend Framework**: Fastify
- **Frontend Framework**: Vue.js 3 (with Composition API)
- **Database**: PostgreSQL (with PostGIS extension)
- **Authentication**: JWT (JSON Web Tokens)
- **File Storage**: Garage (Self-hosted, S3-compatible)
- **Data Access**: Knex.js (prioritizing raw SQL queries)
