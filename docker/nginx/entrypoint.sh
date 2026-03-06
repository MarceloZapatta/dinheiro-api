#!/bin/sh
set -e

# Substitute environment variables in the nginx config template
envsubst '${NGINX_PORT}' < /etc/nginx/conf.d/default.conf.template > /etc/nginx/conf.d/default.conf

# Start nginx in foreground
exec nginx -g 'daemon off;'
