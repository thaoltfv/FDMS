# Project Estimation: Fast Document Management System (FDMS)

**Note:** This document has been revised based on the detailed feature set and architectural requirements outlined in `SPECIFICATION_DRAFT.md`. The new estimates reflect the significant complexity of the core engine.

This document provides a time estimation for a single, experienced full-stack developer to build the FDMS project. This estimate is based on the technical design outlined in `ARCHITECTURE.md` and the feature set in `DRAFT.md`.

The core architectural choice of creating a **separate, physical database table for each document blueprint** is powerful but introduces significant complexity compared to a simpler JSONB-based approach. The backend must function as a database administration tool, dynamically managing schemas. This has been factored into the timeline.

## Time Estimation for a Single Full-Time Developer

### **Phase 1: Foundational Backend & Core APIs (MVP)**

This phase focuses on building the complex, non-negotiable backend foundation required by the chosen architecture.

*   **Core Tasks:**
    *   **Project Setup:** Fastify, PostgreSQL, Knex.js for dynamic queries, and a lightweight ORM (e.g., Prisma) for metadata tables.
    *   **Core Modules:** `AuthModule` (Users, Roles, Groups, JWT), `BlueprintModule` for managing blueprint metadata (fields, sections, stages).
    *   **Schema Manager Service (Critical Path):** Implement the service to translate blueprint definitions into `CREATE TABLE` and basic `ALTER TABLE` DDL statements. This must support:
        *   Adding/removing columns.
        *   Creating PostgreSQL `GENERATED ALWAYS AS` columns for computed fields.
        *   Creating `tsvector` columns for full-text search.
    *   **Dynamic Document Service (Critical Path):** Implement the service using a query builder (Knex.js) to handle dynamic `INSERT`, `SELECT`, and `UPDATE` operations on blueprint-specific tables. MVP filtering will be limited to simple key-value lookups.
    *   **Basic Features:** Implement `ActivityLogService` and a `FileStorageService` with an S3-compatible provider.
*   **Estimate: 4 - 5 months**
*   *This phase is heavily back-loaded with complex, high-risk development. Success here is critical for the entire project.*

### **Phase 2: Core Frontend & UI (MVP)**

This phase focuses on building the user interface to interact with the backend foundation.

*   **Core Tasks:**
    *   **Project Setup:** Vue 3, authentication views, and basic administrative UI for users/groups.
    *   **Blueprint Designer UI:** A user interface for creating and editing blueprints, including their sections, fields (with detailed configurations from the spec), and stages.
    *   **Dynamic Form Renderer (Critical Path):** A generic component that can render a data entry form based on the blueprint definition. Must support basic field types (text, number, date, file, basic JSONB objects) and their specified configurations.
    *   **Document Management UI:** List, create, and edit views for documents with basic filtering and sorting.
    *   **Workflow UI:** Basic UI to show and progress a document through its defined stages.
*   **Estimate: 3 - 4 months**
*   *The blueprint designer and dynamic form renderer are the most complex components here. The focus is on making the core architectural features usable and intuitive.*

### **Total Estimated Time for a Foundational MVP:**

**Total: 7 - 9 months**

This range represents the time to build a stable, functional application that validates the core architecture and includes the essential features for creating and managing documents as detailed in the specification.

### **Phase 3: Advanced Features & Polish ("Version 2")**

This phase builds upon the MVP to deliver the more complex and powerful features envisioned in the draft.

*   **Core Tasks:**
    *   **Advanced Schema Migrations:** Enhance the `SchemaManagerService` to handle complex, non-destructive migrations (e.g., changing a column's data type, preserving history).
    *   **Advanced Query Engine:** Implement the full dynamic filtering and aggregation query capabilities specified in the API design (`/query` and `/aggregate` endpoints).
    *   **Advanced Field Types:** Implement UI and backend logic for `array_of_objects`, relational/foreign-key fields, and geospatial types (with map widgets).
    *   **Output Generation:** Integrate templating libraries (e.g., `docxtemplater`, `jsPDF`) and build the UI to manage and generate documents from templates.
    *   **Fine-Grained Permissions:** Implement the full permissions model (stage, section, field-level access).
    *   **Document Versioning:** Implement the `document_versions` system and a UI to view diffs between versions.
*   **Estimate: 6 - 8 additional months**
*   *Each task in this phase is a significant feature. The query engine and schema migrations are particularly complex.*

### **Phase 4: Long-Term & Enterprise Features**

*   **Tasks:** Full workflow automation engine (triggers, webhooks), bulk import/export tools, advanced analytics dashboards, and performance optimizations like database table partitioning (e.g., using `pg_partman`).
*   **Estimate: 6+ additional months**
*   *These are major features that turn the application into a platform and would likely be prioritized based on specific client needs.*

### Key Assumptions and Variables

*   **Developer Experience:** This estimate assumes a senior full-stack developer with strong experience in Fastify, Vue, PostgreSQL, and DevOps. A less experienced developer could take 1.5x to 2x longer.
*   **Focus:** This assumes 100% dedicated, full-time work on the project.
*   **Scope Discipline:** The timeline depends on strictly adhering to the phased feature rollout. Adding features from later phases into the MVP will significantly delay its completion.