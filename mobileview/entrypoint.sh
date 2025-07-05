#!/bin/sh
set -e

# replace uid and gid id in /etc/passwd for user node
sed -i "s/^node:x:[0-9]*:[0-9]*:/node:x:${USER_ID}:${GROUP_ID}:/" /etc/passwd

# Run command with node if the first argument contains a "-" or is not a system command. The last
# part inside the "{}" is a workaround for the following bug in ash/dash:
# https://bugs.debian.org/cgi-bin/bugreport.cgi?bug=874264
if [ "${1#-}" != "${1}" ] || [ -z "$(command -v "${1}")" ] || { [ -f "${1}" ] && ! [ -x "${1}" ]; }; then
  set -- node "$@"
fi

PROG=$1
shift

# echo "Running command: $PROG $*"

su node -c "$PROG $*"

exit $?
