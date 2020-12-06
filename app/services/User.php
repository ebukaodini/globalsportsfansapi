<?php
namespace Services;

class User
{
   public static $isAuthenticated = false;
   public static $email = '';
   public static $role = 'Guest';
   public static $privileges = [];

   public static function user()
   {
      return json_encode([
         "auth"=>self::$isAuthenticated,
         "email"=>self::$email,
         "role"=>self::$role,
      ]);
   }

   public static function has(string $privilege)
   {
      return in_array($privilege, self::$privileges);
   }

   public static function hasAll(array $privileges)
   {
      $count = 0;
      foreach ($privileges as $privilege) {
         if (in_array($privilege, self::$privileges)) $count++;
      }
      return $count == count($privileges);
   }

   public static function hasAny(array $privileges)
   {
      $count = 0;
      foreach ($privileges as $privilege) {
         if (in_array($privilege, self::$privileges)) $count++;
      }
      return $count > 0;
   }

   public static function is(string $role)
   {
      return self::$role == $role;
   }

   public static function isNot(string $role)
   {
      return self::$role != $role;
   }

}