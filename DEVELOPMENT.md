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

**Starting the containers**

```bash
./dev.sh up --build -d
```

**Stopping the containers**

```bash
./dev.sh down
```


**Restarting the containers**

```bash
./dev.sh restart
```

**Accessing postgres psql**

```bash
./dev.sh psql
```

**Accessing garage**

```bash
./dev.sh garage
```

**Accessing shell**

```bash
./dev.sh shell <service>
```

**Cleaning volumes data**

```bash
./dev.sh vclean
```

## Modules and Services

### Database

The database is a PostgreSQL instance that runs on default port of `5432`.

Directory: `postgres/`
Volumes: `volumes/postgres_data/`


**Accessing postgres psql CLI**

```bash
./dev.sh psql
```

### Backend

The backend is a Fastify server that runs on default port of `3000`.

Directory: `backend/`

Watched by `nodemon` to automatically restart the server when code changes.

**Adding new dependencies**

```bash
./dev.sh exec backend npm install <package>
```

### Frontend

The frontend is an Ionic application that runs on default port of `8100`.

Directory: `mobileview/app/`

Watched by `ionic serve` to automatically hot reload the application when code
changes.

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
