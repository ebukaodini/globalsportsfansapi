<?php
namespace Services;

use Models\Users;
use PhpParser\Node\Stmt\Foreach_;

class Common
{
   public static function generateReferralCode($length)
   {
      //'A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z',
      $options = ['0','1','2','3','4','5','6','7','8','9','a','b','c','d','e','f','g','h','i','j','k','l','m','n','o','p','q','r','s','t','u','v','w','x','y','z'];
      $max = count($options);
      $code = "";
      for ($i=0; $i < $length; $i++) { 
         $code .= $options[rand(0, $max - 1)];
      }
      return $code;
   }

   public static $downlines = [];
   public static $limit = 7;

   // this is a recursive function
   public static function generateDownline(string $referralCode, int $generation)
   {

      // limit to the downline limit
      if (($generation - 1) == self::$limit) return;

      // get all the users whose referredby is this referral_code
      $aGeneration = Users::findAll("firstname, lastname, middlename, telephone, email, referredby, referral_code, referral_level", "WHERE referredby = '$referralCode'");

      if ($aGeneration != false) {
         foreach ($aGeneration as $member) {
            self::$downlines[$generation] = $member;
            self::$downlines[$generation]['downline'] = self::generateDownline($member['referral_code'], $generation+1);
         }
      }

      return self::$downlines;

   }
}
