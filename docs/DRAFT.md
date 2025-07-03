FDMS: Fast Document Management System
========================

## Concepts

- User Roles: system level access rules

- Blueprints
- Blueprint Fields
- Blueprint Sections
- Blueprint Stages: Rejected, Draft, {customized stages} , Passed

- Users
- Activity logs
- Documents with prefix (.i.e. Registration-Documents)
- Document versions
- Groups: document level access rules
- ?? Document Type Permissions
- ?? Document Stage Permissions

**Field Types**

There could be **input** fields and **output** fields

- int
- real
- short text
- multi lines text
- datetime
- datetime range
- single file
- multiple files
- relation (connect from other table)
- Address field: multi level / tree selectable from ?reference table(s) -- or multi-step-select
- auto-complete (stored in a reference table, auto completing, insert if not exist the input value)
- object (recursive-able)
- array of objects

**Output fields**

- Formula (JS expression/template for computing value from other input fields)
- PDF template output field (provide a template in DOCX or MD)
- DOCX template output field
- XLSX template output field

**Output libraries references**

- https://github.com/open-xml-templating/docxtemplater
- https://github.com/guigrpa/docx-templates
- https://github.com/optilude/xlsx-template
- https://www.npmjs.com/package/exceljs
- https://www.npmjs.com/package/js-excel-template

**Composite Object type**

Editing in a modal may be

Selectable
- display_render_type: table, multi-level bullet points

Example:

```
onland_assets: [ {
    title: "On-land Assets",
    field_configs: {
        asset_type: { type: string, title: "Asset Type" },
        title: { type: string, title: "Title" },
        construction_cost: { type: int, title: "Construction cost" }
    },
    field_values: { asset_type: "building", title: "building 1", construction_cost: 26000 }
} ]
```

## MUST Rules

- No Typescript, plain javascript
- GUI implementation using Ionic over Vue3


## Random Ideas

- Use `partman` extension to partition data by month or year. ? what if schema changed, i.e. adding fields or change a field type.
- GEOMETRY point type should have a map viewer and map marker editor
- Allow administrators to create/manage references data tables. maybe put them inside of a postgres schema named `references`, for example: `references.provinces`,`references.districts`,`references.wards`, or any thing admins want, I think it should be look much like a DB Admin Application for `references` schema;
- Need a way to declare a multisteps field type, like the example of administration level
- If use references tables, what happen when changing/deleting a row referenced by an existing document ? What happen to the values store in the document? -- Provide a “soft reference” mechanism (e.g., store label + reference ID, fallback to label if ID is deleted).

### Suggestions

- Output fields with templating?	Generate templates from data JSONB or use blueprint_field.code as data keys in template context
- Field-level permissions might get tricky - recommend simplifying to section-level access for initial version unless necessary.
- Add `field_config -> validation` and `field_config -> required_if`, `visible_if`, etc., to enable conditional logic.
- Consider a `reference_registry` table to dynamically link and manage reference tables via metadata.
- Store diff as structured data, not just text. Example:
```json
{
  "full_name": ["John", "John Doe"],
  "location": [null, "POINT(105 21)"]
}
```

## Database design

### Base structure

roles:
- id
- code
- title

role_permissions (feature level access control)
- id

logged_activities (partman by month)
- id
- user_id
- table_name
- action
- created_at
- data JSONB

groups
- id
- code
- created_at
- updated_at
- created_by
- updated_by
- deleted_at

group_users
- group_id
- user_id
- created_at

blueprint
- id
- code VARCHAR UNIQUE
- title
- table_name
- created_at
- updated_at
- created_by
- updated_by

blueprint_stages:
- id SERIAL
- blueprint_id
- code
- title
- sequence
- created_at
- update_at

blueprint_sections:
- id SERIAL
- blueprint_id
- code VARCHAR(255)
- created_at
- created_by
- updated_at
- updated_by

blueprint_fields
- id
- blueprint_id
- code VARCHAR(255)  -- will be column name in real table
- title
- section_id
- config JSONB

blueprint_versions:
- id SERIAL
- blueprint_id
- version ?
- name
- data JSONB { sections: {}, stages: {}, fields: {}, permissions: {} }
- diff TEXT

blueprint_permissions:
- id
- blueprint_id
- code (level: blueprint, document, stage, section, field)
- group_id

**Example permission codes**
- read
- document.read.own
- document.read.any
- stage.draft
- stage.collect
- stage.compose
- stage.review
- stage.reject
- section.basic
- section.inspection
- section.calculation
- section.output
- section.review

**Brainstorming permissions matrix**

example list all blueprint a user allowed to see (blueprint > document):
select all blueprint where user_group has read_perm; (where bp=id and code=read)

example listing all documents of a blueprint:
select all document where belongs to blueprint and user_group has read_documents_perm (where bp=id and code = )

How admin use:
- create a blueprint
- set group A has read perm
- set group A has create perm (create documents)
- set group B has read perm

What are differences between create-doc and create-blueprint document?

A example scenario: 3 groups X, Y, Z: where as X are info collectors, Y are composers, Z are reviewers:
- X,Y,Z have no create-blueprint perm
- X,Y,Z has doc-create perm


### Example User-defined tables

registration_documents
- id
- blueprint_id
- created_at TIMESTAMP
- updated_at TIMESTAMP
- processed_at TIMESTAMP
- created_by INT (user)
- created_by INT (user)
- metadata JSONB
- customized fields (just field, indent for clear view)
    - full_name TEXT
    - email VARCHAR(1024)
    - date_of_birth DATETIME
    - location GEOMETRY(Point, 4326)
    - etc.

registration_versions
- id
- registration_document_id
- created_at
- created_by
- data JSONB
- diff TEXT


## UI Design

### Third-party

- Must not use Google as backend map provider (use OpenStreetMap)
- UI: Consider OpenLayers or Leaflet integration.

### Hierarchy

- Home (feeds)
- Blueprints
    - Blueprint -> Documents
        - DocumentDetail
- Administration
    - Users
    - Roles
    - Groups
- Profile

### Menus

### Screens

#### Home feed

#### DocumentDetail

- Doc header: title, stage, button for activity history
- Sections list: left sidebar for > tablet, horizontal segments for phone. Only list sections with granted permission.
- Main area: active section content: foreach fields render field base on their type.
- Bottom pane: Save button and Submit button (for next step or for review?)

## Implementation Priorities

### MVP Scope

1. Roles, Groups, Permissions (Blueprint, Document)
    * Blueprint Designer (fields, sections, stages)
    * Document CRUD
    * Stage progression and activity logs
    * Simple field types: text, int, datetime, files

2. Next Phase
    * Output field rendering (PDF/DOCX/XLSX)
    * Composite fields and arrays
    * Field-level conditionals
    * Geospatial and map widgets
    * Reference tables manager
 
3. Post-MVP
    * Workflow automation (rules/triggers per stage)
    * Webhooks, External API
    * Import/export
    * Audit dashboards and version diff UI

## First Client Specified App

Real Estate Inspection Data Collection App




## Tips and Tricks

### Query Table metadata (DESCRIBE)

SELECT * FROM information_schema.columns
WHERE table_name = 'registration-documents';

### Simple PostGIS

PostGIS Geolocation

To store geolocation data in PostgreSQL using PostGIS, you can utilize the spatial data types provided by PostGIS, such as `GEOMETRY` and `GEOGRAPHY`. These types allow you to store and query geographic information efficiently. Here's a step-by-step guide:

1. **Set Up PostGIS**: First, you need to enable the PostGIS extension in your PostgreSQL database. This can be done by connecting to your database and running the following commands:
   ```
   CREATE DATABASE spatial_db;
   \c spatial_db;
   CREATE EXTENSION postgis;
   ```

2. **Create a Spatial Table**: Once PostGIS is enabled, you can create a table to store geolocation data. For example, to store city locations, you might create a table like this:
   ```sql
   CREATE TABLE cities (
       id SERIAL PRIMARY KEY,
       name VARCHAR(100),
       location GEOMETRY(Point, 4326)
   );
   ```
   Here, `GEOMETRY(Point, 4326)` specifies that the `location` column will store point geometries using the WGS 84 coordinate system (SRID 4326).

3. **Insert Spatial Data**: You can insert geolocation data into the table using the `ST_GeomFromText` function. For example:
   ```sql
   INSERT INTO cities (name, location)
   VALUES
       ('New York', ST_GeomFromText('POINT(-74.0060 40.7128)', 4326)),
       ('Los Angeles', ST_GeomFromText('POINT(-118.2437 34.0522)', 4326)),
       ('Chicago', ST_GeomFromText('POINT(-87.6298 41.8781)', 4326));
   ```

4. **Querying Geolocation Data**: PostGIS provides various functions and operators to query geolocation data. For example, to find all cities within a certain distance from a given point, you can use the `ST_Distance` function. Additionally, you can use the `&lt;-&gt;` operator to sort results by distance:
   ```sql
   SELECT name, ST_Distance(location, ST_GeomFromText('POINT(-74.0060 40.7128)', 4326)) AS distance
   FROM cities
   ORDER BY distance;
   ```

5. **Spatial Indexes**: To improve query performance, you can create a spatial index on the `location` column. This allows PostgreSQL to quickly locate and retrieve spatial data:
   ```sql
   CREATE INDEX idx_cities_location ON cities USING GIST (location);
   ```

By following these steps, you can effectively store and query geolocation data in PostgreSQL using PostGIS. This setup is particularly useful for applications that require spatial analysis, such as mapping services, location-based searches, and geographic information systems

### VARCHAR Length

PostgreSQL VARCHAR Unicode Count

In PostgreSQL, the length of a VARCHAR(n) type is determined based on the number of Unicode code points, not bytes. This means that each character, including those represented by multiple bytes in UTF-8, is counted as a single character  For example, a string containing a single emoji, which may require multiple bytes, is counted as one character

The length limit imposed by varchar(n) types and calculated by the length function is in characters, not bytes. So, a string like 'a€c' would be truncated to 'a€c' in a varchar(3) column, even though it uses 5 bytes in a UTF-8 encoded database

To measure the length of a string in PostgreSQL, you can use the length() or char_length() function, which count the number of code points in the string  This is also what is delimited by the type modifier of character varying. PostgreSQL counts the length of a combining character as the number of code points involved

If you want to check, in another language, that some string will fit in a VARCHAR(n), you should check the number of code points in the string, not the number of bytes  This is because PostgreSQL counts the length of a string based on the number of code points, which can vary depending on the encoding of the database

In summary, PostgreSQL's VARCHAR(n) type counts the number of Unicode code points, not bytes, when determining the length of a string. This means that each character, including those represented by multiple bytes, is counted as a single character

### Ionic

**Vue 3 Implementation Patterns**:

```javascript
// Example: Document List Component using Composition API
import { ref, computed, onMounted } from 'vue';
import { useDocumentStore } from '@/stores/documentStore';
import { 
  IonPage, 
  IonHeader, 
  IonToolbar, 
  IonTitle, 
  IonContent,
  IonList,
  IonItem,
  IonLabel,
  IonSearchbar,
  IonButton,
  IonIcon,
  IonChip
} from '@ionic/vue';

/**
 * Document List Component
 * @component
 */
export default {
  name: 'DocumentList',
  components: {
    IonPage,
    IonHeader,
    IonToolbar,
    IonTitle,
    IonContent,
    IonList,
    IonItem,
    IonLabel,
    IonSearchbar,
    IonButton,
    IonIcon,
    IonChip
  },
  props: {
    /** @type {string} Blueprint code */
    blueprintCode: {
      type: String,
      required: true
    }
  },
  setup(props) {
    const documentStore = useDocumentStore();
    const searchTerm = ref('');
    const loading = ref(false);
    const selectedStage = ref('all');

    /**
     * Filtered documents based on search and stage
     * @type {import('vue').ComputedRef<Array>}
     */
    const filteredDocuments = computed(() => {
      let docs = documentStore.documents;
      
      if (searchTerm.value) {
        docs = docs.filter(doc => 
          doc.summary_fields.some(field => 
            String(field.value).toLowerCase().includes(searchTerm.value.toLowerCase())
          )
        );
      }
      
      if (selectedStage.value !== 'all') {
        docs = docs.filter(doc => doc.current_stage.code === selectedStage.value);
      }
      
      return docs;
    });

    /**
     * Load documents for the blueprint
     */
    const loadDocuments = async () => {
      loading.value = true;
      try {
        await documentStore.fetchDocuments(props.blueprintCode);
      } catch (error) {
        console.error('Failed to load documents:', error);
      } finally {
        loading.value = false;
      }
    };

    /**
     * Handle document item click
     * @param {Object} document - Document object
     */
    const handleDocumentClick = (document) => {
      // Navigate to document detail
      router.push(`/documents/${props.blueprintCode}/${document.id}`);
    };

    onMounted(() => {
      loadDocuments();
    });

    return {
      searchTerm,
      loading,
      selectedStage,
      filteredDocuments,
      loadDocuments,
      handleDocumentClick
    };
  }
};
```

**State Management with Pinia**:

```javascript
// stores/documentStore.js
import { defineStore } from 'pinia';
import { ref, computed } from 'vue';
import { documentApi } from '@/api/documents';

/**
 * Document store for managing document state
 */
export const useDocumentStore = defineStore('documents', () => {
  // State
  const documents = ref([]);
  const currentDocument = ref(null);
  const blueprintSchema = ref(null);
  const loading = ref(false);
  const error = ref(null);

  // Getters
  const documentsByStage = computed(() => {
    const grouped = {};
    documents.value.forEach(doc => {
      const stage = doc.current_stage.code;
      if (!grouped[stage]) {
        grouped[stage] = [];
      }
      grouped[stage].push(doc);
    });
    return grouped;
  });

  const documentCount = computed(() => documents.value.length);

  // Actions
  /**
   * Fetch documents for a blueprint
   * @param {string} blueprintCode - Blueprint code
   * @param {Object} filters - Filter options
   */
  const fetchDocuments = async (blueprintCode, filters = {}) => {
    loading.value = true;
    error.value = null;
    
    try {
      const response = await documentApi.getDocuments(blueprintCode, filters);
      documents.value = response.documents;
    } catch (err) {
      error.value = err.message || 'Failed to fetch documents';
      throw err;
    } finally {
      loading.value = false;
    }
  };

  /**
   * Create a new document
   * @param {string} blueprintCode - Blueprint code
   * @param {Object} fieldValues - Document field values
   */
  const createDocument = async (blueprintCode, fieldValues) => {
    loading.value = true;
    error.value = null;

    try {
      const response = await documentApi.createDocument(blueprintCode, {
        field_values: fieldValues
      });
      
      // Add to local state
      documents.value.unshift(response.document);
      
      return response.document;
    } catch (err) {
      error.value = err.message || 'Failed to create document';
      throw err;
    } finally {
      loading.value = false;
    }
  };

  /**
   * Update an existing document
   * @param {string} blueprintCode - Blueprint code
   * @param {number} documentId - Document ID
   * @param {Object} updates - Updates to apply
   */
  const updateDocument = async (blueprintCode, documentId, updates) => {
    loading.value = true;
    error.value = null;

    try {
      const response = await documentApi.updateDocument(blueprintCode, documentId, updates);
      
      // Update local state
      const index = documents.value.findIndex(doc => doc.id === documentId);
      if (index >= 0) {
        documents.value[index] = response.document;
      }
      
      // Update current document if it's the same
      if (currentDocument.value?.id === documentId) {
        currentDocument.value = response.document;
      }
      
      return response.document;
    } catch (err) {
      error.value = err.message || 'Failed to update document';
      throw err;
    } finally {
      loading.value = false;
    }
  };

  /**
   * Clear all state
   */
  const clearState = () => {
    documents.value = [];
    currentDocument.value = null;
    blueprintSchema.value = null;
    error.value = null;
  };

  return {
    // State
    documents,
    currentDocument,
    blueprintSchema,
    loading,
    error,
    
    // Getters
    documentsByStage,
    documentCount,
    
    // Actions
    fetchDocuments,
    createDocument,
    updateDocument,
    clearState
  };
});
```

**Ionic Routing Configuration**:

```javascript
// router/index.js
import { createRouter, createWebHistory } from '@ionic/vue-router';

const routes = [
  {
    path: '/',
    redirect: '/dashboard'
  },
  {
    path: '/dashboard',
    component: () => import('@/views/Dashboard.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/blueprints',
    component: () => import('@/views/BlueprintList.vue'),
    meta: { requiresAuth: true, roles: ['blueprint_admin', 'super_admin'] }
  },
  {
    path: '/blueprints/:code',
    component: () => import('@/views/BlueprintDetail.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/documents/:blueprintCode',
    component: () => import('@/views/DocumentList.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/documents/:blueprintCode/:id',
    component: () => import('@/views/DocumentDetail.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/documents/:blueprintCode/new',
    component: () => import('@/views/DocumentForm.vue'),
    meta: { requiresAuth: true }
  },
  {
    path: '/login',
    component: () => import('@/views/Login.vue')
  },
  {
    path: '/profile',
    component: () => import('@/views/UserProfile.vue'),
    meta: { requiresAuth: true }
  }
];

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes
});

// Navigation guard for authentication
router.beforeEach((to, from, next) => {
  const isAuthenticated = localStorage.getItem('auth_token');
  
  if (to.meta.requiresAuth && !isAuthenticated) {
    next('/login');
  } else {
    next();
  }
});

export default router;
```