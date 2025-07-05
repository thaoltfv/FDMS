#!/bin/sh

export SYSENV=${SYSENV:-dev}
export USER_ID=$(id -u)
export GROUP_ID=$(id -g)

if [ -z "$*" ]; then
  docker compose -f "compose.${SYSENV}.yml" config
  exit 0
fi

SERVICES="backend mobileview postgres"

# Rebuilding the command line arguments
# options starts with "-"
# use shift to parse all the args, the first arg is always the subcommand of `docker compose`
# output the manipulated command line to CMDLINE
SUBCMD="$1"
shift
CMD_ARGS=""
SRV=""
SRV_ARGS=""

while [ "$1" != "" ]; do
  case "$1" in
    -*)
      if [ -z "$SERVICE" ]; then
        CMD_ARGS="$CMD_ARGS $1"
      else
        SRV_ARGS="$SRV_ARGS $1"
      fi
      ;;
    *)
      if [ -z "$SRV" ]; then
        SRV="$1"
      else
        SRV_ARGS="$SRV_ARGS $1"
      fi
      ;;
  esac
  shift
done

if [ "$SUBCMD" = "exec" ]; then
  SRV_ARGS="/entrypoint.sh $SRV_ARGS"
fi

CMDLINE="$SUBCMD $CMD_ARGS $SRV $SRV_ARGS"

docker compose -f "compose.${SYSENV}.yml" $CMDLINE
