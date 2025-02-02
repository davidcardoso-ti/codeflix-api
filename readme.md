<p align="center">
	<img src="https://laravel.com/assets/img/components/logo-laravel.svg">
</p>

# Notes about patterns used on this project

## Authentication
- client: app\Http\Controllers\Auth
- admin: app\Http\Controllers\Admin\Auth

## IDE auto-complete
- barryvdh/laravel-ide-helper + artisan
- doctrine/dbal + artisan

## Email
- mailtrap.io => SMTP faker
- AWS SES => SMTP

## View
- patricktalmadge/bootstrapper:5.10.* + service provider + facade
- kris/laravel-form-builder:1.11 + service provider + facade

## Routes
- Route::resource('users', 'UsersController');
- Routes Model Binding => route argument equal to the one from the action

## NavBar
- $navbar = Navbar::withBrand(config('app.name'), route('admin.dashboard'))->inverse();
- $menusLeft = Navigation::links($arrayLinksLeft);
- $menusRight = Navigation::links($arrayLinksRight)->right();
- $formLogout = FormBuilder::plain([]);

## CRUD User
- php artisan make:controller "Admin\UsersController" --resource --model="CodeFlix\Models\User"
- php artisan make:form "Forms\UserForm" --fields="name:text, email:email"

## Design Pattern:
- Repositories are services from access layer and integration  with the databases entities
- prettus/l5-repository:2.6.27 + service provider
- php artisan vendor:publish --provider="Prettus\Repository\Providers\RepositoryServiceProvider"

## Refactoring CRUD User using L5-Repository
- rename _User.php_ to _User_old.php_
- php artisan make:repository User
- delete new _User.php_ and rename again _User_old.php_ to _User.php_
- delete new migration of User model
- In register() method on _RepositoryServiceProvider.php_: 
	- $this->app->bind(UserRepository::class, UserRepositoryEloquent::class);

## User Verification
- jrean/laravel-user-verification:4.1.2 + service provider + facade
- php artisan vendor:publish --provider="Jrean\UserVerification\UserVerificationServiceProvider" --tag=migrations
- edit namespace of the user model on the new migration
- php artisan vendor:publish --provider="Jrean\UserVerification\UserVerificationServiceProvider" --tag=config
- php artisan migrate
- adjust user admin data migration to be verified by default
- add route middleware **IsVerified::class** on _app\Http\Kernel.php_
- add middleware **isVerified** on admin group routes
- php artisan vendor:publish --provider="Jrean\UserVerification\UserVerificationServiceProvider" --tag=views
- php artisan make:controller EmailVerificationController
- add EmailVerification routes on _routes/web.php_
- edit EmailVerification views
- create _resources/lang/<your-lang>/user-verification.php_

## User settings
- php artisan make:controller "Admin\Auth\UserSettingsController" --resource
	- implements edit and update actions
- add routes on _routes\web.php_
	- Route::get('users/settings', 'Auth\UserSettingsController@edit')->name('user-settings.edit');
    - Route::put('users/settings', 'Auth\UserSettingsController@update')->name('user-settings.update');
- refactor **redirectAfterVerification()** action of **EmailVerificationController** to redirect to route('admin.user-settings.edit')
- php artisan make:form "Forms\UserSettingsForm" --fields="password:password, password_confirmation:password"
- create _resources/views/admin/auth/setting.blade.php_
- add new menu option on _resources/views/layouts/admin.blade.php_
- files used
	- new:   
		- app/Http/Controllers/Admin/Auth/UserSettingsController.php
		- app/Forms/UserSettingsForm.php
		- resources/views/admin/auth/setting.blade.php
	- modified:
		- app/Http/Controllers/EmailVerificationController.php
		- app/Repositories/UserRepositoryEloquent.php
		- resources/views/shared/navbar.blade.php
		- routes/web.php

## CRUD Category using L5-Repository with full options
- php artisan make:entity Category
	- Presenter: no
	- Validator: no
	- Controller: yes
- files used
	- modified:
		- config/app.php
	- new:
		- app/Http/Controllers/CategoriesController.php
		- app/Http/Requests/
		- app/Models/Category.php
		- app/Providers/RepositoryServiceProvider.php
		- app/Repositories/
		- database/migrations/2017_11_12_204552_create_categories_table.php

## CRUD Category using L5-Repository with minimal options
- php artisan make:repository Category

## CRUD Serie
- php artisan make:repository Serie
- php artisan make:controller "Admin\SeriesController" --resource --model="CodeFlix\Models\Serie"
- php artisan make:seeder "SeriesTableSeeder"
- php artisan make:form "Forms\SerieForm" --fields="title:text, description:textarea"
- php artisan migrate:refresh --seed

## CRUD Video
- php artisan make:repository Video
- php artisan make:form "Forms\VideoForm" --fields="title:text, description:textarea"
- php artisan make:controller "Admin\VideosController" --resource --model="CodeFlix\Models\Video"
    - In _VideosController_: implement all actions
    - In _web.php_ routes: add videos resources routes
- php artisan make:seeder VideosTableSeeder
    - In _VideosTableSeeder_: $video->serie()->associate($serie);
    - php artisan migrate:refresh --seed
- In _Videos.php_ model: set $fillable and implement TableInterface methods
- php artisan make:migration create_category_video_table --create=category_video
    - php artisan migrate
    - In _VideosTableSeeder_: $video->categories()->attach($categories->random(4)->pluck('id'));
    - php artisan migrate:refresh --seed
- In _resources/views/admin/videos_: create and implement views
    - to manage videos using tabs, use blade component and slot
- php artisan make:controller "Admin\VideoRelationsController" --resource --model="CodeFlix\Models\Video"
    - In _VideoRelationsController_: implement create and store actions
    - In _web.php_ routes: add videos relations routes
- php artisan make:form "Forms\VideoRelationForm"

## Refactor CRUD Serie
- handling thumb/image upload/download
    - mkdir -p app/Media
    - create trait _MediaStorages.php_
    - create trait _SeriePaths.php_
    - create trait _ThumbUploads.php_
    - refactor _SerieTableSeeder.php_ to upload thumb and update the model
    - requirements
        - composer require folklore/image:0.3.20
        - apt update && apt install php-gmagick # use 'gd' or 'imagick' alternatively
        - add Facade 'Image' => \Folklore\Image\Facades\Image::class
        - add Provider Folklore\Image\ImageServiceProvider::class
        - php artisan vendor:publish --provider="Folklore\Image\ImageServiceProvider"
    - refactor _Serie.php_, _SeriesController.php_, _SeriePaths.php_, _SerieRepositoryEloquent.php_
    - refactor _web.php_
    - refactor _SerieForm.php_, _admin/series/index.blade.php_
    - refactor _ThumbUploads.php_





























