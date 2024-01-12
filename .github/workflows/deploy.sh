git checkout master
git pull

php7.4 ~/bin/composer install
php7.4 bin/console doctrine:migrations:migrate
php7.4 bin/console cache:clear