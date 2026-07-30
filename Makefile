SAIL=./vendor/bin/sail

up:
	$(SAIL) up -d

dev:
	$(SAIL) npm run dev

do:
	$(SAIL) down

clear:
	$(SAIL) artisan optimize:clear

migrate:
	$(SAIL) artisan migrate

migratelist:
	$(SAIL) artisan migrate:status

seed:
	$(SAIL) artisan db:seed