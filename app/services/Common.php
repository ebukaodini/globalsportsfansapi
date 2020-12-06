<?php
namespace Services;

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
}
