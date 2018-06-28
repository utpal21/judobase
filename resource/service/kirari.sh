#!/bin/bash

folder=`dirname ${0}`
if [ "${1}" = "1" ]
then
	ini="${folder}/kirari.ini"
	interval=`sed -n '1,1p' ${ini}`
	url=`sed -n '2,1p' ${ini}`

	while [ true ]
	do
		wget -q $url --delete-after
	#	echo "request ${url}"
		sleep $interval
	done
else
	"${folder}/kirari.sh" 1 &
fi
exit 0
