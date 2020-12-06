<?php

namespace App;

use Controllers\Member;
use Controllers\Auth;
use Library\Http\Request;
use Library\Http\Router;
use Middlewares\JWT;
use Middlewares\Guard;
use Services\User;

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
   Auth::register($req);
})->name('register');

Router::post('/api/login', function(Request $req) {
   Auth::login($req);
})->name('login');

Router::post('/api/sendtoken', function(Request $req) {
   JWT::auth($req);
   Auth::sendToken($req);
});

Router::post('/api/verifytoken', function(Request $req) {
   JWT::auth($req);
   Auth::verifyToken($req);
});

// Member

Router::get('/api/dashboard', function(Request $req) {
   JWT::auth($req);
   Guard::has('dashboard');
   Member::dashboard($req);
});


