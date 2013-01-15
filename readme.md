# Restful routes for Laravel 4

Create restful routes for Laravel 4, included nested controllers, named routes and custom templates.
This greatly enhances the `Route::Resource` method currently available in Laravel 4, which currently
offers none of these features.

## Getting started

### Composer

Add `"jonob/restful": ">=1.0.*"` to the `require` section of your `composer.json`:

```composer
"require": {
	"jonob/restful": ">=1.0.*"
},

```

Now run `composer install`.

### Laravel

Add the following code to the `aliases` section of the `app/config/app.php` file

```php
'Restful' => 'Jonob\Restful\Restful',
```

so that it looks something like the following:

```php
'aliases' => array(
	...
	'Restful'       => 'Jonob\Restful\Restful',
	...
),
```

## Adding Restful Routes
Restful Routes are created in app\routes.php as follows:

```php
// Create a new bunch of restful routes for the 'products' resource
new Restful('products', 'ProductsController');

// Or use the static method
Restful::make('products', 'ProductsController');
```

This will automatically create a whole bunch of restful routes for you as follows:
```
Route::get('products/{id}/edit', array('as' => 'ProductEdit', 'uses' => 'ProductsController@edit');
Route::get('products/add', array('as' => 'ProductAdd', 'uses' => 'ProductsController@create');
Route::get('products/{id}', array('as' => 'Product', 'uses' => 'ProductsController@show');
Route::get('products', array('as' => 'Products', 'uses' => 'ProductsController@index');
Route::post('products', array('as' => 'ProductStore', 'uses' => 'ProductsController@store');
Route::put('products/{id}', array('as' => 'ProductUpdate', 'uses' => 'ProductsController@update');
Route::delete('products/{id}', array('as' => 'ProductDelete', 'uses' => 'ProductsController@destroy');
```

## Nested Routes

There are two main options for handling nested routes. You can either nest your controllers
in sub-folders as well, or you can refer directly to the main controllers folder

#### Nested Controllers
If you have nested controllers, then Restful can handle that too.
```php
Restful::make('products.categories', 'products_CategoriesController');
```
Note here that the underscore represents a directory seperator, so we would expect the following:
```
// app/controllers/products/CategoriesController.php
class Products_CategoriesController extends SiteController
{
	...
}
```

This would create the following restful routes for you:
```php
Route::get('products/{product_id}/categories/{id}/edit', array('as' => 'ProductCategoryEdit', 'uses' => 'products.CategoriesController@edit');
Route::get('products/{product_id}/categories/add', array('as' => 'ProductCommentAdd', 'uses' => 'products.CategoriesController@create');
Route::get('products/{product_id}/categories/{id}', array('as' => 'ProductCategory', 'uses' => 'products.CategoriesController@show');
Route::get('products/{product_id}/categories', array('as' => 'ProductCategories', 'uses' => 'products.CategoriesController@index');
Route::post('products/{product_id}/categories', array('as' => 'ProductCategoryAdd', 'uses' => 'products.CategoriesController@store');
Route::put('products/{product_id}/categories/{id}', array('as' => 'ProductCategoryUpdate', 'uses' => 'products.CategoriesController@update');
Route::delete('products/{product_id}/categories/{id}', array('as' => 'ProductCategoryDelete', 'uses' => 'products.CategoriesController@destroy');
```

#### Controllers in root controllers directory
You can, of course, still have a nested route that routes to the main controllers folder if you wish:
```php
Restful::make('products.categories', 'CategoriesController');

// app/controllers/CategoriesController.php
class CategoriesController extends SiteController
{
	...
}
```

This would create the following restful routes for you:
```php
Route::get('products/{product_id}/categories/{id}/edit', array('as' => 'ProductCategoryEdit', 'uses' => 'CategoriesController@edit');
Route::get('products/{product_id}/categories/add', array('as' => 'ProductCommentAdd', 'uses' => 'CategoriesController@create');
Route::get('products/{product_id}/categories/{id}', array('as' => 'ProductCategory', 'uses' => 'CategoriesController@show');
Route::get('products/{product_id}/categories', array('as' => 'ProductCategories', 'uses' => 'CategoriesController@index');
Route::post('products/{product_id}/categories', array('as' => 'ProductCategoryAdd', 'uses' => 'CategoriesController@store');
Route::put('products/{product_id}/categories/{id}', array('as' => 'ProductCategoryUpdate', 'uses' => 'CategoriesController@update');
Route::delete('products/{product_id}/categories/{id}', array('as' => 'ProductCategoryDelete', 'uses' => 'CategoriesController@destroy');
```

## Changing the route template
Restful Routes uses a default template to create the above routes, but you
can easily override this template to create your own routes by passing this
as the third parameter.
```
Restful::make('products', 'ProductsController', $myTemplate);
