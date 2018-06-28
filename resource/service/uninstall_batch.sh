#!/bin/bash

service kirari stop

chkconfig kirari off

rm -rf /etc/init.d/kirari
rm -rf /usr/sbin/kirari.ini
rm -rf /usr/sbin/kirari.sh
