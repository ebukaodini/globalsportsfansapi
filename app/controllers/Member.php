<?php
namespace Controllers;
use Library\Http\Request;
use Services\User;
use Services\Validate;
use Services\Upload;
use Models\Slots;
use Models\UserSlots;
use Models\Users;
use Models\Invoice;
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
         error('Invalid slot');
      }

      $slot = Slots::findOne("id, program, no_slots, cost, benefits", "WHERE id = $slotId");

      if ($slot != false) {
         // turn on transaction for multiple step insertions
         Users::transaction();

         // create slot for user
         $slotcreate = UserSlots::create([
            "user_id" => User::$id,
            "slot_id" => $slot['id'],
            "slot_program" => $slot['program']
         ]);

         if ($slotcreate == false) error("Slot was not created. Please try again");

         // generate referral code
         $referralcode = Common::generateReferralCode(7);
         // check if exist everytime
         while (Users::exist("WHERE referral_code = '$referralcode'") == true) {
            $referralcode = Common::generateReferralCode(7);
         }
         // update user's referral code
         $referralupdate = Users::update([
            "referral_code" => $referralcode
         ], "WHERE email = '$email'");

         if ($referralupdate == false) {
            Users::rollback();
            error("Slot was not created. Please try again");
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
            "invoice_description" => "Invoice generated for the acquisition of slot program: " + $slot['program'],
            "amount_due" => $slot['cost']
         ]);

         if ($invoicecreation == false) {
            Users::rollback();
            error("Slot was not created. Please try again");
         }

         Users::commit();

         // send email to the member to let him/her know sha has an invoice
         Mail::asHTML("<h4>Good day,</h4><p>A payment invoice has been created for you.<br>Slot progam: {$slot['program']}</br>Invoice number: $invoicenumber</p>")->send(ORG_EMAIL, User::$email, "Payment Invoice [#$invoicenumber]", ORG_EMAIL);

         // send back response
         success('Slot has been created for the user');
        
      } else {
         error('Slot not found');
      }

   }

   public static function read(Request $req)
   {
      // return a resource
   }

   public static function update(Request $req)
   {
      // update a resource
   }

   public static function delete(Request $req)
   {
      // remove a resouce
   }

}
