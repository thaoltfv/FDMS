# How to start local development

## Prerequisites

- Linux or MacOS or WSL2 with bash/zsh (No fucking Windows CMD nor Powershell)
- Docker
- Docker Compose

## Dev tools

The `dev.sh` script is a wrapper around `docker compose` that uses
`compose.dev.yml`as the default compose file and sets up the environment
variables to run the containers. It also provides a few shortcuts for common
operations.

**Starting the containers**

```bash
./dev.sh up --build
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

**Accessing garage CLI**

```bash
./dev.sh garage status
```


## Backend

The backend is a Fastify server that runs on default port of `3000`.

Directory: `backend/`

Watched by `nodemon` to automatically restart the server when code changes.

**Adding new dependencies**

```bash
./dev.sh exec backend npm install <package>
```

## Frontend

The frontend is an Ionic application that runs on default port of `8100`.

Directory: `mobileview/app/`

Watched by `ionic serve` to automatically hot reload the application when code
changes.


