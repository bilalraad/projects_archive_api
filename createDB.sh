echo "Please enter password of the root MySQL user!"
echo "Note: password will be hidden when typing"
read -sp PASSWDDB
# replace "-" with "_" for database username
MAINDB="projects_archive_api"
MAINUSER="computerAdmin"


RESULT=`mysqlshow --user=${MAINUSER} --password=${PASSWDDB} ${MAINDB}| grep -v Wildcard | grep -o ${MAINDB}`
echo "$RESULT" == "$MAINDB"
if [ "$RESULT" != "$MAINDB" ]; then
mysql -u root -p${rootpasswd} -e "CREATE DATABASE ${MAINDB} /*\!40100 DEFAULT CHARACTER SET utf8 */;"
fi
mysql -u root -p${rootpasswd} -e "SELECT host, user FROM mysql.user;"
mysql -u root -p${rootpasswd} -e "DROP USER '${MAINUSER}'@localhost;"
mysql -u root -p${rootpasswd} -e "FLUSH PRIVILEGES;"
mysql -u root -p${rootpasswd} -e "CREATE USER ${MAINUSER}@localhost IDENTIFIED BY '${PASSWDDB}';"
mysql -u root -p${rootpasswd} -e "GRANT ALL PRIVILEGES ON ${MAINDB}.* TO '${MAINUSER}'@'localhost';"
mysql -u root -p${rootpasswd} -e "FLUSH PRIVILEGES;"


