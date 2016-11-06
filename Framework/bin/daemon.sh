#!/bin/sh

logfile=../log/daemon.log
server='run.php'

timepoint=$(date -d 'today' +'%Y-%m-%d %H:%M:%S')

proc=$(pgrep -o -f ${server})

if [ -z "${proc}" ]
then
	echo "" >> $logfile
	php run.php restart
	echo "[${timepoint}] ${server} \"php run.php restart\"" >> $logfile
fi