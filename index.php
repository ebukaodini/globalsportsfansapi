<?php

namespace App;

use Controllers\Admin;
use Controllers\Member;
use Controllers\UserController;
use Library\Http\Request;
use Library\Http\Router;
use Middlewares\JWT;
use Middlewares\Guard;
use Models\OrganisationInfo;
use Models\ReferralLevels;
use Models\Slots;
use Services\Cipher;
use Services\Common;
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

Router::get('/api/organisationinfo', function(Request $req) {
   success('success', OrganisationInfo::findOne("about_us, disclaimer, how_it_works, terms_and_condition, membership, rewards_and_benefits, tournaments_and_leagues, contact_telephone, contact_address, contact_email, faq") ?: []);
});

Router::get('/api/slots', function(Request $req) {
   $slots = Slots::findAll("id, program, no_slots, cost, benefits");
   if ($slots == false) error("No slots", null, 200);
   else success('success', $slots);
});

Router::get('/api/referrallevels', function(Request $req) {
   $referrallevels = ReferralLevels::findAll("id, referrals_required, rank, cash_benefit, benefits");
   if ($referrallevels == false) error("No referral levels", null, 200);
   else success('success', $referrallevels);
});

Router::post('/api/register', function(Request $req) {
   UserController::register($req);
})->name('register');

Router::post('/api/login', function(Request $req) {
   UserController::login($req);
})->name('login');

// Authenticated Routes

Router::post('/api/sendtoken', function(Request $req) {
   JWT::auth($req);
   Guard::isAny(['member', 'admin']);
   UserController::sendToken($req);
});

Router::post('/api/verifytoken', function(Request $req) {
   JWT::auth($req);
   Guard::isAny(['member', 'admin']);
   UserController::verifyToken($req);
});

Router::post('/api/profile/update-bio', function(Request $req) {
   JWT::auth($req);
   Guard::isAny(['member', 'admin']);
   UserController::updateBio($req);
});

Router::post('/api/profile/update-bank-details', function(Request $req) {
   JWT::auth($req);
   Guard::isAny(['member', 'admin']);
   UserController::updateBankDetails($req);
});

Router::post('/api/profile/update-password', function(Request $req) {
   JWT::auth($req);
   Guard::isAny(['member', 'admin']);
   UserController::updatePassword($req);
});

Router::post('/api/profile/update-profile-picture', function(Request $req) {
   JWT::auth($req);
   Guard::isAny(['member', 'admin']);
   UserController::updateProfilePicture($req);
});

Router::get('/api/profile', function(Request $req) {
   JWT::auth($req);
   Guard::isAny(['member', 'admin']);
   UserController::profile($req);
});

Router::post('/api/choose-slot', function(Request $req) {
   JWT::auth($req);
   Guard::isAny(['member','admin']);
   Member::chooseSlot($req);
});

// ?????
Router::get('/api/dashboard', function(Request $req) {
   JWT::auth($req);
   Guard::has('dashboard');
   Member::dashboard($req);
});

Router::get('/api/myslots', function(Request $req) {
   JWT::auth($req);
   Guard::isAny(['member','admin']);
   Member::getMySlots($req);
});

Router::get('/api/unpaid-invoice', function(Request $req) {
   JWT::auth($req);
   Guard::isAny(['member','admin']);
   Member::getUnpaidInvoices($req);
});

Router::post('/api/submit-payment-details', function(Request $req) {
   JWT::auth($req);
   Guard::isAny(['member', 'admin']);
   Member::submitPaymentDetails($req);
});

Router::get('/api/get-downlines', function(Request $req) {
   JWT::auth($req);
   Guard::isAny(['admin','member']);
   Member::getDownlines($req);
});

Router::get('/api/my-benefits', function(Request $req) {
   JWT::auth($req);
   Guard::isAny(['admin', 'member']);
   Member::myBenefits($req);
});


// Admin
Router::post('/api/admin/register', function(Request $req) {
   JWT::auth($req);
   // Guard::is('admin');
   UserController::registerAdmin($req);
});


Router::post('/api/admin/register', function(Request $req) {
   JWT::auth($req);
   // Guard::is('admin');
   Admin::register($req);
});

Router::get('/api/admin/all-users', function(Request $req) {
   JWT::auth($req);
   // Guard::is('admin');
   Admin::getAllUsers($req);
});

Router::get('/api/admin/all-admin', function(Request $req) {
   JWT::auth($req);
   // Guard::is('admin');
   Admin::getAllAdmin($req);
});

Router::post('/api/admin/update-user-status', function(Request $req) {
   JWT::auth($req);
   // Guard::is('admin');
   Admin::updateUserStatus($req);
});

Router::get('/api/admin/user-downlines', function(Request $req) {
   JWT::auth($req);
   // Guard::is('admin');
   Admin::getUserDownlines($req);
});

Router::get('/api/admin/user-benefits', function(Request $req) {
   JWT::auth($req);
   // Guard::is('admin');
   Admin::getUserBenefits($req);
});

Router::get('/api/admin/all-benefits', function(Request $req) {
   JWT::auth($req);
   // Guard::is('admin');
   Admin::getAllBenefits($req);
});

Router::post('/api/admin/update-benefit-status', function(Request $req) {
   JWT::auth($req);
   // Guard::is('admin');
   Admin::updateUserBenefitStatus($req);
});

Router::get('/api/admin/get-all-invoices', function(Request $req) {
   JWT::auth($req);
   // Guard::is('admin');
   Admin::getAllInvoice($req);
});

Router::get('/api/admin/get-paid-invoices', function(Request $req) {
   JWT::auth($req);
   // Guard::is('admin');
   Admin::getAllPaidInvoice($req);
});

Router::get('/api/admin/get-unpaid-invoices', function(Request $req) {
   JWT::auth($req);
   // Guard::is('admin');
   Admin::getAllUnpaidInvoice($req);
});

Router::post('/api/verify-payment', function(Request $req) {
   JWT::auth($req);
   // Guard::is('admin');
   Admin::verifyPayment($req);
});

// Testing group authentication
// JWT::groupAuth($req, function(Request $req) {
JWT::groupAuth($req, function() {
   
   Router::get('/test-group-auth/{name}', function(Request $req) {
      success('Test is successful; Name: ' . $req->routeParams['name']);
   });
    
});