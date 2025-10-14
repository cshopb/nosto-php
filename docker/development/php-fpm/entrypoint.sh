#!/bin/sh

# Create key if necessary.
if ! grep -q APP_KEY .env 2>/dev/null ; then
  php artisan key:generate
fi

#  Clear cached configuration, events, routes, and views
php artisan optimize:clear

# Run the main container command
exec "$@"
