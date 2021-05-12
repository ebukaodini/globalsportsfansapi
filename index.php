<?php

namespace App;

use Library\Http\Request;
use Library\Http\Router;
use Controllers\Admin;
use Controllers\Member;
use Controllers\UserController;
use Middlewares\JWT;
use Middlewares\Guard;
use Models\Competitions;
use Models\CountryLeagues;
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

// Public Routes

Router::get('/', function () {
   render('welcome.html', ['org_name' => APP_NAME]);
})->name('home');

Router::get('/api/organisationinfo', function (Request $req) {
   success('success', OrganisationInfo::findOne("about_us, disclaimer, how_it_works, terms_and_condition, membership, rewards_and_benefits, tournaments_and_leagues, contact_telephone, contact_address, contact_email, faq") ?: []);
});

Router::get('/api/slots', function (Request $req) {
   $slots = Slots::findAll("id, program, no_slots, cost, benefits");
   if ($slots == false) error("No slots", null, 200);
   else success('success', $slots);
});

Router::get('/api/referrallevels', function (Request $req) {
   $referrallevels = ReferralLevels::findAll("id, referrals_required, rank, cash_benefit, benefits");
   if ($referrallevels == false) error("No referral levels", null, 200);
   else success('success', $referrallevels);
});

Router::post('/api/contactus', function (Request $req) {
   Admin::contactSupport($req);
});

Router::post('/api/forgotpassword', function (Request $req) {
   UserController::forgotPassword($req);
});

Router::post('/api/forgotpassword/verify', function (Request $req) {
   UserController::verifyForgotToken($req);
});

Router::post('/api/forgotpassword/reset', function (Request $req) {
   UserController::resetPassword($req);
});

Router::post('/api/register', function (Request $req) {
   UserController::register($req);
})->name('register');

Router::post('/api/login', function (Request $req) {
   UserController::login($req);
})->name('login');

// Authenticated Routes

Router::post('/api/re-authenticate', function (Request $req) {
   JWT::auth($req);
   Guard::isAny(['member', 'admin']);
   UserController::reAuthenticate($req);
});

Router::post('/api/sendtoken', function (Request $req) {
   JWT::auth($req);
   Guard::isAny(['member', 'admin']);
   UserController::sendToken($req);
});

Router::post('/api/verifytoken', function (Request $req) {
   JWT::auth($req);
   Guard::isAny(['member', 'admin']);
   UserController::verifyToken($req);
});

Router::post('/api/profile/update-bio', function (Request $req) {
   JWT::auth($req);
   Guard::isAny(['member', 'admin']);
   UserController::updateBio($req);
});

Router::post('/api/profile/update-bank-details', function (Request $req) {
   JWT::auth($req);
   Guard::isAny(['member', 'admin']);
   UserController::updateBankDetails($req);
});

Router::post('/api/profile/update-password', function (Request $req) {
   JWT::auth($req);
   Guard::isAny(['member', 'admin']);
   UserController::updatePassword($req);
});

Router::post('/api/profile/update-profile-picture', function (Request $req) {
   JWT::auth($req);
   Guard::isAny(['member', 'admin']);
   UserController::updateProfilePicture($req);
});

Router::get('/api/profile', function (Request $req) {
   JWT::auth($req);
   Guard::isAny(['member', 'admin']);
   UserController::profile($req);
});

Router::post('/api/auto-choose-slot', function (Request $req) {
   Member::chooseSlot($req);
});

Router::post('/api/choose-slot', function (Request $req) {
   JWT::auth($req);
   Guard::isAny(['member', 'admin']);
   Member::chooseSlot($req);
});

Router::get('/api/dashboard', function (Request $req) {
   JWT::auth($req);
   Guard::isAny(['member', 'admin']);
   Member::dashboard($req);
});

Router::get('/api/mypackage', function (Request $req) {
   JWT::auth($req);
   Guard::isAny(['member', 'admin']);
   Member::getMyPackage($req);
});

Router::get('/api/myslots', function (Request $req) {
   JWT::auth($req);
   Guard::isAny(['member', 'admin']);
   Member::getMySlots($req);
});

Router::get('/api/invoice/unpaid', function (Request $req) {
   JWT::auth($req);
   Guard::isAny(['member', 'admin']);
   Member::getUnpaidInvoice($req);
});

Router::post('/api/payment/initiate', function (Request $req) {
   JWT::auth($req);
   Guard::isAny(['member', 'admin']);
   Member::initiatePayment($req);
});

Router::post('/api/payment/verify', function (Request $req) {
   JWT::auth($req);
   Guard::isAny(['member', 'admin']);
   Member::verifyPayment($req);
});

Router::post('/api/submit-payment-details', function (Request $req) {
   JWT::auth($req);
   Guard::isAny(['member', 'admin']);
   Member::submitPaymentDetails($req);
});

Router::get('/api/get-downlines', function (Request $req) {
   JWT::auth($req);
   Guard::isAny(['admin', 'member']);
   Member::getDownlines($req);
});

Router::get('/api/my-benefits', function (Request $req) {
   JWT::auth($req);
   Guard::isAny(['admin', 'member']);
   Member::myBenefits($req);
});

Router::get('/api/unread-notifications', function (Request $req) {
   JWT::auth($req);
   Guard::isAny(['admin', 'member']);
   Member::getUnreadNotification($req);
});

Router::post('/api/mark-as-read', function (Request $req) {
   JWT::auth($req);
   Guard::isAny(['admin', 'member']);
   Member::markNotificationAsRead($req);
});

Router::post('/api/sendinvite', function (Request $req) {
   JWT::auth($req);
   Guard::isAny(['admin', 'member']);
   Member::sendEmailInvite($req);
});

Router::post('/api/querybenefits', function (Request $req) {
   JWT::auth($req);
   Guard::isAny(['admin', 'member']);
   Member::queryAccruedBenefit($req);
});

Router::get('/api/getcountries', function (Request $req) {
   JWT::auth($req);
   Guard::isAny(['admin', 'member']);

   $countries = CountryLeagues::findAll("id, country, continent, updated_at");
   if ($countries) success('success', $countries); else error();
});

Router::get('/api/getcompetitions', function (Request $req) {
   JWT::auth($req);
   Guard::isAny(['admin', 'member']);

   $competitions = Competitions::findAll();
   if ($competitions) success('success', $competitions); else error();
});

// Admin
Router::post('/api/admin/register', function (Request $req) {
   JWT::auth($req);
   Guard::is('admin');
   UserController::registerAdmin($req);
});

Router::post('/api/admin/member-benefits', function (Request $req) {
   JWT::auth($req);
   Guard::is('admin');
   Admin::memberBenefits($req);
});

Router::get('/api/admin/all-benefits', function (Request $req) {
   JWT::auth($req);
   Guard::is('admin');
   Admin::allBenefits($req);
});

Router::post('/api/admin/givebenefit', function (Request $req) {
   JWT::auth($req);
   Guard::is('admin');
   Admin::giveBenefit($req);
});

Router::post('/api/admin/cancelbenefit', function (Request $req) {
   JWT::auth($req);
   Guard::is('admin');
   Admin::cancelBenefit($req);
});

Router::post('/api/admin/memberpackage', function (Request $req) {
   JWT::auth($req);
   Guard::is('admin');
   Admin::getMemberPackage($req);
});

Router::post('/api/admin/users/slots', function (Request $req) {
   JWT::auth($req);
   Guard::is('admin');
   Admin::getMemeberSlots($req);
});

Router::get('/api/unread-admin-notifications', function (Request $req) {
   JWT::auth($req);
   Guard::is('admin');
   Admin::getUnreadNotification($req);
});

Router::post('/api/queryinvoice', function (Request $req) {
   JWT::auth($req);
   Guard::is('admin');
   Admin::queryUnpaidInvoice($req);
});

Router::get('/api/admin/all-users', function (Request $req) {
   JWT::auth($req);
   Guard::is('admin');
   Admin::getAllUsers($req);
});

Router::post('/api/admin/users/profile', function (Request $req) {
   JWT::auth($req);
   Guard::is('admin');
   Admin::getProfile($req);
});

Router::post('/api/admin/update-status', function (Request $req) {
   JWT::auth($req);
   Guard::is('admin');
   Admin::updateUserStatus($req);
});

Router::post('/api/admin/update-privileges', function (Request $req) {
   JWT::auth($req);
   Guard::is('admin');
   Admin::updateUserPrivileges($req);
});

Router::post('/api/admin/send-notice', function (Request $req) {
   JWT::auth($req);
   Guard::is('admin');
   Admin::sendNotice($req);
});

Router::post('/api/admin/send-group-notice', function (Request $req) {
   JWT::auth($req);
   Guard::is('admin');
   Admin::sendGroupNotice($req);
});

Router::get('/api/admin/user-downlines', function (Request $req) {
   JWT::auth($req);
   Guard::is('admin');
   Admin::getUserDownlines($req);
});

Router::get('/api/admin/org-downlines', function (Request $req) {
   JWT::auth($req);
   Guard::is('admin');
   Admin::getOrgDownlines($req);
});

Router::get('/api/admin/user-benefits', function (Request $req) {
   JWT::auth($req);
   Guard::is('admin');
   Admin::getUserBenefits($req);
});

Router::get('/api/admin/all-benefits', function (Request $req) {
   JWT::auth($req);
   Guard::is('admin');
   Admin::getAllBenefits($req);
});

Router::post('/api/admin/update-benefit-status', function (Request $req) {
   JWT::auth($req);
   Guard::is('admin');
   Admin::updateUserBenefitStatus($req);
});

Router::get('/api/admin/get-invoices', function (Request $req) {
   JWT::auth($req);
   Guard::is('admin');
   Admin::getAllInvoice($req);
});

// Router::get('/api/admin/get-paid-invoices', function (Request $req) {
//    JWT::auth($req);
//    Guard::is('admin');
//    Admin::getAllPaidInvoice($req);
// });

// Router::get('/api/admin/get-unpaid-invoices', function (Request $req) {
//    JWT::auth($req);
//    Guard::is('admin');
//    Admin::getAllUnpaidInvoice($req);
// });

Router::get('/api/admin/payments', function (Request $req) {
   JWT::auth($req);
   Guard::is('admin');
   Admin::getPayments($req);
});

Router::post('/api/admin/verify-payment', function (Request $req) {
   JWT::auth($req);
   Guard::is('admin');
   Admin::verifyPayment($req);
});

Router::post('/api/admin/update-organisation-info', function (Request $req) {
   JWT::auth($req);
   Guard::is('admin');
   Admin::updateOrganisationInfo($req);
});

Router::get('/api/admin/referrallevels', function (Request $req) {
   JWT::auth($req);
   Guard::is('admin');
   Admin::getReferralLevels($req);
});

Router::post('/api/admin/referrallevels/update', function (Request $req) {
   JWT::auth($req);
   Guard::is('admin');
   Admin::updateReferralLevels($req);
});

Router::get('/api/admin/slots', function (Request $req) {
   JWT::auth($req);
   Guard::is('admin');
   Admin::getSlotPackages($req);
});

Router::post('/api/admin/slots/update', function (Request $req) {
   JWT::auth($req);
   Guard::is('admin');
   Admin::updateSlotPackage($req);
});

Router::get('/api/admin/competitions', function (Request $req) {
   JWT::auth($req);
   Guard::is('admin');
   Admin::getCompetitions($req);
});

Router::post('/api/admin/competitions/create', function (Request $req) {
   JWT::auth($req);
   Guard::is('admin');
   Admin::addCompetition($req);
});

Router::post('/api/admin/competitions/update', function (Request $req) {
   JWT::auth($req);
   Guard::is('admin');
   Admin::updateCompetition($req);
});

Router::post('/api/admin/competitions/delete', function (Request $req) {
   JWT::auth($req);
   Guard::is('admin');
   Admin::deleteCompetition($req);
});

// Testing group authentication
// JWT::groupAuth($req, function(Request $req) {
JWT::groupAuth($req, function () {

   Router::get('/test-group-auth/{name}', function (Request $req) {
      success('Test is successful; Name: ' . $req->routeParams['name']);
   });
});
