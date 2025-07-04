#!/bin/sh

export SYSENV=${SYSENV:-dev}
export USER_ID=$(id -u)
export GROUP_ID=$(id -g)

if [ -z "$*" ]; then
  docker compose -f "compose.${SYSENV}.yml" config
else
  docker compose -f "compose.${SYSENV}.yml" "$@"
fi
