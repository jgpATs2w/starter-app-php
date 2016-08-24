
log="/var/www/html/geval/cron.log"
date >> $log
curl http://gev.anaya.es/geval/src/lib/evaluacion/tools_clean_tmp.php >> $log


