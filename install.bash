#!/usr/bin/env bash

COMPOSER="composer"

cd "$( dirname "${BASH_SOURCE[0]}" )"

check_composer ()
{
    local found=0
    if [ -z $(command -v $COMPOSER) ] ;
    then
        echo -e "Composer not found. Install one from your package manager
        OR get one here:
        https://getcomposer.org/download/ and update COMPOSER variable in this file: $0
        then rerun $0"
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

goodbuy ()
{
    echo -e "================================================
    All done.\n Now you can call \ncd $(pwd) && ./serve.bash\n to start a local server\n"
}

check_composer
update_vendor
check_config
migrate
goodbuy