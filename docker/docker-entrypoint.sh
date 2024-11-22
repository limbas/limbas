#!/bin/sh

limbasConfDocker='/opt/include_db_docker.lib'
limbasConf='/var/www/html/openlimbas/dependent/inc/include_db.lib'
limbasDockerFlag='/var/www/html/openlimbas/dependent/inc/docker'

touch $limbasDockerFlag

usesEnvVar=false

# check if any LIMBAS environment variable was passed
if [ -n "${LIMBAS_DB_HOST}" ]; then
  usesEnvVar=true
elif [ -n "${LIMBAS_DB_TYPE}" ]; then
  usesEnvVar=true
elif [ -n "${LIMBAS_DB_USER}" ]; then
  usesEnvVar=true
elif [ -n "${LIMBAS_DB_PASSWORD}" ]; then
  usesEnvVar=true
elif [ -n "${LIMBAS_HOST}" ]; then
  usesEnvVar=true
elif [ -n "${LIMBAS_DB_PORT}" ]; then
  usesEnvVar=true
elif [ -n "${LIMBAS_DB_VERSION}" ]; then
  usesEnvVar=true
fi

# if LIMBAS environment variable passed copy config file if not exists
if [ "$usesEnvVar" = true ] && [  ! -f "$limbasConf" ] && [  -f "$limbasConfDocker" ] ; then
    awk ' /unique key/ {
						cmd = "head -c1m /dev/urandom | sha1sum | cut -d\\  -f1"
						cmd | getline str
						close(cmd)
						gsub("unique key", str)
					}
					{ print }
				' "$limbasConfDocker" > "$limbasConf"
				
		if [ -f "$limbasConfDocker" ] ; then
      rm "$limbasConfDocker"
    fi
    
    
    if [ -n "${LIMBAS_AUTO_INSTALL}" ]; then
        php /var/www/html/openlimbas/limbas_src/admin/install/autoinstall.php
    fi
    
fi

chown -R www-data:www-data /var/www/html

exec "$@"
