<?php
namespace Middlewares;
use Library\Http\Request;
// use Library\Http\Router;
use Services\User;
use Services\JWT as JWTService;

class JWT
{
   public static function auth(Request $request)
   {
      $token = $request->httpAuthtoken ?? '';

      if ( empty($token) == true) {
         error('Please login', null, 401);
      }

      try {
         $payload = JWTService::decode($token, APP_KEY, ['HS256', 'HS384', 'HS512', 'RS256']);
         
         // retrieve credentials
         User::$isAuthenticated = true;
         User::$email = $payload->email;
         User::$role = $payload->role;
         User::$privileges = explode(",", $payload->permissions);

         // set the users details

      } catch (\Exception $e) {
         error('Please login', null, 401);
      }
   }
}
