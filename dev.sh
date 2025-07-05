#!/bin/sh

export UID=$(id -u)
export GID=$(id -g)

export SYSENV=${SYSENV:-dev}
export USER_ID=$(id -u)
export GROUP_ID=$(id -g)

if [ -z "$*" ]; then
  docker compose -f "compose.${SYSENV}.yml" config
  exit 0
fi

SERVICES="backend mobileview postgres garage"

# Rebuilding the command line arguments.
# - Otions starts with "-"
# - Use shift to parse all the args, the first arg is always the subcommand of
#   `docker compose`
# - Output the manipulated command line to CMDLINE
SUBCMD="$1"
shift

CMD_ARGS=""
SRV=""
SRV_ARGS=""
ORIGINAL_ARGS="$*"

while [ "$1" != "" ]; do
  case "$1" in
    -*)
      if [ -z "$SRV" ]; then
        CMD_ARGS="$CMD_ARGS $1"
      else
        SRV_ARGS="$SRV_ARGS $1"
      fi
      ;;
    *)
      if [ -z "$SRV" ]; then
        if echo "$SERVICES" | grep -q "$1"; then
          SRV="$1"
        else
          CMD_ARGS="$CMD_ARGS $1"
        fi
      else
        SRV_ARGS="$SRV_ARGS $1"
      fi
      ;;
  esac
  shift
done

# For debugging
# echo "ORIGINAL_ARGS: $ORIGINAL_ARGS"
# echo "CMD_ARGS: $CMD_ARGS"
# echo "SRV_ARGS: $SRV_ARGS"
# echo "SRV: $SRV"
# echo "SUBCMD: $SUBCMD"

if [ "$SUBCMD" = "exec" ]; then
  SRV_ARGS="/entrypoint.sh $SRV_ARGS"
fi

if [ "$SUBCMD" = "psql" ]; then
  SUBCMD="exec"
  CMD_ARGS=""
  SRV="postgres"
  SRV_ARGS=`echo su - postgres -c \"psql $ORIGINAL_ARGS\"`
fi

CMDLINE="$SUBCMD $CMD_ARGS $SRV $SRV_ARGS"

# For debugging
# echo "Running command: $CMDLINE"

FULL_CMDLINE=`echo docker compose -f "compose.${SYSENV}.yml" $CMDLINE`
# echo $FULL_CMDLINE

sh -c "$FULL_CMDLINE"
