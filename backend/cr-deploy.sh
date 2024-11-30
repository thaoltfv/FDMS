#!/bin/sh

rm -f deploy.tgz

tar -czf deploy.tgz --exclude vendor --exclude .vscode --exclude deploy.tgz --exclude . --exclude .. .* *

caprover deploy -t deploy.tgz $@
