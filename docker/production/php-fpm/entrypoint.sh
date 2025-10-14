#!/bin/sh
#  Cache the configuration, events, routes, and views
php artisan optimize

# Run the main container command
exec "$@"
