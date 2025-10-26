#!/bin/sh

influx setup \
    --name INFLUXDB_DB \
    --host influxdb \
    -u INFLUXDB_ADMIN_USER \
    -p INFLUXDB_ADMIN_PASSWORD \
    -o INFLUXDB_ORG \
    -b INFLUXDB_BUCKET \
    -t INFLUXDB_TOKEN \
    -r 0 -f

# Run the main container command
exec "$@"
