#!/bin/bash

echo "Starting up.."

supervisord -c /etc/supervisord.conf
