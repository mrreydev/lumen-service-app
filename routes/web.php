<?php

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

$router->get('/', 'HomeController@index');

$router->get('/users', 'UserController@index');

$router->get('/users/{id:[0-9]+}', 'UserController@show');

$router->get('/hello-lumen', function () {
    return "<h1>Lumen</h1><p>Hi I,m Rey, right now using this cool framework</p>";
});

// authentication
$router->group(['prefix' => 'auth'], function() use($router){
    $router->post('/register', 'AuthController@register');
    $router->post('/login', 'AuthController@login');
    $router->post('/logout', 'AuthController@logout');
});

$router->group(['middleware' => ['auth']], function($router) {
    /**
     * Route Posts
     */
    $router->get('/categories', 'CategoryController@index');
    $router->get('/categories/{id}', 'CategoryController@show');
    $router->post('/categories', 'CategoryController@store');
    $router->put('/categories/{id}', 'CategoryController@update');
    $router->delete('/categories/{id}', 'CategoryController@delete');

    /**
     * Route Posts
     */
    $router->get('/posts', 'PostsController@index');
    $router->get('/posts/{postId}', 'PostsController@show');
    $router->post('/posts', 'PostsController@store');
    $router->put('/posts/{postId}', 'PostsController@update');
    $router->delete('/posts/{postId}', 'PostsController@delete');

    /**
     * Route Comments
     */
    $router->get('/comments', 'CommentController@index');
    $router->get('/comments/{id}', 'CommentController@show');
    $router->post('/posts/{id}/comment', 'CommentController@store');
    $router->get('/posts/{id}/comment', 'CommentController@getPostComments');
    /**
     * Route Majors
     */
    $router->get('/majors', 'MajorController@index');
    $router->post('/majors', 'MajorController@create');
    $router->patch('/majors/{id}', 'MajorController@update');
    $router->delete('/majors/{id}', 'MajorController@delete');
    
    /**
     * Route Professors
     */
    $router->get('/professors', 'ProfessorController@index');
    $router->post('/professors', 'ProfessorController@create');
    $router->get('/professors/{id}', 'ProfessorController@show');
    $router->patch('/professors/{id}', 'ProfessorController@update');
    $router->delete('/professors/{id}', 'ProfessorController@delete');
    
    /**
     * Route Students
     */
    $router->get('/students', 'StudentController@index');
    $router->post('/students', 'StudentController@create');
    $router->get('/students/{id}', 'StudentController@show');
    $router->patch('/students/{id}', 'StudentController@update');
    $router->delete('/students/{id}', 'StudentController@delete');
    
    /**
     * Route Subjects
     */
    $router->get('/subjects', 'SubjectController@index');
    $router->post('/subjects', 'SubjectController@create');
    $router->get('/subjects/{id}', 'SubjectController@show');
    $router->patch('/subjects/{id}', 'SubjectController@update');
    $router->delete('/subjects/{id}', 'SubjectController@delete');
    
    /**
     * Route Scores
     */
    $router->get('/scores', 'ScoreController@index');
    $router->post('/scores', 'ScoreController@create');
    $router->get('/scores/{id}', 'ScoreController@show');
    $router->patch('/scores/{id}', 'ScoreController@update');
    $router->delete('/scores/{id}', 'ScoreController@delete');
});


$router->group(['prefix' => 'public'], function() use($router){
    $router->get('/posts', 'Public\PostsController@index');
    $router->get('/posts/{postId}', 'Public\PostsController@show');

    
});

