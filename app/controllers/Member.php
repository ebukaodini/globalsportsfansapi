<?php

namespace Controllers;

use Library\Http\Request;
use Services\User;
use Services\Validate;
use Services\Upload;
use Models\Slots;
use Models\UserPackage;
use Models\UserBenefits;
use Models\Users;
use Models\Invoice;
use Models\Notifications;
use Models\Payments;
use Models\ReferralLevels;
use Models\UserSlots;
use Services\Cipher;
use Services\Common;
use Services\Mail;

class Member
{

   public static function dashboard(Request $req)
   {
      $userId = User::$id;

      $myPackage = UserPackage::findOne("no_slots, slot_program, initial_referrals_required, target_rank, rank, status, created_at, updated_at", "WHERE user_id = $userId");

      // UserSlots::findOne("id, user_id, user_package_id, no_slots, referral_code, referral_level, node_level, referrals_required, referrals_acquired, update_uplink, status, created_at, updated_at", "WHERE user_id = $userId AND status = 'active'");

      $myActiveSlot = UserSlots::findJoin("user_slots.*, referral_levels.cash_benefit", "WHERE user_id = $userId AND status = 'active'")
         ->leftJoin("referral_levels", "user_slots.referral_level = referral_levels.rank")
         ->join();

      $recentUnreadNotifications = Notifications::findAll("id, user_id, message, route, status, created_at, updated_at", "WHERE user_id = $userId AND status = 'unread' ORDER BY created_at DESC LIMIT 5");

      $totalCashBenefitsAccrued = UserBenefits::findAll("SUM(cash) as totalCash", "WHERE user_id = $userId")[0]['totalCash'];

      $recentPendingBenefits = UserBenefits::findAll("id, user_id, achievement, cash, benefit, status, created_at, updated_at", "WHERE user_id = $userId AND status = 'pending' ORDER BY created_at DESC LIMIT 3");

      success('success', [
         'userPackage' => $myPackage ?: null,
         'activeSlot' => $myActiveSlot[0] ?: null,
         'recentUnreadNotifications' => $recentUnreadNotifications ?: [],
         'totalCashBenefitAccrued' =>  $totalCashBenefitsAccrued ?: 0,
         'recentPendingBenefits' => $recentPendingBenefits ?: []
      ]);
   }

   // member has choosen a slot,
   // creates a slot for the user
   // creates an invoice for the user
   public static function chooseSlot(Request $req)
   {
      extract($req->body);
      $email = User::$email ?: $email;
      $slotId = $slot ?? '';
      User::$id = Users::findOne('id', "WHERE email = '$email'")['id'];

      Validate::isInteger('Slot Id', $slotId);
      if (Validate::$status == false) {
         error('Invalid slot', null, 200);
      }

      $slot = Slots::findOne("id, program, no_slots, cost, benefits", "WHERE id = $slotId");

      if ($slot != false) {
         // turn on transaction for multiple step insertions
         Users::transaction();

         // check if user has an uncompleted slot before opening another
         $userHaveUncompletedSlot = UserPackage::exist("WHERE user_id = " . User::$id . " AND status <> 'completed'");
         if ($userHaveUncompletedSlot) error("User have a slot already", null, 200);

         // get the current referral level and the next referral level
         // and also use them in creating the user's slot
         $currentReferralLevel = 1;
         $nextReferralLevel = $currentReferralLevel + 1;
         $referralLevel = ReferralLevels::findOne("rank", "WHERE id = $currentReferralLevel");
         $nextReferralLevel = ReferralLevels::findOne("referrals_required, rank", "WHERE id = $nextReferralLevel");

         $user = Users::findOne('node_level, referral_code', "WHERE email = '$email'");
         [$nodeLevel, $referralcode] = [$user['node_level'], $user['referral_code']];

         // create package for user
         $packagecreate = UserPackage::create([
            "user_id" => User::$id,
            "slot_id" => $slot['id'],
            "no_slots" => $slot['no_slots'],
            "slot_program" => $slot['program'],
            "referral_code" => $referralcode,
            "initial_referrals_required" => (intval($slot['no_slots']) * intval($nextReferralLevel['referrals_required'])), // the initial referrals required to form the user's direct downlink
            "target_rank" => $nextReferralLevel['rank'],
            "rank" => $referralLevel['rank']
         ]);

         if ($packagecreate == false) error("Slot was not created. Please try again", null, 200);

         $packageId = UserPackage::lastId();

         // create slots for each referral level for the user
         $referralLevels = ReferralLevels::findAll("referrals_required, rank", "WHERE id > 1");

         $noslots = $slot['no_slots'];
         $levelCount = 0;
         $baseLevel = $nodeLevel;
         $slotcreated = [];
         foreach ($referralLevels as $level) {
            // for each level create slots for the user according to the number of slot he bought
            $slots = [];
            // for each level, increment the expected node level
            $nnodeLevel = ++$baseLevel;
            for ($i = 0; $i < $noslots; $i++) {
               $slots[] = [
                  "user_id" => User::$id,
                  "user_package_id" => $packageId,
                  "no_slots" => $noslots,
                  "referral_code" => $referralcode,
                  "referral_level" => $level['rank'],
                  "node_level" => $nnodeLevel,
                  "referrals_required" => intval($level['referrals_required']),
                  "referrals_acquired" => 0,
                  "update_uplink" => $i == 0 ? true : false,
                  "status" => $i == 0 && $levelCount == 0 ? 'active' : 'pending' // set the very first slot of Coach level to active
               ];
            }
            $slotcreated[] = UserSlots::createMany(...$slots);

            $levelCount++;
         }

         if (in_array(false, $slotcreated)) {
            Users::rollback();
            error("Slot was not created. Please try again", null, 200);
         } else {
            // TODO: Notify user
            $totalSlots = $noslots * 6;
            Common::notify(User::$id, "Congratulations, $totalSlots slots have been created for you, with $noslots slot(s) at each level.", '/member/slots');
         }

         // generate invoice for the slot
         // generate invoice number
         $invoicenumber = intval(Invoice::findAll("COUNT(*) as count")[0]['count']) + 1;
         $invoicenumber = str_pad($invoicenumber, 10, "0", STR_PAD_LEFT);

         // check if exist everytime
         while (Invoice::exist("WHERE invoice_number = '$invoicenumber'") == true) {
            $invoicenumber = str_pad((intval($invoicenumber) + 1), 10, "0", STR_PAD_LEFT);
         }

         $invoicecreation = Invoice::create([
            "user_id" => User::$id,
            "invoice_number" => $invoicenumber,
            "invoice_description" => "Invoice generated for the acquisition of slot package: " . $slot['program'] . " $noslots Slot(s).",
            "amount_due" => $slot['cost']
         ]);

         if ($invoicecreation == false) {
            Users::rollback();
            error("Slot was not created. Please try again", null, 200);
         }

         Users::commit();

         // send email to the member to let him/her know sha has an invoice
         Mail::asHTML("<h4>Good day,</h4><p>A payment invoice has been created for you.<br>Slot Package: {$slot['program']}</br>Number of Slots: $noslots</br>Invoice number: $invoicenumber</p>")->send(ORG_EMAIL, User::$email, "Payment Invoice [#$invoicenumber]", ORG_EMAIL);

         // TODO: Notify user of new invoice
         Common::notify(User::$id, "A payment invoice have been created for you for the acquisition of your {$slot['program']} package. Invoice Number: #$invoicenumber", '/member/invoice');
         // TODO: Notify admin of new invoice
         Common::notify(0, "A payment invoice have been created for " . $email . " for the acquisition of your {$slot['program']} package. Invoice Number: #$invoicenumber", '/member/invoice');

         // send back response
         success('Slot has been created for the user');
      } else {
         error('Slot not found', null, 200);
      }
   }

   public static function getUnpaidInvoice(Request $req)
   {
      $unpaidInvoices = Invoice::findOne("id, invoice_number, invoice_description, amount_due, status, created_at", "WHERE status = 'unpaid' AND user_id = " . User::$id);

      if ($unpaidInvoices == false) error('No unpaid invoice', null, 200);
      else success('Unpaid invoice', $unpaidInvoices);
   }

   public static function submitPaymentDetails($req)
   {
      extract($req->body);
      $invoicenumber = $invoicenumber ?? '';
      $paymentmethod = $paymentmethod ?? '';
      $amountpaid = $amountpaid ?? '';

      // Validate
      Validate::isNotEmpty('Invoice number', $invoicenumber);
      Validate::hasMaxLength('Invoice number', $invoicenumber, 10);
      Validate::mustContainNumberOnly('Invoice number', $invoicenumber);
      Validate::isNotEmpty('Payment method', $paymentmethod);
      Validate::hasMaxLength('Payment method', $paymentmethod, 20);
      Validate::isNotEmpty('Amount paid', $amountpaid);
      Validate::mustContainNumberOnly('Amount Paid', $amountpaid);

      if (Validate::$status == false) error('Payment details not submitted', array_values(Validate::$error), 200);

      // whether paymentevidence was uploaded or not, upload it
      Upload::$field = 'Payment Evidence';
      Upload::$unit = 'Mb';
      Upload::tmp('paymentevidence', 'payments/' . User::$id . "." . date('ymdhis'), Upload::$imagefiles, null, 2);
      $path = Upload::$path ?? '';

      if (Invoice::update([
         "amount_paid" => $amountpaid,
         "payment_method" => $paymentmethod,
         "payment_evidence" => $path,
         "status" => "unverified payment"
      ], "WHERE invoice_number = '$invoicenumber'")) success('Payment details submitted');
      else error('Payment details not submitted; Please try again', null, 200);
   }

   public static function getDownlines(Request $req)
   {
      // get the users registered under this users
      // i.e the users whose referredby is the users referral_code

      $email = User::$email;
      $my = Users::findOne("referral_code, node_level", "WHERE email = '$email'");
      $referralCode = $my['referral_code'] ?? null;
      $referralLevel = intval($my['node_level']) ?? null;

      if (!is_null($referralCode) && !is_null($referralLevel)) {
         $downlines = Common::generateDownline($referralCode, $referralLevel, 1);

         if (is_null($downlines)) {
            error('Your downline is empty');
         }
         success("Your downlines", $downlines);
      } else error("You don't have a referral code, contact the support team.", null, 200);
   }

   public static function myBenefits(Request $req)
   {
      $userId = User::$id;
      $benefits = UserBenefits::findAll("id, achievement, cash, benefit, status, created_at, updated_at", "WHERE user_id = $userId ORDER BY status DESC, created_at ASC");

      if ($benefits != false) success('Your accrued benefits', $benefits);
      else error('No accrued benefits', null, 200);
   }

   public static function getMySlots(Request $req)
   {
      $userId = User::$id;
      $mySlots = UserSlots::findAll("referral_level, referrals_required, referrals_acquired, status, updated_at", "WHERE user_id = $userId ORDER BY id ASC");

      if ($mySlots != false) success('Your Slots', $mySlots);
      else error('No Slots', null, 200);
   }

   public static function getMyPackage(Request $req)
   {
      $userId = User::$id;
      $myPackage = UserPackage::findOne("no_slots, slot_program, initial_referrals_required, target_rank, rank, status, created_at, updated_at", "WHERE user_id = $userId");

      if ($myPackage != false) success('Your Package', $myPackage);
      else error('No Package', null, 200);
   }

   public static function getUnreadNotification(Request $req)
   {
      $userId = User::$id;

      $unreadNotifications = Notifications::findAll("id, user_id, message, route, status, created_at, updated_at", "WHERE user_id = $userId AND status = 'unread' ORDER BY created_at DESC LIMIT 5");

      success('Unread Notifications', $unreadNotifications ?: []);
   }

   public static function markNotificationAsRead(Request $req)
   {
      extract($req->body);
      $userId = User::$id;
      // $notificationId = $notificationId ?? null;

      if (!isset($notificationId)) error();

      if (Notifications::update([
         "status" => 'read'
      ], "WHERE id = $notificationId AND user_id = $userId") == true) success();
      else error();
   }

   public static function sendEmailInvite(Request $req)
   {
      extract($req->body);

      $user = Users::findOne("CONCAT(firstname,' ',lastname) as fullname, referral_code", "WHERE id = " . User::$id);
      $fullname = $user['fullname'] ?? User::$email;
      $referralcode = $user['referral_code'];
      $inviteWasSent = Mail::asHTML(
         "<h4>Good day,</h4>
         <p>Your friend $fullname invites you to join Sports Fans Club by joining with his referral link https://initframework.com/register?referral=$referralcode</p> <br /><br />
         <p><b>SPORT FANS CLUB (SFC)</b> is a property of <b>GLOBAL SPORTS FANS LIMITED</b>. SFC was created to satisfy the aspirations of millions of sports fans who are enthusiastic about different sports and exude passion in supporting their favourite teams during sporting competitions. <br />
         SFC provides an opportunity for individuals to live out the reality of being a “true fan”. A true fan is someone who not only loves and cheer a team, but is a registered and recognized member of the team’s supporters base. SFC help registered members to achieve this dream. Depending on your ability and activity in the CLUB, SFC will provide you with your favourite club’s souvenirs and sponsor your trip to watch a live match/event involving your team.</p>
         "
      )->send(ORG_EMAIL, $friendEmail, "Invitation to Sports Fans Club", ORG_EMAIL);

      if ($inviteWasSent) success();
      else error();
   }

   public static function queryAccruedBenefit(Request $req)
   {
      Common::notify(0, "This member (" . User::$email . ") is querying his benefits", '/member/benefits');
      success();
   }

   public static function initiatePayment(Request $req)
   {
      extract($req->body);
      $invoicenumber = $invoicenumber ?? '';
      $reference = $reference ?? '';
      $service = $service ?? '';

      Validate::mustContainNumber('Invoice Number', $invoicenumber);
      Validate::mustContainNumber('Reference', $reference);

      if (Validate::$status == false) error();

      if (Payments::create([
         "invoice_number" => $invoicenumber,
         "reference" => $reference,
         "email" => User::$email,
         "payment_service" => $service
      ]) == true) success();
      else error();
   }

   public static function verifyPayment(Request $req)
   {
      extract($req->body);
      $reference = $reference ?? '';

      Validate::mustContainNumber('Reference', $reference);
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
               $updateInvoice = self::updateInvoice("paid", $invoicenumber, ($pay['amount'] / 100), "$service: {$pay['channel']}", "");
               if ($updateInvoice == true) {
                  success($resp['message']);
               } else {
                  error('Payment was not verified, contact admin');
               }
            } else error('Payment is not verified.');
         } else error($resp['message']);
      }
   }

   private static function updateInvoice($status, $invoicenumber, $amountpaid, $paymethod, $payevidence)
   {
      $updatestatus = Invoice::update([
         "amount_paid" => $amountpaid,
         "payment_method" => $paymethod,
         "payment_evidence" => $payevidence,
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

            UserPackage::update([
               "status" => "active"
            ], "WHERE user_id = $memberUserId");

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
            Common::notify($memberUserId, "Congratulations!!! Your benefit is due for accrual for Attaining {$referralLevel['rank']} level.", '/member/benefits');

            $email = Users::findOne("email", "WHERE id = $memberUserId")['email'] ?? 'A customer';
            // Notify admin
            Common::notify(0, "Notice!, $email has a benefit that is due for Attaining {$referralLevel['rank']} level.", '/member/benefits');
         }

         // TODO: notify member
         return true;
      } else return false;
   }
}
