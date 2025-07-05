#!/bin/sh
set -e

# replace uid and gid id in /etc/passwd for user node
sed -i "s/^postgres:x:[0-9]*:[0-9]*:/postgres:x:${USER_ID}:${GROUP_ID}:/" /etc/passwd

chown -R postgres:postgres /var/lib/postgresql
chown -R postgres:postgres /var/run/postgresql

exec docker-entrypoint.sh "$@"

exit $?
