# comp 353 project

## Installing

You need:

- [npm](https://nodejs.org/en/) (LTS or Current)
- PHP v7.2.11

After cloning the repo, run:

```sh
npm install
npm run dev
```

Then use `php artisan serve` to start the app.

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
