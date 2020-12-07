<?php

namespace App;

use Controllers\Member;
use Controllers\Auth;
use Library\Http\Request;
use Library\Http\Router;
use Middlewares\JWT;
use Middlewares\Guard;
use Models\OrganisationInfo;
use Models\Slots;
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

Router::get('/organisationinfo', function(Request $req) {
   success('success', OrganisationInfo::findOne("about_us, disclaimer, how_it_works, terms_and_condition, membership, rewards_and_benefits, tournaments_and_leagues, contact_telephone, contact_address, contact_email, faq") ?: []);
});

Router::get('/slots', function(Request $req) {
   $slots = Slots::findAll("id, program, no_slots, cost, benefits");
   if ($slots == false) error("No slots", null, 200);
   else success('success', $slots);
});

// Admin


// Members

Router::get('/api/dashboard', function(Request $req) {
   JWT::auth($req);
   Guard::has('dashboard');
   Member::dashboard($req);
});

Router::post('/api/choose-slot', function(Request $req) {
   JWT::auth($req);
   Guard::isAny(['member','admin']);
   Member::chooseSlot($req);
});
