#!/bin/sh -xe

rm -f deploy.tar.gz

tar -czf deploy.tar.gz dist captain-definition

caprover deploy -t deploy.tar.gz $@
