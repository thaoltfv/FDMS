# Project Estimation: Fast Document Management System (FDMS)

This document provides a time estimation for a single, experienced full-stack developer to build the FDMS project. This estimate is based on the technical design outlined in `ARCHITECTURE.md` and the feature set in `DRAFT.md`.

The core architectural choice of creating a **separate, physical database table for each document blueprint** is powerful but introduces significant complexity compared to a simpler JSONB-based approach. The backend must function as a database administration tool, dynamically managing schemas. This has been factored into the timeline.

## Time Estimation for a Single Full-Time Developer

### **Phase 1: Foundational Backend & Core APIs (MVP)**

This phase focuses on building the complex, non-negotiable backend foundation required by the chosen architecture.

*   **Core Tasks:**
    *   **Project Setup:** Fastify, PostgreSQL, TypeORM/Prisma for metadata tables.
    *   **Core Modules:** `AuthModule` (Users, Roles, Groups, JWT), `BlueprintModule` for managing blueprint metadata.
    *   **Schema Manager Service (Critical Path):** Implement the service to translate blueprint definitions into `CREATE TABLE` and basic `ALTER TABLE` (e.g., adding columns) DDL statements. This is the heart of the system.
    *   **Dynamic Document Service (Critical Path):** Implement the service using a query builder (e.g., Knex.js) to handle dynamic `INSERT`, `SELECT`, and `UPDATE` operations on the blueprint-specific tables.
    *   **Basic Features:** Implement activity logging and initial `FileStorageModule` for S3 integration.
*   **Estimate: 3 - 4 months**
*   *This phase is heavily back-loaded with complex, high-risk development. Success here is critical for the entire project.*

### **Phase 2: Core Frontend & UI (MVP)**

This phase focuses on building the user interface to interact with the backend foundation.

*   **Core Tasks:**
    *   **Project Setup:** Vue 3, authentication views, and basic administrative UI for users/groups.
    *   **Blueprint Designer UI:** A user interface for creating and editing blueprints, their sections, fields, and stages.
    *   **Dynamic Form Renderer:** A generic component that can render a data entry form based on the blueprint definition fetched from the API. Must support simple field types (text, number, date, file).
    *   **Document Management UI:** List, create, and edit views for documents.
    *   **Workflow UI:** Basic UI to show and progress a document through its defined stages.
*   **Estimate: 2 - 3 months**
*   *The dynamic form renderer is the most complex component here. The focus is on making the core architectural features usable.*

---

### **Total Estimated Time for a Foundational MVP:**

**Total: 5 - 7 months**

This range represents the time to build a stable, functional application that validates the core architecture and includes the essential features for creating and managing documents.

---

### **Phase 3: Advanced Features & Polish ("Version 2")**

This phase builds upon the MVP to deliver the more complex and powerful features envisioned in the draft.

*   **Core Tasks:**
    *   **Advanced Schema Migrations:** Enhance the `SchemaManagerService` to handle complex migrations (e.g., changing a column's data type by renaming the old column and creating a new one).
    *   **Advanced Field Types:** Implement UI and backend logic for composite objects (arrays of objects), relational fields, and geospatial types (with map widgets).
    *   **Reference Data Management:** Build the UI for administrators to manage reference data tables.
    *   **Output Generation:** Integrate templating libraries (DOCX, XLSX, PDF) and build the UI to manage and generate documents from templates.
    *   **Fine-Grained Permissions:** Implement the full permissions model (stage, section, field-level access).
    *   **Document Versioning:** Implement the `document_versions` system and a UI to view diffs.
*   **Estimate: 5 - 8 additional months**
*   *Each task in this phase is a significant feature. The schema migration and output generation are particularly complex.*

### **Phase 4: Long-Term & Enterprise Features**

*   **Tasks:** Full workflow automation (triggers, webhooks), import/export tools, advanced analytics dashboards, and performance optimizations like database table partitioning (`partman`).
*   **Estimate: 6+ additional months**
*   *These are major features that turn the application into a platform and would likely be prioritized based on specific client needs.*

### Key Assumptions and Variables

*   **Developer Experience:** This estimate assumes a senior full-stack developer with strong experience in Fastify, Vue, PostgreSQL, and DevOps. A less experienced developer could take 1.5x to 2x longer.
*   **Focus:** This assumes 100% dedicated, full-time work on the project.
*   **Scope Discipline:** The timeline depends on strictly adhering to the phased feature rollout. Adding features from later phases into the MVP will significantly delay its completion.