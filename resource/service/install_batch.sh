#!/bin/bash

folder=`dirname ${0}`

cp -f "${folder}/kirari" /etc/init.d
cp -f "${folder}/batch.ini" /usr/sbin/kirari.ini
cp -f "${folder}/kirari.sh" /usr/sbin/kirari.sh

chkconfig kirari on

service kirari condrestart
