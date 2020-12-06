<?php
namespace Controllers;
use Library\Http\Request;
use Services\Validate;
use Services\Cipher;
use Models\Users;
use Models\UserSlots;
use Services\Common;
use Services\JWT;
use Services\User;
use Services\Mail;

class Auth
{
   // create a new user
   public static function register(Request $req)
   {
      extract($req->body);
      $email = $email ?? '';
      $password = $password ?? '';
      $cpassword = $cpassword ?? '';
      $referralcode = $referralcode ?? '';
      // validate email
      Validate::isValidEmail('Email', $email);
      Validate::hasMaxLength('Email', $email, 100);
      // validate password
      Validate::isValidPassword('Password', $password, true, true, true, false, 8);
      // validate the referral code if it is not empty
      if (empty($referralcode) == false) {
         Validate::hasExactLength('Referral code', $referralcode, 7);
      }
      // verify validation
      if (Validate::$status == false) {
         error('Registeration failed', array_values(Validate::$error));
      }
      if ($password != $cpassword) {
         error('Registeration failed', ['Password and confirm password must be the same']);
      }

      // hash password
      $hpassword = Cipher::hashPassword($password);

      // Check if user exists
      if (Users::exist("WHERE email = '$email'") == true) {
         error('Registeration failed. Email already exists');
      }

      // if referral code is not empty
      // confirm that the referral code exist before using it
      if (!empty($referralcode)) {
         if (UserSlots::exist("WHERE referral_code = '$referralcode'") == false) {
            error("Registeration failed. Referral code is incorrect");
         }
      }

      if (Users::create([
         "email" => $email,
         "password" => $hpassword,
         "referredby" => $referralcode
      ]) == true) {
         success('Registeration successful');
      } else {
         error('Registeration failed, please try again');
      }

   }

   public static function login(Request $req)
   {
      extract($req->body);
      $email = $email ?? '';
      $password = $password ?? '';
      // validate email and password
      if ( Validate::isValidEmail('Email', $email) == false || Validate::isValidPassword('Password', $password, true, true, true, false, 8) == false) {
         error("Invalid Email / Password");
      }

      // get user with this email
      $user = Users::findOne("*", "WHERE email = '$email'");
      if (!$user == false) {
         // verify users password_get_info
         if (Cipher::verifyPassword($password, $user['password']) == true) {

            // generate token payload
            $payload = [
               'email' => $user['email'],
               'role' => $user['role'],
               'permissions' => $user['permissions']
            ];

            // create a jwt token for the user
            $token = JWT::encode($payload, APP_KEY, 'HS256');

            // success response
            success('Login successful', [
               'token' => $token,
               'role' => $user['role']
            ]);

         } else {
            error("Invalid Email / Password");   
         }
      } else {
         error("Invalid Email / Password");
      }
   }

   public static function sendToken(Request $req)
   {
      extract($req->body);
      $email = User::$email;
      $token = Cipher::token(5); // generate token for user

      // update the user's token
      Users::update([
         "token" => $token
      ], "WHERE email = '$email'");

      $sendMail = Mail::asText("
      Your authentication token is $token
      ")->send(ORG_EMAIL, $email, 'Authentication Token', 'reply');

      if ($sendMail == true) {
         success("A 5 digit token has been sent to $email");
      } else {
         error("Token was not sent. Please logout and login again");
      }
   }

   public static function verifyToken(Request $req)
   {
      extract($req->body);
      $token = $token ?? '';
      $email = User::$email;

      // validate the token
      Validate::mustContainNumberOnly('Token', $token);
      Validate::hasExactLength('Token', $token, 5);
      if (Validate::$status == false) {
         error("Invalid token");
      }

      if (Users::exist("WHERE email = '$email' AND ". DB_PREFIX ."users.token = '$token'")) {
         success('Valid token');
      } else {
         error('Invalid token');
      }
   }

   public static function delete(Request $req)
   {
      // remove a resouce
   }

}
