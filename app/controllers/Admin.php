<?php
namespace Controllers;
use Library\Http\Request;
use Models\Invoice;
use Models\OrganisationInfo;
use Models\Users;
use Services\Cipher;
use Services\User;
use Services\Validate;
use Models\ReferralLevels;
use Models\UserBenefits;
use Services\Common;

class Admin
{

   public static function getAllInvoice(Request $req)
   {
      $allInvoice = Invoice::findAll("*");
      if ($allInvoice == false) error("No invoice", null, 200);
      else success('All invoice', $allInvoice);
   }

   public static function getAllPaidInvoice(Request $req)
   {
      $allInvoice = Invoice::findAll("*", "WHERE status = 'paid'");
      if ($allInvoice == false) error("No paid invoice", null, 200);
      else success('All paid invoice', $allInvoice);
   }

   public static function getAllUnpaidInvoice(Request $req)
   {
      $allInvoice = Invoice::findAll("*", "WHERE status = 'unpaid'");
      if ($allInvoice == false) error("No unpaid invoice", null, 200);
      else success('All unpaid invoice', $allInvoice);
   }

   public static function getAllUsers(Request $req)
   {
      $allusers = Users::findAll("id, telephone, email, role, permissions, firstname, lastname, middlename, residential_address, occupation, profile_picture, mou, nextofkin_name, nextofkin_telephone, nextofkin_residential_address, accountnumber, accountname, bankname, favorite_sport, favorite_team, referredby, referral_code, referral_level, member_id, verification_status, created_at, updated_at", "WHERE role = 'member'");
      if ($allusers) success("All users", $allusers);
      else error("No user", null, 200);
   }

   public static function getAllAdmin(Request $req)
   {
      $allusers = Users::findAll("id, telephone, email, role, permissions, firstname, lastname, middlename, residential_address, occupation, profile_picture, mou, nextofkin_name, nextofkin_telephone, nextofkin_residential_address, accountnumber, accountname, bankname, favorite_sport, favorite_team, referredby, referral_code, referral_level, member_id, verification_status, created_at, updated_at", "WHERE role = 'admin'");
      if ($allusers) success("All admin", $allusers);
      else error("No admin", null, 200);
   }

   public static function verifyPayment(Request $req)
   {
      extract($req->body);
      $invoicenumber = $invoicenumber ?? '';
      $status = $status ?? '';

      // Validate
      Validate::isNotEmpty('Invoice number', $invoicenumber);
      Validate::hasMaxLength('Invoice number', $invoicenumber, 10);
      Validate::mustContainNumberOnly('Invoice number', $invoicenumber);
      Validate::isNotEmpty('Status', $status);
      Validate::hasMaxLength('Status', $status, 20);

      $updatestatus = Invoice::update([
         "status" => $status
      ], "WHERE invoice_number = '$invoicenumber'");

      if ($updatestatus) {
         
         if ($status == 'paid') {
            // generate and update the members id
            $memberId = Cipher::token(10);
   
            while (Users::exist("WHERE member_id = '$memberId'") == true) {
               $memberId = Cipher::token(10);
            }

            // get the id of the member whose invoice was updated
            $memberUserId = Invoice::findOne("user_id", "WHERE invoice_number = '$invoicenumber'")['user_id'];
            // then update
            if ($memberUserId != false) {
               Users::update([
                  "member_id" => $memberId
               ], "WHERE id = $memberUserId");
            }

            // TODO: give the user who made payment his accrued benefits
            $currentReferralLevel = 1;

            $referralLevel = ReferralLevels::findOne("cash_benefit, benefits, rank", "WHERE id = $currentReferralLevel");
            UserBenefits::create([
               "user_id" => $memberUserId,
               "achievement" => "Attained " . $referralLevel['rank'] . " Level.",
               "cash" => $referralLevel['cash_benefit'],
               "benefit" => $referralLevel['benefits']
            ]);

            // TODO: notify the user of his new benefits

         }

         // TODO: notify member
         success('Invoice status updated successfully');
      } else error('Invoice status not updated', null, 200);

   }

   public static function updateUserStatus(Request $req)
   {
      extract($req->body);
      $status = $status ?? '';
      $userId = $userId ?? '';

      Validate::isNotEmpty('Status', $status);
      Validate::mustContainLetters('Status', $status);
      Validate::isNotEmpty('User ID', $userId);
      Validate::mustContainNumberOnly('User ID', $userId);

      if (Validate::$status == false) error("User status could not be updated", array_values(Validate::$error));

      if (Users::update([
         "verification_status" => $status
      ], "WHERE id = $userId")== true) {
         success("User status updated successfully");
      } else error("User status could not be updated; Please try again");
   }

   public static function getUserDownlines(Request $req)
   {
      extract($req->query);
      $email = $email ?? '';

      // Validate email
      Validate::isValidEmail('Email', $email);
      Validate::hasMaxLength('Email', $email, 100);

      if (Validate::$status == false) error("Could not get user's downlines", array_values(Validate::$error));

      $my = Users::findOne("referral_code, referral_level", "WHERE email = '$email'");
      $referralCode = $my['referral_code'] ?? null;
      $referralLevel = intval($my['referral_level']) ?? null;
      
      if (!is_null($referralCode) && !is_null($referralLevel)) {
         $downlines = Common::generateDownline($referralCode, $referralLevel, 1);

         success("User downlines", $downlines);

      } else error("User do not have a referral code, contact the support team.", null, 200);
   }

   public static function getUserBenefits(Request $req)
   {
      extract($req->query);
      $userId = $userId ?? '';

      Validate::isNotEmpty('User ID', $userId);
      Validate::mustContainNumberOnly('User ID', $userId);

      if (Validate::$status == false) error("User benefits could not be gotten", array_values(Validate::$error));

      $benefits = UserBenefits::findAll("id, achievement, cash, benefit, status, created_at, updated_at", "WHERE user_id = $userId");
      
      if ($benefits != false) success('User accrued benefits', $benefits); else error('No accrued benefits', null, 200);
   }

   public static function getAllBenefits(Request $req)
   {
      $benefits = UserBenefits::findAll("id, user_id, achievement, cash, benefit, status, created_at, updated_at");

      if ($benefits !== false) success('All benefits', $benefits); else error('No benefits');
   }
   
   public static function updateUserBenefitStatus(Request $req)
   {
      extract($req->body);
      $status = $status ?? '';
      $id = $id ?? '';

      Validate::isNotEmpty('Status', $status);
      Validate::mustContainLetters('Status', $status);
      Validate::isNotEmpty('ID', $id);
      Validate::mustContainNumberOnly('ID', $id);

      if (Validate::$status == false) error("User benefits could not be updated", array_values(Validate::$error));

      if (UserBenefits::update([
         "status" => $status
      ], "WHERE id = $id") == true) success("User benefit status is updated"); else error("User benefit status is not updated");

   }

   
   public static function updateOrganisationInfo(Request $req)
   {
      extract($req->body);
      $aboutUs = $aboutUs ?? '';
      $disclaimer = $disclaimer ?? '';
      $howItWorks = $howItWorks ?? '';
      $termsAndCondition = $termsAndCondition ?? '';
      $mou = $mou ?? '';
      $membership = $membership ?? '';
      $rewardsAndBenefits = $rewardsAndBenefits ?? '';
      $tournamentAndLeagues = $tournamentAndLeagues ?? '';
      $contactTelephone = $contactTelephone ?? '';
      $contactAddress = $contactAddress ?? '';
      $contactEmail = $contactEmail ?? '';
      $faq = $faq ?? '';

      // validation
      Validate::isNotEmpty('About Us', $aboutUs);
      Validate::isNotEmpty('Disclaimer', $disclaimer);
      Validate::isNotEmpty('How it works', $howItWorks);
      Validate::isNotEmpty('Terms and Condition', $termsAndCondition);
      Validate::isNotEmpty('Memorandum of Understanding', $mou);
      Validate::isNotEmpty('Membership', $membership);
      Validate::isNotEmpty('Rewards and Benefits', $rewardsAndBenefits);
      Validate::isNotEmpty('Tournament and Leagues', $tournamentAndLeagues);
      Validate::isNotEmpty('Contact Telephone', $contactTelephone);
      Validate::isNotEmpty('Contact Address', $contactAddress);
      Validate::isNotEmpty('Contact Email', $contactEmail);
      Validate::isNotEmpty('Frequently Asked Question', $faq);

      Validate::isValidTelephone('Contact Telephone', $contactTelephone);
      Validate::hasMaxLength('Contact Telephone', $contactTelephone, 50);
      Validate::hasMaxLength('Contact Address', $contactAddress, 200);
      Validate::isValidEmail('Contact Email', $contactEmail);
      Validate::hasMaxLength('Contact Email', $contactEmail, 100);

      if (Validate::$status == false) {
         error('Info not updated', Validate::$error);
      }

      if (OrganisationInfo::update([
         "about_us" => $aboutUs,
         "disclaimer" => $disclaimer,
         "how_it_works" => $howItWorks,
         "terms_and_condition" => $termsAndCondition,
         "mou" => $mou,
         "membership" => $membership,
         "rewards_and_benefits" => $rewardsAndBenefits,
         "tournaments_and_leagues" => $tournamentAndLeagues,
         "contact_telephone" => $contactTelephone,
         "contact_address" => $contactAddress,
         "contact_email" => $contactEmail,
         "faq" => $faq,
      ], "WHERE id = 1") == true) {
         success('Info is updated');
      } else {
         error("Info is not updated");
      }
   }
   
}
