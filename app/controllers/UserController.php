<?php
namespace Controllers;
use Library\Http\Request;
use Services\Validate;
use Services\Cipher;
use Models\Users;
use Models\UserSlots;
use Services\Upload;
use Services\JWT;
use Services\User;
use Services\Mail;
use Services\Common;

class UserController
{
   // create a new user
   public static function register(Request $req)
   {
      extract($req->body);
      $email = $email ?? '';
      $password = $password ?? '';
      $cpassword = $cpassword ?? '';
      $referralcode = $referralcode ?? '';
      $slot = $slot ?? '';
      $mou = $mou ?? false;

      // stop user from registering if he/she refuse to agree to the mou
      if ($mou == false) error('Accept the Memorandum of Understanding to continue', null, 200);

      // validate email
      Validate::isValidEmail('Email', $email);
      Validate::hasMaxLength('Email', $email, 100);
      // validate password
      Validate::isValidPassword('Password', $password, true, true, true, false, 8);
      // validate the referral code if it is not empty
      if (empty($referralcode) == false) {
         Validate::hasExactLength('Referral code', $referralcode, 7);
      }
      // validate slot id if it is sent as well
      if (empty($slot) == false) {
         Validate::isInteger('Slot', $slot);
      }
      // verify validation
      if (Validate::$status == false) {
         error('Registeration failed', array_values(Validate::$error), 200);
      }
      if ($password != $cpassword) {
         error('Registeration failed', ['Password and confirm password must be the same'], 200);
      }

      // hash password
      $hpassword = Cipher::hashPassword($password);

      // Check if user exists
      if (Users::exist("WHERE email = '$email'") == true) {
         error('Registeration failed. Email already exists', null, 200);
      }

      // if referral code is not empty
      // confirm that the referral code exist before using it
      if (!empty($referralcode)) {
         if (Users::exist("WHERE referral_code = '$referralcode'") == false) {
            error("Registeration failed. Referral code is incorrect", null, 200);
         }
      } else {
         // else use the referral code of the organisation
         $referralcode = ORG_REFERRAL_CODE;
      }

      $referralCodeUserId;
      if ($referralcode != ORG_REFERRAL_CODE) {
         $referralCodeUserId = Users::findOne("id", "WHERE referral_code = '$referralcode'")['id'];
         // NB: if the users whose referral code is being used has already gotten his initial_referrals_required
         // i.e referrals_acquired >= initial_referrals_required
         // then do not use his referral code, rather use the organisations code
         if (UserSlots::exist("WHERE user_id = $referralCodeUserId AND referrals_acquired >= initial_referrals_required ") == true) $referralcode = ORG_REFERRAL_CODE;
      }

      // get the referral level of the user
      $referrallevel = Users::findOne("referral_level", "WHERE referral_code = '$referralcode'")['referral_level'] ?? '1'; // 1 assuming that this is the topmost parent in the referral tree
      if ($referrallevel == "1") $referralcode = ORG_REFERRAL_CODE; // i assume there should be no need for this as it has already been considered
      // and increment by one to generate referrallevel for this user
      $referrallevel = intval($referrallevel) + 1;

      if (Users::create([
         "email" => $email,
         "password" => $hpassword,
         "referredby" => $referralcode,
         "referral_level" => $referrallevel
      ]) == true) {

         // before returning success
         // increment the referrals acquired for the users slot whose referral code is used to register (i.e if referral code is not ORG_REFERRAL_CODE)
         if ($referralcode != ORG_REFERRAL_CODE) {
            UserSlots::update([
               "referrals_acquired" => intval((UserSlots::findOne("referrals_acquired", "WHERE user_id = $referralCodeUserId")['referrals_acquired'] ?: 0) + 1),
            ], "WHERE user_id = $referralCodeUserId");

            // TODO: TEST
            // update all uplinks with status still active
            // this is going to be a recursive function
            Common::updateAllUplinks($referralcode);
         }

         success('Registeration successful', 
            $slot == '' ? null : ['slot' => $slot]
         );
      } else {
         error('Registeration failed, please try again', null, 200);
      }

   }

   public static function login(Request $req)
   {
      extract($req->body);
      $email = $email ?? '';
      $password = $password ?? '';
      // validate email and password
      if ( Validate::isValidEmail('Email', $email) == false || Validate::isValidPassword('Password', $password, true, true, true, false, 8) == false) {
         error("Invalid Email / Password", null, 200);
      }

      // get user with this email
      $user = Users::findOne("*", "WHERE email = '$email'");
      if (!$user == false) {
         // verify users password_get_info
         if (Cipher::verifyPassword($password, $user['password']) == true) {

            // generate token payload
            $payload = Cipher::encryptAES(APP_KEY, json_encode(
               [
                  'id' => $user['id'],
                  'email' => $user['email'],
                  'role' => $user['role'],
                  'permissions' => $user['permissions']
               ]
            ));

            // create a jwt token for the user
            $token = JWT::encode($payload, APP_KEY, 'HS256');

            // success response
            success('Login successful', [
               'token' => $token,
               'role' => $user['role']
            ]);

         } else {
            error("Invalid Email / Password", null, 200);   
         }
      } else {
         error("Invalid Email / Password", null, 200);
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
         error("Token was not sent. Please logout and login again", null, 200);
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
         error("Invalid token", null, 200);
      }

      if (Users::exist("WHERE email = '$email' AND token = '$token' LIMIT 1")) {
         $statusupdate = Users::update([
            "verification_status" => "verified"
         ], "WHERE email = '$email'");
         if ($statusupdate == true) success('Valid token. Account is verified'); else error("Account not verified", null, 200);
      } else {
         error('Invalid token', null, 200);
      }
   }

   public static function profile(Request $req)
   {
      $email = User::$email;
      // get the users profile details
      $profile = Users::findOne("telephone, email, firstname, lastname, middlename, residential_address, occupation, profile_picture, mou, nextofkin_name, nextofkin_telephone, nextofkin_residential_address, accountnumber, accountname, bankname, favorite_sport, favorite_team, referral_code, member_id, verification_status, created_at", "WHERE email = '$email'");

      if ($profile == false) {
         error("No profile for this user", null, 200); // no obvious chance of this accuring
      } else {
         success('User profile', $profile);
      }
   }

   public static function updatePassword(Request $req)
   {
      extract($req->body);
      $opassword = $opassword ?? '';
      $npassword = $npassword ?? '';
      $cpassword = $cpassword ?? '';
      // exit(json_encode($_POST));

      // Validate the password
      Validate::isValidPassword('New Password', $npassword, true, true, true, false, 8);
      if (Validate::$status == false) {
         error("Password update failed", array_values(Validate::$error), 200);
      }

      if ($npassword != $cpassword) {
         error("Password update failed", ['Password and confirm password must be the same'], 200);
      }

      // verify the old password

      $email = User::$email;
      
      // get the password in the database or use ''
      $dbpassword = Users::findOne("password", "WHERE email = '$email'")['password'] ?: '';
      
      // return error if password fails
      if (Cipher::verifyPassword($opassword, $dbpassword) == false) error("Password update failed", ["Invalid Password"], 200);

      // hash the password
      $hpassword = Cipher::hashPassword($npassword);

      // update the user's password
      $passwordUpdate = Users::update([
         "password" => $hpassword,
      ], "WHERE email = '$email'");

      if ($passwordUpdate) {
         success("Password updated successful");
      } else {
         error("Password update failed");
      }
   }

   public static function updateProfilePicture(Request $req)
   {
      extract($req->body);
      $email = User::$email;

      Upload::$field = 'Profile Picture';
      Upload::$unit = 'Mb';
      Upload::tmp('profile-picture', "profile_pictures/" . User::$id.".".date('ymdhis'), Upload::$imagefiles, null, 2);

      if (Upload::$status == false) {
         error('Profile picture not updated', array_values(Upload::$error), 200);
      } else {
         $pictureupdate = Users::update([
            "profile_picture" => Upload::$path,
         ], "WHERE email = '$email'");
         if ($pictureupdate == true)
         success("Profile picture updated successfully", ['path' => Upload::$path]);
         else
         error("Profile picture not updated", null, 200);
      }

   }

   public static function updateBankDetails(Request $req)
   {
      extract($req->body);
      $accountnumber = $accountnumber ?? '';
      $accountname = $accountname ?? '';
      $bankname = $bankname ?? '';

      // validate the bank details
      if (Validate::mustContainNumberOnly("Account Number", $accountnumber) == false) {
         error('Bank details not updated', ["Invalid Account Number"], 200);
      }
      
      if (empty($accountname) == true || empty($bankname) == true) {
         error("Bank details not updated", ['Account details cannot be empty'], 200);
      }

      $email = User::$email;

      // update the user's bank details
      $bankdetailupdate = Users::update([
         "accountnumber" => $accountnumber,
         "accountname" => $accountname,
         "bankname" => $bankname
      ], "WHERE email = '$email'");

      if ($bankdetailupdate == true) {
         success("Bank details updated successfully");
      } else {
         error("Bank details not updated", null, 200);
      }
   }

   public static function updateBio(Request $req)
   {
      extract($req->body);
      
      $telephone = $telephone ?? '';
      $firstname = $firstname ?? '';
      $lastname = $lastname ?? '';
      $middlename = $middlename ?? '';
      $residential_address = $residential_address ?? '';
      $occupation = $occupation ?? '';
      $nextofkin_name = $nextofkin_name ?? '';
      $nextofkin_telephone = $nextofkin_telephone ?? '';
      $nextofkin_residential_address = $nextofkin_residential_address ?? '';
      $favorite_sport = $favorite_sport ?? '';
      $favorite_team = $favorite_team ?? '';

      // validation
      Validate::isNotEmpty("Telephone", $telephone);
      Validate::isValidTelephone("Telephone", $telephone);
      Validate::hasMaxLength("Telephone", $telephone, 20);
      Validate::isNotEmpty("Firstname", $firstname);
      Validate::hasMaxLength("Firstname", $firstname, 50);
      Validate::isNotEmpty("Lastname", $lastname);
      Validate::hasMaxLength("Lastname", $lastname, 50);
      Validate::isNotEmpty("Middlename", $middlename);
      Validate::hasMaxLength("Middlename", $middlename, 50);
      Validate::isNotEmpty("Residential Address", $residential_address);
      Validate::hasMaxLength("Residential Address", $residential_address, 200);
      Validate::isNotEmpty("Occupation", $occupation);
      Validate::hasMaxLength("Occupation", $occupation, 50);
      Validate::isNotEmpty("Next of Kin Name", $nextofkin_name);
      Validate::hasMaxLength("Next of Kin Name", $nextofkin_name, 100);
      Validate::isNotEmpty("Next of Kin Telephone", $nextofkin_telephone);
      Validate::isValidTelephone("Next of Kin Telephone", $nextofkin_telephone);
      Validate::hasMaxLength("Next of Kin Telephone", $nextofkin_telephone, 20);
      Validate::isNotEmpty("Next of Kin Residential Address", $nextofkin_residential_address);
      Validate::hasMaxLength("Next of Kin Residential Address", $nextofkin_residential_address, 200);
      Validate::isNotEmpty("Favorite Sport", $favorite_sport);
      Validate::hasMaxLength("Favoite Sport", $favorite_sport, 50);
      Validate::isNotEmpty("Favorite Team", $favorite_team);
      Validate::hasMaxLength("Favoite Team", $favorite_team, 50);

      if (Validate::$status == false) {
         error("Profile not updated", Validate::$error, 200);
      }

      $email = User::$email;

      // telephone must be unique
      if (Users::exist("WHERE telephone = '$telephone' AND email <> '$email'") == true) error('Telephone number already exist', null, 200);

      // update the user profile
      $updateprofile = Users::update([
         "telephone" => $telephone,
         "firstname" => $firstname,
         "lastname" => $lastname,
         "middlename" => $middlename,
         "residential_address" => $residential_address,
         "occupation" => $occupation,
         "nextofkin_name" => $nextofkin_name,
         "nextofkin_telephone" => $nextofkin_telephone,
         "nextofkin_residential_address" => $nextofkin_residential_address,
         "favorite_sport" => $favorite_sport,
         "favorite_team" => $favorite_team
      ], "WHERE email = '$email'");
      
      if ($updateprofile == true) {
         success("Profile updated successfully");
      } else {
         error("Profile not updated", null, 200);
      }

   }

}
