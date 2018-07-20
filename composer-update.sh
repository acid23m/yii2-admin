#!/usr/bin/env bash
docker run --rm -i -v $PWD:/app --user $(id -u):www-data composer update --prefer-dist -o -vvv
