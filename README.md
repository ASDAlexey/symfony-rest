## symfony

### Added bundles
1. [DoctrineMigrationsBundle](http://symfony.com/doc/current/bundles/DoctrineMigrationsBundle/index.html)

### Base symfony commands
1. ./bin/console doctrine:database:create // create DB
2. ./bin/console doctrine:database:drop --force // drop DB
3. ./bin/console doctrine:schema:update --force  // update the DB schema
4. ./bin/console doctrine:query:sql ‘SELECT * FROM table’  // view symphony object
5. ./bin/console doctrine:schema:update —dump-sql  // get sql dump
6. ./bin/console  // see all console commands
7. ./bin/console doctrine:migrations:diff // migrations based diff of what was added in the model
8. ./bin/console doctrine:migrations:migrate // to apply the new migration
9. ./bin/console doctrine:fixtures:load // to load the fixtures

### User switch
http://symfony-base.com//admin/item?_switch_user=asdalexey%2b5@yandex.ru
http://symfony-base.com//admin/item?_switch_user=_exit

#### Generate private key(https://knpuniversity.com/screencast/symfony-rest4/lexikjwt-authentication-bundle)
- mkdir var/jwt
- openssl genrsa -out var/jwt/private.pem -aes256 4096
