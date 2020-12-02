# comp 353 project

## Installing

You need:

- PHP v7.2.11
- [Composer](https://getcomposer.org/)
- [npm](https://nodejs.org/en/) (LTS or Current)

After cloning the repo, run (in order):

```sh
npm install
npm run dev
# See note for composer below
composer update # or php composer.phar update
composer install # or php composer.phar install
cp .env.example .env
php artisan key:generate
php artisan cache:clear
php artisan config:clear
```

Then use `php artisan serve` to start the app.

### Installing Composer

Windows users can use the installer.exe (And use `composer update|install`)

On Linux systems, you have to manually install composer (and use `php composer.phar update|install`)

You can use this bash script to install composer.phar:

```sh
EXPECTED_CHECKSUM="$(wget -q -O - https://composer.github.io/installer.sig)"
curl https://getcomposer.org/installer -o composer-setup.php
ACTUAL_CHECKSUM="$(php -r "echo hash_file('sha384', 'composer-setup.php');")"

if [ "$EXPECTED_CHECKSUM" != "$ACTUAL_CHECKSUM" ]
then
    >&2 echo 'ERROR: Invalid installer checksum'
    rm composer-setup.php
    exit 1
fi

php composer-setup.php --quiet
RESULT=$?
rm composer-setup.php
exit $RESULT
```

## Server Setup

- Follow the install guide above, installing in the public `/www/groups/j/jz_comp353_2` directory
  - Here, we are cloning the git repo to the directory `project` and then running the install commands
  - Afterwards, copy all the files in `project` to `/www/groups/j/jz_comp353_2` so you have `/www/groups/j/jz_comp353_2/public`
  - Original plan was to install in the private group directory and create a symlink from the public folder to the `/public` folder of Laravel, but:
    - Hard links do not work since both paths are on different partitions
    - Symlinks do not work since the `Options +FollowSymLinks` directive causes 500 errors, and Apache does not follow symlinks by default
      - My assumption here is that ENCS does not allow for overwriting options from directory `.htaccess` files
    - Instead, we use an `.htaccess` file at the root of the public directory to redirect *all* requests to the Laravel public directory
- Edit the following .env vars:
  - `APP_URL=https://jzc353.encs.concordia.ca/`
  - `DB_DATABASE=the provided database`
  - `DB_USERNAME=the provided username`
  - `DB_PASSWORD=the provided password`
- Create an `.htaccess` in the `/www/...` directory, with the content:

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteRule ^(.*)$ public/$1 [L]
</IfModule>
```

- The server should now be live, have fun.

## Contributing

### Github

**Don't commit to the main branch!** Create a branch off of main, work on it, and then create a pull request 
to merge it into main. I'll approve it then.

```sh
git checkout main
git fetch
git pull origin
git checkout -b your-new-branch
# do some work, make commits after each logical "step"
git add your-files
git commit -m 'Add some descriptive commit message'
# once you're done working on your branch
git push origin
# then open up the git repo and create a pull request
```

[Create a pull request](https://github.com/AtlasTheBot/comp-353-project/pulls)

### The code

See the Laravel 7.x (*not 8.x!*) docs for details:

- https://laravel.com/docs/7.x
- https://laravel.com/docs/7.x/routing
- https://laravel.com/docs/7.x/controllers
- https://laravel.com/docs/7.x/views
- [Bootstrap](https://getbootstrap.com/docs/4.5/getting-started/introduction/) is installed for CSS already (which should be enough)

In short:

- `routes/` -> Create routes, basically mapping from URL -> Controller/View
- `app/Http/Controllers` -> Create the "logic" for the application. If you want to create a `PostController` with `createNewPost()` this is where you would do it.
- `resources/views/` -> Create views, which are HTML templates using [Blade](https://laravel.com/docs/7.x/blade)

If you wanted to create a way for users to post messages for example, you could:

- Create a View in `resources/views/` where the user can write in the post they want to make
- Create a Controller in `app/Http/Controllers` like `PostController` with a method `createNewPost()`
- Create a Route in `routes` like `Route::get('/post/new', 'CreatePostView')`
  - You can create this in a file called like `routes/posts.php`
- Create another Route like `Route::post('/post/new', 'PostController@createNewPost')`
- Create another view to see the post etc

Create a View to display data  
Create a Controller to modify/work with data  
Create a Route to map URLs
