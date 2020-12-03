<?php

namespace App;

use Controllers\User;
use Library\Http\Request;
use Library\Http\Router;

// autoloader
include_once 'vendor/autoload.php';

// Handle Request
$req = new Request();

// Start Router
Router::start($req);

Router::get('/', function () {
   render('welcome.html', ['org_name' => APP_NAME]);
})->name('home');

Router::post('/api/register', function(Request $req) {
   User::create($req);
})->name('register');
