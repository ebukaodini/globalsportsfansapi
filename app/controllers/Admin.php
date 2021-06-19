<?php

namespace Controllers;

use Library\Database\Model;
use Library\Http\Request;
use Models\Competitions;
use Models\Invoice;
use Models\Notifications;
use Models\OrganisationInfo;
use Models\Payments;
use Models\ReferralBenefits;
use Models\Users;
use Services\Cipher;
use Services\User;
use Services\Validate;
use Models\ReferralLevels;
use Models\Slots;
use Models\UserBenefits;
use Models\UserPackage;
use Models\UserSlots;
use Services\Common;
use Services\Mail;
use Services\Sanitize;

class Admin
{

   public static function contactSupport(Request $req)
   {
      extract($req->body);

      $messageSent = Mail::asHTML(
         "<h4>
         Name: $name <br />
         Email: $email <br />
         </h4> <br />
         <p>$message</p>"
      )->send(ORG_EMAIL, "support@sportsfansng.com", "$subject (Contact Us Form)", $email);

      if ($messageSent) success('Sent successfully');
      else error('Not sent');
   }


   public static function getAllInvoice(Request $req)
   {
      $allInvoice = Invoice::findJoin("invoice.*, users.email", "ORDER BY invoice.status DESC, invoice.created_at DESC")
         ->rightJoin("users", "invoice.user_id = users.id")
         ->join();

      // $allInvoice = Invoice::findAll("*", "ORDER BY id ASC");
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
      // Note: When joining, specify fieldnames as tablename.fieldname;
      // Prefix the every tablename with DB_PREFIX
      $prefix = DB_PREFIX;
      $allusers = Users::findJoin("{$prefix}users.*, {$prefix}user_package.slot_program, {$prefix}user_package.no_slots, {$prefix}user_package.rank, {$prefix}user_package.status as payment_status", "ORDER BY {$prefix}users.id ASC")
         ->rightJoin("{$prefix}user_package", "{$prefix}users.id = {$prefix}user_package.user_id")
         ->join();

      if ($allusers) success("All users", $allusers);
      else error("No user", null, 200);
   }

   // public static function getAllAdmin(Request $req)
   // {
   //    $allusers = Users::findAll("id, telephone, email, role, permissions, firstname, lastname, middlename, residential_address, occupation, profile_picture, nextofkin_name, nextofkin_telephone, nextofkin_residential_address, accountnumber, accountname, bankname, favorite_sport, favorite_team, referredby, referral_code, node_level, member_id, verification_status, created_at, updated_at", "WHERE role = 'admin'");
   //    if ($allusers) success("All admin", $allusers);
   //    else error("No admin", null, 200);
   // }

   public static function verifyPayment(Request $req)
   {
      extract($req->body);
      $invoicenumber = $invoicenumber ?? '';
      // $status = $status ?? '';
      $reference = $reference ?? '';

      // Validate
      Validate::isNotEmpty('Invoice number', $invoicenumber);
      Validate::hasMaxLength('Invoice number', $invoicenumber, 10);
      Validate::mustContainNumberOnly('Invoice number', $invoicenumber);
      // Validate::isNotEmpty('Status', $status);
      // Validate::hasMaxLength('Status', $status, 20);
      Validate::mustContainNumber('Reference', $reference);

      if (Validate::$status == false) error('Payment not verified');

      $curl = curl_init();
      curl_setopt_array($curl, array(
         CURLOPT_URL => "https://api.paystack.co/transaction/verify/$reference",
         CURLOPT_RETURNTRANSFER => true,
         CURLOPT_ENCODING => "",
         CURLOPT_MAXREDIRS => 10,
         CURLOPT_TIMEOUT => 30,
         CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
         CURLOPT_CUSTOMREQUEST => "GET",
         CURLOPT_HTTPHEADER => array(
            "Authorization: Bearer sk_test_23702d3d5e12e294594d4cfdf41a20a4a0b8bf8e",
            "Cache-Control: no-cache",
         ),
      ));

      $response = curl_exec($curl);
      $err = curl_error($curl);
      curl_close($curl);

      if ($err) {
         error("cURL Error #:" . $err);
      } else {
         $resp = json_decode($response, true);
         if ($resp['status'] == true) {
            $pay = $resp['data'];
            if (Payments::update([
               "domain" => $pay['domain'],
               "amount_paid" => $pay['amount'],
               "ip_address" => $pay['ip_address'],
               "channel" => $pay['channel'],
               "currency" => $pay['currency'],
               "gateway_response" => $pay['gateway_response'],
               "status" => $pay['status'],
               "paid_at" => $pay['paid_at'],
            ], "WHERE reference = '$reference'") == true) {

               $payment = Payments::findOne("invoice_number, payment_service", "WHERE reference = '$reference'");

               $invoicenumber = $payment['invoice_number'] ?: $reference;
               $service = $payment['payment_service'] ?: "Paystack";
               $updateInvoice = Member::updateInvoice("paid", $invoicenumber, ($pay['amount'] / 100), "$service: {$pay['channel']}", "");
               if ($updateInvoice == true) {
                  // It is at this point that the user should be allowed to update all his uplink

                  // $userId = User::$id;
                  $userId = Invoice::findOne("user_id", "WHERE invoice_number = '$invoicenumber'")['user_id'];
                  $user = Users::findOne('node_level, referral_code', "WHERE id = $userId");
                  $nodelevel = $user['node_level'];
                  $referralcode = $user['referral_code'];

                  // before returning success
                  // increment the referrals acquired for the users slot whose referral code is used to register (i.e if referral code is not ORG_REFERRAL_CODE)
                  if ($referralcode != ORG_REFERRAL_CODE) {
                     // TODO: TEST
                     // update all uplinks with status still active 
                     // AND whose node level is greater than or equal to the referral cap level
                     // this is going to be a recursive function
                     $nodeCapLevel = $nodelevel > 8 ? $nodelevel - 7 : 1; /* 1 is the node level of the ORGANISATION */
                     Common::updateReferralUplink($referralcode, $nodeCapLevel, $nodelevel);
                  }

                  success($resp['message']);
               } else {
                  error('Payment was not verified, contact admin');
               }
            } else error('Payment was not verified.');
         } else error($resp['message']);
      }

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
      ], "WHERE id = $userId") == true) {
         success("User status updated successfully");
      } else error("User status could not be updated; Please try again");
   }

   public static function updateUserPrivileges(Request $req)
   {
      extract($req->body);
      $role = $role ?? '';
      $permissions = $privileges ?? '';
      $userId = $userId ?? '';

      Validate::isNotEmpty('Role', $role);
      Validate::mustContainLetters('Role', $role);
      Validate::isNotEmpty('Permissions', $privileges);
      Validate::isNotEmpty('User ID', $userId);
      Validate::mustContainNumberOnly('User ID', $userId);

      if (Validate::$status == false) error("User status could not be updated", array_values(Validate::$error));

      if (Users::update([
         "permissions" => $permissions,
         "role" => $role
      ], "WHERE id = $userId") == true) {
         success("User privileges updated successfully");
      } else error("User privileges could not be updated; Please try again");
   }

   public static function getUserDownlines(Request $req)
   {
      extract($req->query);
      $email = $email ?? '';

      // Validate email
      Validate::isValidEmail('Email', $email);
      Validate::hasMaxLength('Email', $email, 100);

      if (Validate::$status == false) error("Could not get user's downlines", array_values(Validate::$error));

      $my = Users::findOne("referral_code, node_level, profile_picture, firstname, lastname, telephone", "WHERE email = '$email'");
      $referralCode = $my['referral_code'] ?? null;
      $referralLevel = intval($my['node_level']) ?? null;

      if (!is_null($referralCode) && !is_null($referralLevel)) {
         $downlines = Common::generateDownline($referralCode, $referralLevel, 1);

         success("User downlines", ['user' => $my, 'downlines' => $downlines]);
      } else error("User do not have a referral code, contact the support team.", null, 200);
   }

   public static function getOrgDownlines(Request $req)
   {
      $referralCode = ORG_REFERRAL_CODE;
      $referralLevel = 1;

      if (!is_null($referralCode) && !is_null($referralLevel)) {
         $downlines = Common::generateDownline($referralCode, $referralLevel, 1, true);

         success("Organization downlines", $downlines);
      } else error("Organization do not have a referral code, contact the support team.", null, 200);
   }

   public static function getUserBenefits(Request $req)
   {
      extract($req->query);
      $userId = $userId ?? '';

      Validate::isNotEmpty('User ID', $userId);
      Validate::mustContainNumberOnly('User ID', $userId);

      if (Validate::$status == false) error("User benefits could not be gotten", array_values(Validate::$error));

      $benefits = UserBenefits::findAll("id, achievement, cash, benefit, status, created_at, updated_at", "WHERE user_id = $userId");

      if ($benefits != false) success('User accrued benefits', $benefits);
      else error('No accrued benefits', null, 200);
   }

   public static function getAllBenefits(Request $req)
   {
      $benefits = UserBenefits::findAll("id, user_id, achievement, cash, benefit, status, created_at, updated_at");

      if ($benefits !== false) success('All benefits', $benefits);
      else error('No benefits');
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
      ], "WHERE id = $id") == true) success("User benefit status is updated");
      else error("User benefit status is not updated");
   }

   public static function updateOrganisationInfo(Request $req)
   {
      extract($req->body);
      $aboutUs = $aboutUs ?? '';
      $disclaimer = $disclaimer ?? '';
      $howItWorks = $howItWorks ?? '';
      $termsAndCondition = $termsAndCondition ?? '';
      $membership = $membership ?? '';
      $rewardsAndBenefits = $rewardsAndBenefits ?? '';
      $tournamentsAndLeagues = $tournamentsAndLeagues ?? '';
      $contactTelephone = $contactTelephone ?? '';
      $contactAddress = $contactAddress ?? '';
      $contactEmail = $contactEmail ?? '';
      $faq = $faq ?? '';

      // validation
      Validate::isNotEmpty('About Us', $aboutUs);
      Validate::isNotEmpty('Disclaimer', $disclaimer);
      Validate::isNotEmpty('How it works', $howItWorks);
      Validate::isNotEmpty('Terms and Condition', $termsAndCondition);
      Validate::isNotEmpty('Membership', $membership);
      Validate::isNotEmpty('Rewards and Benefits', $rewardsAndBenefits);
      Validate::isNotEmpty('Tournament and Leagues', $tournamentsAndLeagues);
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
         error('Info not updated', array_values(Validate::$error));
      }

      if (OrganisationInfo::update([
         "about_us" => $aboutUs,
         "disclaimer" => $disclaimer,
         "how_it_works" => $howItWorks,
         "terms_and_condition" => $termsAndCondition,
         "membership" => $membership,
         "rewards_and_benefits" => $rewardsAndBenefits,
         "tournaments_and_leagues" => $tournamentsAndLeagues,
         "contact_telephone" => $contactTelephone,
         "contact_address" => $contactAddress,
         "contact_email" => $contactEmail,
         "faq" => htmlentities($faq, ENT_QUOTES),
      ], "WHERE id = 1") == true) {
         success('Info is updated');
      } else {
         error("Info is not updated");
      }
   }

   public static function getReferralLevels(Request $req)
   {
      // $referrallevels = ReferralLevels::findAll("*");
      // if ($referrallevels == false) error("No referral levels", null, 200);
      // else success('success', $referrallevels);

      // refactored code
      $referrallevels = ReferralLevels::findAll("id, rank, referrals_required, updated_at");
      if ($referrallevels == false) error("No referral levels", null, 200);
      else {
         $count = 0;
         foreach ($referrallevels as $level) {
            // get the benefits for each level
            $benefits = ReferralBenefits::findAll("id as benefit_id, cash, souvenir", "WHERE referral_level_id = {$level['id']} ORDER BY slot_id");
            $referrallevels[$count]['benefits'] = $benefits ?: [];
            $count++;
         }
         success('success', $referrallevels);
      }
   }

   public static function updateReferralLevels(Request $req)
   {
      extract($req->body);
      $referrals_required = $referrals_required ?? '';
      $rank = $rank ?? '';
      $cash_benefit = $cash_benefit ?? '';
      $benefits = $benefits ?? '';
      $id = $id ?? '';

      Validate::isNotEmpty('ID', $id);
      Validate::mustContainNumberOnly('ID', $id);
      Validate::isNotEmpty('Referral Rank', $rank);
      Validate::mustContainLetters('Referral Rank', $rank);
      // Validate::isNotEmpty('Referrals required', $referrals_required);
      Validate::mustContainNumberOnly('Referrals required', $referrals_required);
      // Validate::isNotEmpty('Cash benefit', $cash_benefit);
      // Validate::mustContainNumberOnly('Cash benefit', $cash_benefit);
      // Validate::isNotEmpty('Souvenir benefit', $benefits);

      Validate::mustContainNumberOnly('Local Cash benefit', $local_cash);
      Validate::isNotEmpty('Local Souvenir benefit', $local_souvenir);
      Validate::mustContainNumberOnly('Foreign Cash benefit', $foreign_cash);
      Validate::isNotEmpty('Foreign Souvenir benefit', $foreign_souvenir);
      Validate::mustContainNumberOnly('International Cash benefit', $international_cash);
      Validate::isNotEmpty('International Souvenir benefit', $international_souvenir);
      Validate::mustContainNumberOnly('Continental Cash benefit', $continental_cash);
      Validate::isNotEmpty('Continental Souvenir benefit', $continental_souvenir);
      Validate::mustContainNumberOnly('World Cup Cash benefit', $worldcup_cash);
      Validate::isNotEmpty('World Cup Souvenir benefit', $worldcup_souvenir);
      Validate::mustContainNumberOnly('Olympic Cash benefit', $olympic_cash);
      Validate::isNotEmpty('Olympic Souvenir benefit', $olympic_souvenir);

      if (Validate::$status == false) error("Referral level not updated.", array_values(Validate::$error));


      // refactored code
      // update the referral benefitindividually for each slot package
      Model::transaction();

      if (
         ReferralLevels::update([
            "referrals_required" => $referrals_required,
            "rank" => $rank,
            // "cash_benefit" => $cash_benefit,
            // "benefits" => $benefits,
         ], "WHERE id = $id") == true
         ||
         (ReferralBenefits::update([
            "cash" => $local_cash,
            "souvenir" => $local_souvenir
         ], "WHERE id = $local_benefit_id") == true
            ||
            ReferralBenefits::update([
               "cash" => $foreign_cash,
               "souvenir" => $foreign_souvenir
            ], "WHERE id = $foreign_benefit_id") == true
            ||
            ReferralBenefits::update([
               "cash" => $international_cash,
               "souvenir" => $international_souvenir
            ], "WHERE id = $international_benefit_id") == true
            ||
            ReferralBenefits::update([
               "cash" => $continental_cash,
               "souvenir" => $continental_souvenir
            ], "WHERE id = $continental_benefit_id") == true
            ||
            ReferralBenefits::update([
               "cash" => $worldcup_cash,
               "souvenir" => $worldcup_souvenir
            ], "WHERE id = $worldcup_benefit_id") == true
            ||
            ReferralBenefits::update([
               "cash" => $olympic_cash,
               "souvenir" => $olympic_souvenir
            ], "WHERE id = $olympic_benefit_id") == true)
      ) {
         Model::commit();

         $referrallevels = ReferralLevels::findAll("id, rank, referrals_required, updated_at");
         $count = 0;
         foreach ($referrallevels as $level) {
            // get the benefits for each level
            $benefits = ReferralBenefits::findAll("id as benefit_id, cash, souvenir", "WHERE referral_level_id = {$level['id']} ORDER BY slot_id");
            $referrallevels[$count]['benefits'] = $benefits ?: [];
            $count++;
         }

         success("Referral level and benefits updated successfully", $referrallevels);
      } else {
         Model::rollback();
         error("Referral level and benefits not updated");
      }
   }

   public static function getSlotPackages(Request $req)
   {
      $slots = Slots::findAll("*");
      if ($slots == false) error("No slot packages", null, 200);
      else success('success', $slots);
   }

   public static function updateSlotPackage(Request $req)
   {
      extract($req->body);
      $no_slots = $no_slots ?? '';
      $program = $program ?? '';
      $cost = $cost ?? '';
      $benefits = $benefits ?? '';
      $id = $id ?? '';

      Validate::isNotEmpty('ID', $id);
      Validate::mustContainNumberOnly('ID', $id);
      Validate::isNotEmpty('Slot Package', $program);
      Validate::mustContainLetters('Slot Package', $program);
      Validate::isNotEmpty('No. slots', $no_slots);
      Validate::mustContainNumberOnly('No. slots', $no_slots);
      Validate::isNotEmpty('Cost', $cost);
      Validate::mustContainNumberOnly('Cost', $cost);
      Validate::isNotEmpty('Slot benefit', $benefits);

      if (Validate::$status == false) error("Slot package not updated.", array_values(Validate::$error));

      if (Slots::update([
         "program" => $program,
         "no_slots" => $no_slots,
         "cost" => $cost,
         "benefits" => $benefits
      ], "WHERE id = $id") == true) success("Slot package updated successfully", Slots::findAll("*"));
      else error("Slot package not updated");
   }

   public static function getCompetitions(Request $req)
   {
      $competitions = Competitions::findAll();
      if ($competitions == false) error("No competitions", null, 200);
      else success('success', $competitions);
   }

   public static function addCompetition(Request $req)
   {
      extract($req->body);
      $slotpackage = $slotpackage ?? '';
      $sport = $sport ?? '';
      $region = $region ?? '';
      $competition = $competition ?? '';
      $qualifiedTeams = $qualified_teams ?? '';

      Validate::isNotEmpty("Slot Package", $slotpackage);
      Validate::mustContainNumberOnly("Slot Package", $slotpackage);
      Validate::isNotEmpty("Sport", $sport);
      Validate::mustContainLetters("Sport", $sport);
      Validate::isNotEmpty("Region", $region);
      Validate::mustContainLetters("Region", $region);
      Validate::isNotEmpty("Competition", $competition);
      Validate::mustContainLetters("Competition", $competition);
      Validate::isNotEmpty("Qualified Teams", $qualifiedTeams);

      $sanitizedQualifiedTeams = [];
      foreach (json_decode($qualifiedTeams, true) as $team) {
         $sanitizedQualifiedTeams[] = Sanitize::string($team);
      }
      $qualifiedTeams = json_encode($sanitizedQualifiedTeams);

      if (Validate::$status == false) error("Competition not added.", array_values(Validate::$error));

      if (Competitions::create([
         "slotpackage" => $slotpackage,
         "competition" => $competition,
         "region" => $region,
         "sport" => $sport,
         "qualified_teams" => $qualifiedTeams
      ]) == true) {
         success('Competition added successfully', Competitions::findAll() ?: []);
      }
   }

   public static function updateCompetition(Request $req)
   {
      extract($req->body);
      $slotpackage = $slotpackage ?? '';
      $sport = $sport ?? '';
      $region = $region ?? '';
      $competition = $competition ?? '';
      $qualifiedTeams = $qualified_teams ?? '';
      $competitionId = $competitionId ?? '';

      Validate::mustContainNumberOnly("Competition ID", $competitionId);
      Validate::isNotEmpty("Slot Package", $slotpackage);
      Validate::mustContainNumberOnly("Slot Package", $slotpackage);
      Validate::isNotEmpty("Sport", $sport);
      Validate::mustContainLetters("Sport", $sport);
      Validate::isNotEmpty("Region", $region);
      Validate::mustContainLetters("Region", $region);
      Validate::isNotEmpty("Competition", $competition);
      Validate::mustContainLetters("Competition", $competition);
      Validate::isNotEmpty("Qualified Teams", $qualifiedTeams);

      $sanitizedQualifiedTeams = [];
      foreach (json_decode($qualifiedTeams, true) as $team) {
         $sanitizedQualifiedTeams[] = Sanitize::string($team);
      }
      $qualifiedTeams = json_encode($sanitizedQualifiedTeams);

      if (Validate::$status == false) error("Competition not added.", array_values(Validate::$error));

      if (Competitions::update([
         "slotpackage" => $slotpackage,
         "competition" => $competition,
         "region" => $region,
         "sport" => $sport,
         "qualified_teams" => $qualifiedTeams
      ], "WHERE id = $competitionId") == true) {
         success('Competition added successfully', Competitions::findAll() ?: []);
      }
   }

   public static function deleteCompetition(Request $req)
   {
      extract($req->body);
      $competitionId = $competitionId ?? '';
      Validate::mustContainNumberOnly("Competition ID", $competitionId);

      if (Validate::$status == false) error("Competition not deleted.", array_values(Validate::$error));

      if (Competitions::delete("WHERE id = $competitionId") == true) success('Competition deleted successfully', Competitions::findAll() ?: []);
      else error("Competition not deleted", null, 200);
   }

   public static function getUnreadNotification(Request $req)
   {
      $userId = User::$id;

      $unreadNotifications = Notifications::findAll("id, user_id, message, route, status, created_at, updated_at", "WHERE user_id = 0 AND status = 'unread' ORDER BY created_at DESC LIMIT 5");

      success('Unread Notifications', $unreadNotifications ?: []);
   }

   public static function queryUnpaidInvoice(Request $req)
   {
      extract($req->body);

      Common::notify($userId, "Complete your registeration by paying for your Slot Package so you can start getting your accrued benefits.", '/member/invoice');
      success();
   }

   public static function sendNotice(Request $req)
   {
      extract($req->body);

      Common::notify($userId, $message);
      success();
   }

   public static function sendGroupNotice(Request $req)
   {
      extract($req->body);
      $userIds = json_decode($userIds);
      // exit($userIds);

      foreach ($userIds as $userId) {
         Common::notify($userId, $message);
      }
      success();
   }

   public static function giveBenefit(Request $req)
   {
      extract($req->body);

      if (UserBenefits::update([
         "status" => "given"
      ], "WHERE id = $benefitId") == true) {
         Common::notify($userId, "Your accrued benefit have been delivered to you and have been marked as given.", '/member/benefits');
         success();
      } else {
         error();
      }
   }

   public static function cancelBenefit(Request $req)
   {
      extract($req->body);

      if (UserBenefits::update([
         "status" => "cancelled"
      ], "WHERE id = $benefitId") == true) {
         Common::notify($userId, "Your accrued benefit have been marked as cancelled.", '/member/benefits');
         success();
      } else {
         error();
      }
   }

   public static function getProfile(Request $req)
   {
      extract($req->body);
      $email = $email ?? '';

      $profile = Users::findJoin("users.*, user_package.slot_program, user_package.no_slots, user_package.rank", "WHERE users.email = '$email' ORDER BY users.id ASC")
         ->rightJoin("user_package", "users.id = user_package.user_id")
         ->join();

      if ($profile) success("success", $profile[0]);
      else error("error", null, 200);
   }

   public static function getPayments(Request $req)
   {
      $payments = Payments::findAll("id, invoice_number, reference, email, domain, amount_paid, ip_address, payment_service, channel, currency, gateway_response, status, paid_at, created_at, updated_at", "ORDER BY status DESC, domain ASC");

      if ($payments != false) success('All payments', $payments);
      else error('No payments', null, 200);
   }

   public static function allBenefits(Request $req)
   {
      $benefits = UserBenefits::findJoin("user_benefits.*, users.email", "ORDER BY user_benefits.status DESC, user_benefits.created_at ASC")
         ->innerJoin("users", "user_benefits.user_id = users.id")
         ->join();

      if ($benefits != false) success('Your accrued benefits', $benefits);
      else error('No accrued benefits', null, 200);
   }

   public static function memberBenefits(Request $req)
   {
      extract($req->body);

      $benefits = UserBenefits::findJoin("user_benefits.*, users.email", "WHERE users.email = '$email' ORDER BY user_benefits.status DESC, user_benefits.created_at ASC ")
         ->innerJoin("users", "user_benefits.user_id = users.id")
         ->join();

      if ($benefits != false) success('Your accrued benefits', $benefits);
      else error('No accrued benefits', null, 200);
   }

   public static function getMemberPackage(Request $req)
   {
      extract($req->body);

      $package = UserPackage::findJoin("user_package.*, users.email", "WHERE users.email = '$email'")
         ->rightJoin("users", "user_package.user_id = users.id")
         ->join();

      if ($package != false) success('Your Package', $package[0]);
      else error('No Package', null, 200);
   }

   public static function getMemeberSlots(Request $req)
   {
      extract($req->body);

      $slots = UserSlots::findJoin("user_slots.*, users.email", "WHERE users.email = '$email' ORDER BY user_slots.id ASC")
         ->rightJoin("users", "user_slots.user_id = users.id")
         ->join();

      if ($slots != false) success('Your Slots', $slots);
      else error('No Slots', null, 200);
   }
}
