Here are several critical logic and architectural concerns I spotted across your FDMS documentation:

1. **Contradictory Technology Constraints** (DONE)

   * `TECHNICAL_NOTES.md` and the SRS both mandate **no TypeScript**—"pure JavaScript (NO TypeScript)" is a **MUST** —yet your **PLAN.md** Phase 1 and the "Technical Implementation Details" in Phase 4 explicitly call out **TypeScript** for both backend and frontend . You'll need to choose one or the other; mixing them risks toolchain conflicts, wasted effort configuring TypeScript, or violating your own constraints.

2. **Scalability of Table‑per‑Blueprint** (DONE)

   * You plan to support up to **10,000 blueprints each with 1,000 fields** . Creating 10K separate PostgreSQL tables, each with up to 1,000 columns, will bloat the system catalogs, dramatically slow DDL (even "green" ALTERs), and stress connection pooling. Consider partitioning or a hybrid approach (e.g. grouping similar blueprints into shared tables with JSONB) to avoid catastrophic metadata growth.

3. **Migration Risk & Data Preservation Strategy** (DONE)

   * Your "red migrations" strategy renames old columns and creates new ones—preserving historical data via versioning and backups . But you rely on document‑version snapshots stored in JSONB; if a "rename/recreate" fails partway, it could leave the table schema inconsistent. You'll need **transactional DDL** or an out‑of‑band migration tool to guarantee atomic schema changes. PostgreSQL doesn't fully roll back DDL inside a transaction in all cases, so this "rollback" mechanism may fail silently.

   References:
   - [Transactional DDL in PostgreSQL: A Competitive Analysis](https://wiki.postgresql.org/wiki/Transactional_DDL_in_PostgreSQL:_A_Competitive_Analysis)
   - [PostgreSQL Documentation - ALTER TABLE](https://www.postgresql.org/docs/current/sql-altertable.html)

4. **Overly Complex Permissions Matrix** (DONE)

   * You've defined up to **five levels** of permission (blueprint, document, stage, section, field) . While granular, implementing & testing the combinatorial explosion of "read.own", "stage.review", "section.inspection", etc., is a huge undertaking and a frequent source of security bugs. Early on, you may wish to collapse to **section‑level only** (as suggested in Draft.md) and defer field‑level rules until after MVP .

   Solution:
   - Remove field level permission.

5. **Plan vs. Estimation Mismatch** (MAYBE)

   * Your **ESTIMATION.md** projects **7–9 months** for a single developer to build MVP , but your **PLAN.md** Phase 1 alone spans ~4–5 months, and Phase 2 adds another ~3–4 months . Combined that's already **7–9 months**, yet your Plan still has Phases 3–4 beyond MVP. In practice, you've under‑estimated: just getting to MVP in your own plan likely takes closer to **9 months**, not counting setup & buffers.

6. **Schema Versioning Coupled to Data Versioning**

   * You store both **blueprint_versions** and **document_versions** as JSONB snapshots  . This duplicates data and increases storage, but more critically, you don't describe how you'll **prune** or **archive** old versions. Without garbage‑collection policies, high‑turnover tables could bloat indefinitely.

7. **UI Requirements vs. Implementation Stack**

   * The SRS calls for an **Ionic + Angular** mobile‑first interface , but the UI Design Draft is desktop‑oriented (sidebars, drawers, complex menus) and hard‑codes Vietnamese text samples . You'll need responsive designs and proper internationalization rather than fixed labels.

---

**Recommendations to Mitigate These Issues:**

* **Harmonize your toolchain**: decide on JS *or* TS and update all docs accordingly.
* **Re‑evaluate table‑per‑blueprint** at scale—benchmark metadata operations or explore JSONB‑hybrid.
* **Use a robust migration framework** (e.g. `sqitch`, `liquibase`, or Postgres logical replication) for safe, atomic DDL.
* **Simplify permissions** for MVP—focus on blueprint‑ and document‑level checks first.
* **Align your Plan and Estimation**: adjust timelines or scope to ensure MVP delivery fits your projected 7–9 months.
* **Define version retention policies** to cap storage growth.
* **Adopt a responsive UI toolkit** with theming and i18n to match your target audience.
