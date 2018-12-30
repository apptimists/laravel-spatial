mySrv=mysql

oV=5.7
oDB_DIR=$(shell pwd)/_odb-$(oV)

mV=10.3
mDB_DIR=$(shell pwd)/_mdb-$(mV)

pV=10
pDB_DIR=$(shell pwd)/_pdb-$(pV)

start_db_mysql:
	@echo Starting MySQL $(oV)
	docker run --rm -d --name spatial-mysql \
    -p 3306:3306 \
    -v $(oDB_DIR):/var/lib/mysql \
		--health-cmd='mysqladmin ping --silent && mysql -e "use spatial_test;"' \
    -e MYSQL_DATABASE=spatial_test \
    -e MYSQL_ALLOW_EMPTY_PASSWORD=yes \
    mysql:$(oV) --character-set-server=utf8 --collation-server=utf8_general_ci --default-authentication-plugin=mysql_native_password

start_db_maria:
	@echo Starting MariaDB $(mV)
	docker run --rm -d --name spatial-mysql \
		-p 3306:3306 \
		-v $(mDB_DIR):/var/lib/mysql \
		--health-cmd='mysqladmin ping --silent && mysql -e "use spatial_test;"' \
		-e MYSQL_DATABASE=spatial_test \
		-e MYSQL_ALLOW_EMPTY_PASSWORD=yes \
		mariadb:$(mV) --character-set-server=utf8 --collation-server=utf8_general_ci

start_db_postgres:
	@echo Starting Postgres $(pV)
	docker run --rm -d --name spatial-postgres \
		-p 5432:5432 \
		-v $(pDB_DIR):/var/lib/postgresql/data \
		--health-cmd='if [[ $(pV) -ge 9.3 ]]; then pg_isready -U postgres; else psql -U postgres -c "SELECT 1"; fi' \
		-e POSTGRES_DB=spatial_test \
		-e POSTGRES_PASSWORD="" \
		mdillon/postgis:$(pV)

rm_db_mysql:
	docker stop spatial-mysql || true
	sudo rm -Rf $(oDB_DIR)

rm_db_maria:
	docker stop spatial-mysql || true
	sudo rm -Rf $(mDB_DIR)

rm_db_postgres:
	docker stop spatial-postgres || true
	sudo rm -Rf $(pDB_DIR)

refresh_db_mysql: rm_db_mysql start_db_mysql

refresh_db_maria: rm_db_maria start_db_maria

refresh_db_postgres: rm_db_postgres start_db_postgres

get_ip_mysql:
	@docker inspect -f '{{range .NetworkSettings.Networks}}{{.IPAddress}}{{end}}' spatial-mysql

get_ip_postgres:
	@docker inspect -f '{{range .NetworkSettings.Networks}}{{.IPAddress}}{{end}}' spatial-postgres

run_tests: start_db_${mySrv} start_db_postgres __do_test rm_db_${mySrv} rm_db_postgres

__do_test:
	@echo -n "\nWaiting for servers to fully start"
	@./wait_container spatial-mysql
	@./wait_container spatial-postgres
	@echo " Ready! Starting tests.\n"
	@cd .. && ./vendor/bin/phpunit || true
