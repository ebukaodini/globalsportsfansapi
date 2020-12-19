<?php
namespace Controllers;
use Library\Http\Request;
use Models\Invoice;
use Models\Users;
use Services\Cipher;
use Services\User;
use Services\Validate;
use Models\ReferralLevels;
use Models\UserBenefits;

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

   public static function register(Request $req)
   {
      // create a resource
      extract($req->body);
   }

   public static function getAllUsers(Request $req)
   {
      $allusers = Users::findAll("*");
      if ($allusers) success("All users", $allusers);
      else error("No user", null, 200);
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

   public static function update(Request $req)
   {
      // update a resource
      extract($req->body);
   }

   public static function delete(Request $req)
   {
      // remove a resouce
   }

}
