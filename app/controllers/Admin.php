<?php
namespace Controllers;
use Library\Http\Request;
use Models\Invoice;
use Models\Users;

class Admin
{

   public static function getAllInvoice(Request $req)
   {
      $allInvoice = Invoice::findAll("*");
      if ($allInvoice == false) error("No invoice");
      else success('All invoice', $allInvoice);
   }

   public static function getAllPaidInvoice(Request $req)
   {
      $allInvoice = Invoice::findAll("*", "WHERE status = 'paid'");
      if ($allInvoice == false) error("No paid invoice");
      else success('All paid invoice', $allInvoice);
   }

   public static function getAllUnpaidInvoice(Request $req)
   {
      $allInvoice = Invoice::findAll("*", "WHERE status = 'unpaid'");
      if ($allInvoice == false) error("No unpaid invoice");
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
      else error("No user");
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
