<?php
namespace Controllers;
use Library\Http\Request;

class User
{

   public static function index(Request $req)
   {
      // return all resources
   }

   // create a new user
   public static function create(Request $req)
   {
      // $firstname = 
      exit(json_encode($req->body));
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
