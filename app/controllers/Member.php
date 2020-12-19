<?php
namespace Controllers;
use Library\Http\Request;
use Services\User;
use Services\Validate;
use Services\Upload;
use Models\Slots;
use Models\UserSlots;
use Models\UserBenefits;
use Models\Users;
use Models\Invoice;
use Models\ReferralLevels;
use Services\Cipher;
use Services\Common;
use Services\Mail;

class Member
{

   public static function dashboard(Request $req)
   {
      success("Hello, Goodbye");
   }

   // member has choosen a slot,
   // creates a slot for the user
   // creates an invoice for the user
   public static function chooseSlot(Request $req)
   {
      extract($req->body);
      $email = User::$email;
      $slotId = $slot ?? '';

      Validate::isInteger('Slot Id', $slotId);
      if (Validate::$status == false) {
         error('Invalid slot', null, 200);
      }

      $slot = Slots::findOne("id, program, no_slots, cost, benefits", "WHERE id = $slotId");

      if ($slot != false) {
         // turn on transaction for multiple step insertions
         Users::transaction();

         // check if user has an uncompleted slot before opening another
         $userHaveUncompletedSlot = UserSlots::exist("WHERE user_id = ".User::$id." AND status <> 'completed'");
         if ($userHaveUncompletedSlot) error("User have a slot already", null, 200);

         // get the current referral level and the next referral level
         // and also use them in creating the user's slot
         $currentReferralLevel = 1;
         $nextReferralLevel = $currentReferralLevel + 1;
         $referralLevel = ReferralLevels::findOne("rank", "WHERE id = $currentReferralLevel");
         $nextReferralLevel = ReferralLevels::findOne("referrals_required, rank", "WHERE id = $nextReferralLevel");
         
         // create slot for user
         $slotcreate = UserSlots::create([
            "user_id" => User::$id,
            "slot_id" => $slot['id'],
            "slot_program" => $slot['program'],
            "initial_referrals_required" => (intval($slot['no_slots']) * intval($nextReferralLevel['referrals_required'])), // the initial referrals required to form the user's direct downlink
            "referrals_required" => (intval($slot['no_slots']) * intval($nextReferralLevel['referrals_required'])), // referrals required to attain next level
            "target_rank" => $nextReferralLevel['rank'],
            "rank" => $referralLevel['rank']
         ]);

         if ($slotcreate == false) error("Slot was not created. Please try again", null, 200);
         // TODO: Notify user, referredby and admin of new slot created

         // generate referral code
         $referralcode = Cipher::hash(7);
         // check if exist everytime
         while (Users::exist("WHERE referral_code = '$referralcode'") == true) {
            $referralcode = Cipher::hash(7);
         }
         // update user's referral code
         $referralupdate = Users::update([
            "referral_code" => $referralcode
         ], "WHERE email = '$email'");

         // TODO: notify user of their referral code

         if ($referralupdate == false) {
            Users::rollback();
            error("Slot was not created. Please try again", null, 200);
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
            "invoice_description" => "Invoice generated for the acquisition of slot program: " . $slot['program'],
            "amount_due" => $slot['cost']
         ]);

         if ($invoicecreation == false) {
            Users::rollback();
            error("Slot was not created. Please try again", null, 200);
         }

         Users::commit();

         // send email to the member to let him/her know sha has an invoice
         Mail::asHTML("<h4>Good day,</h4><p>A payment invoice has been created for you.<br>Slot progam: {$slot['program']}</br>Invoice number: $invoicenumber</p>")->send(ORG_EMAIL, User::$email, "Payment Invoice [#$invoicenumber]", ORG_EMAIL);

         // TODO: Notify user of new invoice
         // TODO: Notify admin of new invoice
         
         // send back response
         success('Slot has been created for the user');
        
      } else {
         error('Slot not found', null, 200);
      }

   }

   public static function getUnpaidInvoices(Request $req)
   {
      $unpaidInvoices = Invoice::findAll("id, invoice_number, invoice_description, amount_due, status, created_at", "WHERE status = 'unpaid' AND user_id = ".User::$id);

      if ($unpaidInvoices == false) error('No unpaid invoice', null, 200);
      else success('All unpaid invoices', $unpaidInvoices);
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
      Upload::tmp('paymentevidence', 'payments/' . User::$id.".".date('ymdhis'), Upload::$imagefiles, null, 2);
      $path = Upload::$path ?? '';

      if (Invoice::update([
         "amount_paid" => $amountpaid,
         "payment_method" => $paymentmethod,
         "payment_evidence" => $path,
         "status" => "unverified payment"
      ], "WHERE invoice_number = '$invoicenumber'")) success('Payment details submitted'); else error('Payment details not submitted; Please try again', null, 200);

   }

   public static function getDownlines(Request $req)
   {
      // get the users registered under this users
      // i.e the users whose referredby is the users referral_code

      $email = User::$email;
      $my = Users::findOne("referral_code, referral_level", "WHERE email = '$email'");
      $referralCode = $my['referral_code'] ?? null;
      $referralLevel = intval($my['referral_level']) ?? null;
      
      if (!is_null($referralCode) && !is_null($referralLevel)) {
         $downlines = Common::generateDownline($referralCode, $referralLevel, 1);

         success("Your downlines", $downlines);

      } else error("You don't have a referral code, contact the support team.", null, 200);
      
   }

   public static function myBenefits(Request $req)
   {
      $userId = User::$id;
      $benefits = UserBenefits::findAll("id, achievement, cash, benefit, status, created_at, updated_at", "WHERE user_id = $userId");
      
      if ($benefits != false) success('Your accrued benefits', $benefits); else error('No accrued benefits', null, 200);
   }

}
