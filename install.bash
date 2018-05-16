#!/usr/bin/env bash

COMPOSER="../composer.phar"

cd "$( dirname "${BASH_SOURCE[0]}" )"

check_composer ()
{
    local found=0
    if [ -z $(command -v $COMPOSER) ] ;
    then
        echo -e "Composer not found. Install one from your package manager
        OR get one here:
        https://getcomposer.org/download/ and update COMPOSER variable in this script
        then rerun script"
        exit 1
    else
        echo "Using $COMPOSER as composer binary"
    fi
}

update_vendor ()
{
    echo -e "Running $COMPOSER update to install deps"
    $COMPOSER update
}

check_conf_file ()
{
    if [ ! -f $1 ] ; then
        echo -e "File $1 does not exists.
        Please copy:
        cp $1{.sample,}
        And update with real data"
        exit 1
    else
        echo "Found config file $1"
        cat $1
    fi
}

check_config ()
{
    check_conf_file "./config/config.php"
    check_conf_file "./config/phinx.yml"
}

migrate ()
{
    echo -e "Running migrations"
    ./vendor/bin/phinx migrate -c ./config/phinx.yml
    ./vendor/bin/phinx seed:run -c ./config/phinx.yml

}

check_composer
update_vendor
check_config
migrate