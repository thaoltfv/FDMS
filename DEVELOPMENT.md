# How to start local development

## Prerequisites

- Linux or MacOS or WSL2 with bash/zsh (No fucking Windows CMD nor Powershell)
- Docker
- Docker Compose

## Development tools

The `dev.sh` script is a wrapper around `docker compose` that uses
`compose.dev.yml`as the default compose file and sets up the environment
variables to run the containers. It also provides a few shortcuts for common
operations.

**dev.sh usage**

```bash
Usage: ./dev.sh <subcommand> <service> <args>
Docker compose sub-commands:
  up - Start the development environment
  down - Stop the development environment
  restart - Restart the development environment
  logs - Follow logs of the development environment
  exec - Execute a command in a service
  (...) - All other docker compose sub-commands

Custom sub-commands:
  vclean - Clean volumes data
  psql - Access PostgreSQL CLI
  garage - Access Garage CLI
  shell - Access shell of a service
```

**Starting the whole development environment**

```bash
./dev.sh up --build -d
```

**Stopping the development environment**

```bash
./dev.sh down
```

**Restarting the development environment**

```bash
./dev.sh restart
```

**Accessing postgres psql CLI**

```bash
./dev.sh psql
```

**Accessing garage CLI**

```bash
./dev.sh garage
```

**Accessing shell**

```bash
./dev.sh shell <service>
```

This command will open a shell in the container of the service. This command is
not accepting any arguments except the service name.

**Cleaning volumes data**

If we want a fresh start, we can clean the volumes data. This command will
remove all the data in the volumes of the development environment.

```bash
./dev.sh vclean
```

**Rebuild instead of restart**

More often than not, we want to rebuild the containers instead of restarting them.

## Modules and Services

### Database

The database is a PostgreSQL instance that runs on default port of `5432`.

Directory: `postgres/`

Volumes: `volumes/postgres_data/`

**Accessing postgres psql CLI**

```bash
./dev.sh psql <options> <args>
```

This command passes the arguments to the actual psql CLI.

### Backend

#### Backend overview

The backend is a Fastify server that runs on default port of `3000`.

Directory: `backend/`

Watched by `nodemon` to automatically restart the server when code changes.

#### Backend structure

**Technology Stack**
- **Framework**: Fastify (Node.js web framework)
- **Database**: PostgreSQL with Knex.js ORM
- **Authentication**: JWT-based with role-based access control
- **Documentation**: Swagger/OpenAPI with Swagger UI
- **Development**: Nodemon for auto-restart

**Project Structure**
```
backend/
├── src/
│   ├── index.js              # Main entry point and server configuration
│   ├── database/
│   │   ├── connection.js     # Database connection setup
│   │   ├── knexfile.js       # Knex configuration (dev/prod)
│   │   ├── migrations/       # Database schema migrations
│   │   └── seeds/            # Database seed data
│   ├── middleware/
│   │   └── auth.js           # Authentication & authorization middleware
│   ├── modules/              # Feature-based route modules
│   │   ├── auth/             # Authentication routes
│   │   ├── blueprint/        # (Placeholder for blueprint management)
│   │   ├── document/         # (Placeholder for document management)
│   │   └── file-storage/     # (Placeholder for file storage)
│   └── services/
│       └── authService.js    # Authentication business logic
├── package.json              # Dependencies and scripts
├── Dockerfile                # Container configuration
└── entrypoint.sh             # Container entry script
```

**Core Components**

1. **Entry Point** (`src/index.js`)
   - Fastify server configuration
   - Plugin registration (CORS, JWT, Swagger)
   - Route registration with prefixes
   - Global error handling
   - Health check and root endpoints

2. **Database Layer** (`src/database/`)
   - **Connection**: PostgreSQL connection via Knex.js
   - **Migrations**: Schema version control with automatic UUID generation
   - **Seeds**: Default roles (super_admin, blueprint_admin, data_manager, viewer, auditor)
   - **Tables**: Users, roles, user_roles with proper indexing

3. **Authentication System**
   - **JWT-based**: Secure token authentication
   - **Role-based Access Control**: 5 predefined system roles
   - **Password Security**: bcrypt with 12 salt rounds
   - **Middleware**: `authenticate()`, `authorize()`, `optionalAuth()`

4. **API Documentation**
   - **Swagger/OpenAPI**: Auto-generated API docs
   - **Swagger UI**: Interactive documentation at `/documentation`
   - **Schema Validation**: Request/response validation

5. **Module Architecture**
   - **Modular Routes**: Feature-based organization
   - **Prefix Support**: Routes grouped under `/api/{module}`
   - **Schema Definitions**: Comprehensive input/output validation

**Available Scripts**

Remember to prefix `./dev.sh exec backend` before running any bellow npm command.

- `npm run dev`: Start development server with auto-restart
- `npm run migrate`: Run database migrations
- `npm run migrate:make <name>`: Create new migration
- `npm run migrate:rollback`: Rollback last migration
- `npm run seed`: Run database seeds

**API Endpoints**
- `GET /`: API information and version
- `GET /health`: Health check endpoint
- `GET /documentation`: Interactive API documentation
- `POST /api/auth/register`: User registration
- `POST /api/auth/login`: User authentication
- `GET /api/auth/profile`: User profile (authenticated)

**Key Features**
- **Auto-restart**: Nodemon watches for code changes
- **Database Migrations**: Version-controlled schema changes
- **Role-based Security**: Granular permission system
- **Comprehensive Logging**: Built-in Fastify logging
- **Error Handling**: Standardized error responses
- **CORS Support**: Cross-origin resource sharing enabled

#### Hints

**Adding new dependencies**

```bash
./dev.sh exec backend npm install <package>
```

### Frontend

#### Frontend overview

The frontend is an Ionic application that runs on default port of `8100`.

Directory: `mobileview/app/`, mounted to `/app` in the container.

Watched by `ionic serve` to automatically hot reload the application when code
changes.

#### Frontend structure

**Technology Stack**

- **Framework**: Ionic + Angular (standalone)
- **UI Framework**: Ionic UI Components
- **State Management**: Angular Services + RxJS
- **Routing**: Angular Router with lazy loading
- **HTTP Client**: Angular HttpClient
- **Mobile Platform**: Capacitor
- **Testing**: Jasmine + Karma
- **Internationalization**: Angular i18n
- **Build Tool**: Angular CLI

**Project Structure**

```
mobileview/
├── app/
│   ├── src/
│   │   ├── app/
│   │   │   ├── app.component.ts        # Root component
│   │   │   ├── app.component.html      # Root component template
│   │   │   ├── app.component.scss      # Root component styles
│   │   │   ├── app.component.spec.ts   # Root component tests
│   │   │   ├── app.routes.ts           # Application routing configuration
│   │   │   ├── auth.guard.ts           # Route authentication guard
│   │   │   ├── auth.service.ts         # Authentication service
│   │   │   ├── home/                   # Home page component
│   │   │   ├── login/                  # Login page component
│   │   │   ├── profile/                # Profile page component
│   │   │   └── register/               # Register page component
│   │   ├── assets/                     # Static assets
│   │   ├── environments/               # Environment configurations
│   │   ├── locale/                     # Internationalization files
│   │   ├── theme/                      # Global theming
│   │   ├── global.scss                 # Global styles
│   │   ├── index.html                  # Main HTML file
│   │   ├── main.ts                     # Application bootstrap
│   │   ├── polyfills.ts                # Browser polyfills
│   ├── angular.json                    # Angular CLI configuration
│   ├── capacitor.config.ts             # Capacitor configuration
│   ├── ionic.config.json               # Ionic CLI configuration
│   ├── karma.conf.js                   # Karma test configuration
│   ├── package.json                    # Dependencies and scripts
│   ├── tsconfig.json                   # TypeScript configuration
│   ├── tsconfig.app.json               # App TypeScript configuration
│   └── tsconfig.spec.json              # Test TypeScript configuration
├── Dockerfile                          # Container configuration
└── entrypoint.sh                       # Container entry script
```

**Core Components**

1. **Application Structure** (`src/app/`)
   - **Standalone Components**: Angular 17+ standalone architecture
   - **Lazy Loading**: Route-based code splitting
   - **Page Components**: Feature-based organization (home, login, profile, register)
   - **Services**: Shared business logic (auth.service.ts)
   - **Guards**: Route protection (auth.guard.ts)

2. **Authentication System**
   - **JWT-based**: Token-based authentication
   - **Local Storage**: Persistent token storage
   - **Role-based Access**: User roles and permissions
   - **Guard Protection**: Route-level security
   - **Observable State**: Reactive user state management

3. **Routing Configuration**
   - **Lazy Loading**: Dynamic component imports
   - **Route Guards**: Protected routes with authGuard
   - **Redirects**: Default route handling
   - **Navigation**: Programmatic and declarative routing

4. **Internationalization**
   - **Angular i18n**: Built-in internationalization
   - **Vietnamese Support**: Complete Vietnamese translation
   - **Locale Files**: XLF translation format
   - **Build Integration**: Locale-specific builds

5. **Mobile Platform**
   - **Capacitor**: Native mobile capabilities
   - **Ionic Components**: Mobile-optimized UI components
   - **Progressive Web App**: PWA support
   - **Device Features**: Camera, storage, notifications

**Available Scripts**

Remember to prefix `./dev.sh exec mobileview` before running any below npm command.

- `npm run start`: Start development server with live reload
- `npm run build`: Build the application for production
- `npm run test`: Run unit tests
- `npm run lint`: Run code linting
- `npm run watch`: Build and watch for changes

**Key Features**
- **Responsive Design**: Mobile-first approach
- **Dark/Light Mode**: Theme switching support
- **Authentication Flow**: Complete login/register/profile
- **Route Protection**: Guard-based security
- **Internationalization**: Multi-language support
- **PWA Ready**: Progressive Web App capabilities
- **Live Reload**: Hot module replacement in development



#### Hints

**Adding new dependencies**

```bash
./dev.sh exec mobileview npm install <package>
```

**Accessing frontend CLI**

```bash
./dev.sh shell mobileview
```

### Garage (S3 compatible storage)

The garage is a S3 compatible storage that runs on default port of `3900`.

Docker image Directory: `garage/` (based on `dxflrs/garage:v2.0.0`)

Configuration: `garage/garage.toml`

Volumes: `volumes/garage_meta/` and `volumes/garage_data/`

**Accessing garage CLI**

```bash
./dev.sh garage
```

## General Development rules

- List `docs/` folders for available documentation.
- To install more packages or run any command, follow guide in `DEVELOPMENT.md`
  (not run `npm` directly)
- Do not create more dotenv (.env) files in sub folders. Use the `.env` in
  project root, as we use docker compose to manage container and environment.
- When design frontend components, try to use default Ionic style without much
  customization unless necessary.
- When design frontend pages, do not use `WIREFRAME_DESIGN_RULES.md` as
  reference for color and styling, use default Ionic style. This document is 
  only applying for Wireframes, not for actual design, do not use it for color
  and icons.
- There are some SVG wireframes in `docs/wireframes/screen*` folder, use them as
  reference for LAYOUT only on frontend pages.
- When writing code for any sub-module/sub-project/service, first thing to do is
  to read `README.md` of that module/project/service if available.
- When writing code, try to follow the existing code styles.
- When writing code, try to follow the existing code conventions.
- When writing code, try to follow the existing code guidelines.
- When writing code, try to follow the existing code patterns.
- When writing code, try to follow the existing code architecture.

## Ionic specific rules

### Guidelines

- Support both `light` and `dark` mode.

### Hints

#### Example adding icons

In module ts, import:

```ts
import { addIcons } from 'ionicons';
import { personCircleOutline } from 'ionicons/icons';
```

in modules ts constructor, add icons:

```ts
addIcons({ 'person-circle-outline': personCircleOutline });
```

in module html, use icons:

```html
<ion-icon name="person-circle-outline"></ion-icon>
```
