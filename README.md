# Fast Document Management System (FDMS)

## 1. Project Overview

FDMS is a multi‑tenant, schema‑driven document management platform designed to let organizations define custom document “blueprints” (types), manage documents through configurable stages, and enforce fine‑grained, role‑based permissions. It supports versioning, activity logging, and geospatial data via PostGIS.

---

## 2. Core Concepts & Data Model

* **Blueprints & Versions**

  * **Blueprint**: Defines a document type, its sections, fields, and lifecycle stages.
  * **Blueprint Version**: Immutable JSONB snapshot of a blueprint’s schema for audit and rollback.

* **Documents & Versions**

  * **Document**: An instance of a blueprint, storing field values in columns or JSONB.
  * **Document Version**: Historical snapshot of a document’s data at each change.

* **Stages**

  * Customizable workflow stages (e.g. Draft → Review → Approved).

* **Permissions**

  * Multi‑level controls (blueprint, document, stage, section, field).
  * Roles grant combinations of actions (create/read/update/delete) scoped to “all” vs. “own” and stage or section contexts.

* **Activity Logs**

  * Append‑only audit trail capturing who did what, when, and on which data.

---

## 3. High‑Level Architecture

1. **API Layer** (Node.js / Express)

   * Authentication via Zitadel + LDAP.
   * REST/GraphQL endpoints for blueprints, documents, versions, and permissions.

2. **Database Layer** (PostgreSQL + PostGIS)

   * Dynamic schema: table‑per‑blueprint OR shared JSONB approach.
   * Versioning tables for snapshots.
   * Spatial support for geotagged documents.

3. **Frontend** (Vue 3 + Ionic)

   * Mobile‑first responsive UI for creating, editing, and reviewing documents.
   * Internationalization support for multiple languages.

4. **Deployment & Infra**

   * Dockerized services behind Nginx load balancer.
   * S3‑backed file storage for attachments.
   * CI/CD pipeline with automated migrations.

---

## 4. Key Technologies

* **Backend**: Node.js (or pure JS), Fastify
* **Database**: PostgreSQL, PostGIS, JSONB for flexible schemas
* **Frontend**: Vue 3 + Ionic, i18n
* **Storage**: AWS S3 (attachments, backups)
* **Infrastructure**: Docker, Nginx, CI/CD (GitHub Actions)

---


## 5. Documentation


### 5.1. Building documentation

**Requirements**

- Fonts: `DejaVu Sans`, `DejaVu Sans Mono`
- pandoc
- pandoc luatex engine
- pandoc xetex engine

**Install dependencies for Ubuntu (22.04)**

```bash
sudo apt install pandoc texlive-latex-base texlive-fonts-recommended \
    texlive-extra-utils texlive-latex-extra texlive-xetex
```

**Build documentation**

Source: `docs/`
Output: `pdf/docs/`

```bash
make
```
