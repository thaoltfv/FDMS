#!/bin/sh

echo "Starting up.."

su - $USER_NAME -c "mkdir -p /srv/www/storage/api-docs"
su - $USER_NAME -c "mkdir -p /srv/www/storage/app/public"
su - $USER_NAME -c "mkdir -p /srv/www/storage/framework/cache/data"
su - $USER_NAME -c "mkdir -p /srv/www/storage/framework/sessions"
su - $USER_NAME -c "mkdir -p /srv/www/storage/framework/testing"
su - $USER_NAME -c "mkdir -p /srv/www/storage/framework/views"
su - $USER_NAME -c "mkdir -p /srv/www/storage/logs"

supervisord -c /etc/supervisord.conf
