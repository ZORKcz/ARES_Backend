#!/bin/sh
set -e

echo "Starting entrypoint.sh"
# Composer
composer install

symfony check:requirements

# Symfony
bin/console ca:cl
symfony server:stop

echo "---- FIRST_RUN: ${FIRST_RUN} ----"
# If ran for the first time, create database etc.
if [ "${FIRST_RUN}" = 1 ]; then
    symfony console doctrine:database:drop --force --if-exists
    symfony console doctrine:database:create --if-not-exists
    symfony console doctrine:migrations:migrate --no-interaction
    symfony console doctrine:fixtures:load --no-interaction
    echo "---- All setup for first run done, restarting container ----"
    exit 0;  # this means that the first run setup has finished
else

#  symfony console doctrine:migrations:migrate --no-interaction

  # first arg is `-f` or `--some-option`
  if [ "${1#-}" != "$1" ]; then
    set -- php "$@"
  fi

  exec "$@"
fi

echo "Finished entrypoint.sh"