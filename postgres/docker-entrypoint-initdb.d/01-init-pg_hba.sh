#!/bin/sh
echo "" >> /var/lib/postgresql/data/pg_hba.conf
echo "host	all		all		0.0.0.0/0		scram-sha-256" \
    >> /var/lib/postgresql/data/pg_hba.conf