<?php
namespace Services;

use Models\Users;
use Models\UserSlots;
use Models\Slots;
use Models\ReferralLevels;
use Models\UserBenefits;

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
   public static function generateDownline(string $referralCode, int $referralLevel, int $gLevel)
   {
      // limit to the downline limit
      if (($gLevel - 1) == self::$limit) return;

      // get all the users whose referredby is this referral_code
      $aGeneration = Users::findAll("firstname, lastname, middlename, telephone, email, referredby, referral_code, referral_level", "WHERE referredby = '$referralCode' AND referral_level > $referralLevel AND verification_status = 'verified'");

      if ($aGeneration != false) {
         $count = 0;
         foreach ($aGeneration as $member) {
            $downline[$count] = $member;
            $downline[$count]['level'] = $gLevel;
            $referralCode = $member['referral_code'] ?: null;
            $referralLevel = intval($member['referral_level']);
            $downline[$count]['downline'] = is_null($referralCode) ? null : self::generateDownline($referralCode, $referralLevel, ($gLevel + 1));
            $count++;
         }
      }

      return $downline;
   }

   public static function updateAllUplinks(string $referralCode)
   {
      // get the user that referred me
      $referral = Users::findOne("id, referredby", "WHERE referral_code = '$referralCode'");
      // get his id and get who referred him
      $referralCodeUserId = $referral['id'];
      $referralReferredBy = $referral['referredby'] ?? null;
      
      // increment his acquired referrals if his slot is still active (i.e not completed)
      UserSlots::update([
         "referrals_acquired" => intval((UserSlots::findOne("referrals_acquired", "WHERE user_id = $referralCodeUserId")['referrals_acquired'] ?: 0) + 1),
      ], "WHERE user_id = $referralCodeUserId AND status <> 'completed'");

      // if referrals_acquired == referrals_required
      if (UserSlots::exist("WHERE user_id = $referralCodeUserId AND referrals_acquired = referrals_required") == true) {

         // get the referral slot
         $referralSlot = UserSlots::findOne("id, user_id, slot_id, slot_program, target_rank, referrals_acquired, rank, status, created_at, updated_at", "WHERE user_id = $referralCodeUserId");

         // if not at the final level
         if ($referralSlot['target_rank'] != "Trophy") {
            // unlock new level for the user i.e set the referrals required and the target rank
            // find the next available level
            $nextReferralLevel = ReferralLevels::findOne("referrals_required, rank", "WHERE rank = '" . $referralSlot['target_rank'] . "'");

            // find number of slots
            $noSlots = Slots::findOne("no_slots", "WHERE program = '". $referralSlot['slot_program'] ."'")['no_slots'];

            // update the next level
            UserSlots::update([
               "referrals_required" => (intval($noSlots) * intval($nextReferralLevel['referrals_required'])),
               "target_rank" => $nextReferralLevel['rank'],
               "rank" => $referralSlot['target_rank'],
            ], "WHERE user_id = $referralCodeUserId");

            // give the user his accrued benefits
            $referralLevel = ReferralLevels::findOne("cash_benefit, benefits, rank", "WHERE rank = '" . $referralSlot['rank'] . "'");
            UserBenefits::create([
               "user_id" => $referralCodeUserId,
               "achievement" => "Attained " . $referralLevel['rank'] . " Level.",
               "cash" => $referralLevel['cash_benefit'],
               "benefit" => $referralLevel['benefits']
            ]);

            // Notify the user
         }


      }


      if (!is_null($referralReferredBy) && $referralReferredBy != ORG_REFERRAL_CODE) {
         // the loop continues until an account that is opened under the organisation direct is met
         self::updateAllUplinks($referralReferredBy);
      }

   }
}
