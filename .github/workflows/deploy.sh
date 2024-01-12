git checkout master
git pull

cd app
php7.4 ~/bin/composer install
php7.4 bin/console doctrine:migrations:migrate
php7.4 bin/console cache:clear