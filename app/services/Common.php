<?php

namespace Services;

use Models\Notifications;
use Models\ReferralBenefits;
use Models\Users;
use Models\UserPackage;
use Models\Slots;
use Models\ReferralLevels;
use Models\UserBenefits;
use Models\UserSlots;

class Common
{
   public static function generateReferralCode($length)
   {
      //'A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z',
      $options = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9', 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z'];
      $max = count($options);
      $code = "";
      for ($i = 0; $i < $length; $i++) {
         $code .= $options[rand(0, $max - 1)];
      }
      return $code;
   }

   public static $downlines = [];
   public static $limit = 7;

   // this is a recursive function
   public static function generateDownline(string $referralCode, int $referralLevel, int $gLevel, bool $isOrg = false)
   {
      // this is a limit to the downline level or tree generation
      // 0 is for the ORG
      if (($gLevel - 1) == self::$limit && $isOrg == false) return [];

      // get all the users: 
      // whose referredby is this referral_code
      // whose node level is greater than this node level
      // whose status is verified
      // who has bought a slot

      // $aGeneration = Users::findAll("firstname, lastname, middlename, telephone, email, profile_picture, referredby, referral_code, node_level", "WHERE referredby = '$referralCode' AND node_level > $referralLevel AND verification_status = 'verified'");

      // Refactor this to also check the user_package status
      $aGeneration = Users::findJoin("users.firstname, users.lastname, users.middlename, users.telephone, users.email, users.profile_picture, users.referredby, users.referral_code, users.node_level", "WHERE users.referredby = '$referralCode' AND users.node_level > $referralLevel AND users.verification_status = 'verified'")
         ->innerJoin("user_package", "users.id = user_package.user_id AND user_package.status = 'active'")
         ->join();

      // $aGeneration = Users::query("SELECT firstname, lastname, middlename, telephone, email, profile_picture, referredby, referral_code, node_level FROM users u, user_package p WHERE u.referredby = '$referralCode' AND u.node_level > $referralLevel AND u.verification_status = 'verified' AND u.id = p.user_id AND p.status = 'active' ");
      $downline = [];

      if ($aGeneration != false) {
         $count = 0;
         foreach ($aGeneration as $member) {
            $downline[$count] = $member;
            $downline[$count]['level'] = $gLevel;
            $referralCode = $member['referral_code'] ?: null;
            $nodelevel = intval($member['node_level']);
            $downline[$count]['downline'] = is_null($referralCode) ? [] : self::generateDownline($referralCode, $nodelevel, ($gLevel + 1), $isOrg);
            $count++;
         }
      }

      return $downline;
   }

   public static function updateReferralUplink(string $referralCode, int $nodeCapLevel, int $baseNodeLevel, bool $directUpLink = true)
   {
      // get the user that referred me
      $owner = Users::findOne("id, email, firstname, lastname , referredby", "WHERE referral_code = '$referralCode'");
      // $ownerName = "{$owner['firstname']} {$owner['lastname']}";
      $ownerEmail = $owner['email'];
      $referralsReferralCode = $owner['referredby'] ?: null;

      // if referred by the organisation
      if ($referralsReferralCode == ORG_REFERRAL_CODE || is_null($referralsReferralCode)) return;

      // get his id
      $user = Users::findOne('*', "WHERE referral_code = '$referralsReferralCode'");
      $referralsUserId = $user['id'];

      // restrict access to members beyond node cap level
      // if the node level of the user that referred me is less than to the referral cap level
      if (Users::exist("WHERE id = $referralsUserId AND node_level < $nodeCapLevel")) return;
      // exit("passed $nodeCapLevel {$user['node_level']}");

      // get the user package of the user that referred me
      $userPackage = UserPackage::findOne("id, no_slots, initial_referrals_required, target_rank, rank, status", "WHERE referral_code = '$referralsReferralCode'");

      // instantiate active slot
      $userActiveSlot = [];

      if ($directUpLink == true) {

         // get the user's active slot
         $userActiveSlot = UserSlots::findOne("*", "WHERE referral_code = '$referralsReferralCode' AND referral_level = 'COACH' AND status = 'active' AND referrals_acquired < referrals_required");

         // no active slot for this user
         if ($userActiveSlot == false) {
            error_log('no active slot for this user');
            return;
         }
         $referralsReq = intval($userActiveSlot['referrals_required']);
         $referralsAcq = intval($userActiveSlot['referrals_acquired']);

         // increment the referrals_acquired
         $referralsAcq++;
         UserSlots::update([
            "referrals_acquired" => $referralsAcq,
         ], "WHERE id = {$userActiveSlot['id']}");

         // notify the user that his referral acquired increased
         self::notify($referralsUserId, "$ownerEmail has joined Sports Fans Club with your referral code and is now part of your downline.", '/downlines');

         // NOTE: because of the new design
         // there are needs for extra referrals on top of the regular referral required for members at coach level
         // Those extra referrals are needed by the organization to increase membership
         // Now every extra referral is registered under the organization and not under the referral
         // Hence we compare this user's referrals_required, referrals_acquired 
         // with the base requirement for the coach level, which is 5
         // If acquired is greater than the base, then the referred user is an extra_referral
         // Hence, his/her referred_by should be the organization's referral code which is SPORTFANS

         $baseReferralLevel = ReferralLevels::findOne("referrals_required", "WHERE rank = 'Coach'")['referrals_required'];

         if ($referralsAcq > $baseReferralLevel) {
            Users::update([
               "referredby" => ORG_REFERRAL_CODE,
            ], "WHERE email = '$ownerEmail'");
         }

         // check for completed slots
         if ($referralsAcq == $referralsReq) {
            // update the status to completed
            UserSlots::update([
               "status" => 'completed'
            ], "WHERE id = {$userActiveSlot['id']}");

            // notify the user that this slot has been completed
            self::notify($referralsUserId, "Congratulations!!! Your {$userActiveSlot['referral_level']} slot has been completed.", '/slots');

            // get referral benefits for the referral level of the user slot
            // $benefits = ReferralLevels::findOne("id, rank, cash_benefit, benefits", "WHERE rank = '{$userActiveSlot['referral_level']}' ");

            // refactored
            $referralLevel = ReferralLevels::findOne("id, rank", "WHERE rank = '{$userActiveSlot['referral_level']}'");
            $referralBenefit = ReferralBenefits::query("SELECT cash, souvenir FROM referral_benefits WHERE referral_level_id = {$referralLevel['id']} AND slot_id = (SELECT slot_id FROM user_package WHERE user_id = $referralsUserId) LIMIT 1", true)[0];

            // set benefits for the user
            // set the cash benefits
            UserBenefits::create([
               "user_id" => $referralsUserId,
               "achievement" => 'Completed a slot in ' . $userActiveSlot['referral_level'],
               // "cash" => floatval($benefits['cash_benefit'])
               // refactored
               "cash" => floatval($referralBenefit['cash'])
            ]);

            // notify user of benefit
            self::notify($referralsUserId, "Congratulations!!! Your cash benefit of ₦{$referralBenefit['cash']} is due for accrual for completing a slot in {$userActiveSlot['referral_level']} level.", '/benefits');
            // notify admin of benefit
            self::notify(0, "Notice! {$user['firstname']} {$user['lastname']} ({$user['email']}) has completed a slot at {$userActiveSlot['referral_level']} level and has been accrued a cash benefit of ₦{$referralBenefit['cash']}.", '/benefits');

            // check if all slots have been completed
            $completedAllSlotsInThisLevel = count(UserSlots::findAll("id", "WHERE referral_code = '$referralsReferralCode' AND node_level = {$userActiveSlot['node_level']} AND referral_level = '{$userActiveSlot['referral_level']}' AND status = 'completed' ")) == intval($userPackage['no_slots']);

            // make a slot in the next level active
            if ($completedAllSlotsInThisLevel == true) {

               // if this slot is the last slot in this level, set the souvenir benefits
               UserBenefits::create([
                  "user_id" => $referralsUserId,
                  "achievement" => "Completed all {$userPackage['no_slots']} slots in " . $userActiveSlot['referral_level'],
                  // "benefit" => $benefits['benefits']

                  // refactored
                  "benefit" => $referralBenefit['souvenir']
               ]);

               // notify user of benefit
               self::notify($referralsUserId, "Congratulations!!! Your souvenir reward for completing all slots in {$userActiveSlot['referral_level']} is due.", '/benefits');
               // notify admin of benefit
               self::notify(0, "Notice! {$user['firstname']} {$user['lastname']} ({$user['email']}) has completed all slots at {$userActiveSlot['referral_level']} and has been accrued a souvenir reward.", '/benefits');

               // new node level
               $newNodeLevel = intval($userActiveSlot['node_level']) + 1;

               // next target rank // Players
               // $nextTargetRank = ReferralLevels::findOne("rank", "WHERE id > {$benefits['id']}")['rank']; // refactored
               $nextTargetRank = ReferralLevels::findOne("rank", "WHERE id > {$referralLevel['id']}")['rank'];

               // make a slot in the next level active
               UserSlots::update([
                  "status" => 'active'
               ], "WHERE referral_code = '$referralsReferralCode' AND referral_level = '$nextTargetRank' AND node_level = '$newNodeLevel' AND status = 'pending' ORDER BY id ASC LIMIT 1");

               // update the user package with the new rank and the new target rank
               UserPackage::update([
                  "target_rank" => $nextTargetRank,
                  "rank" => $userPackage['target_rank'],
               ], "WHERE referral_code = '$referralsReferralCode' AND user_id = $referralsUserId ");
            } else {

               // make the next slot in this level active
               UserSlots::update([
                  "status" => 'active'
               ], "WHERE referral_code = '$referralsReferralCode' AND referral_level = '{$userActiveSlot['referral_level']}' AND node_level = '{$userActiveSlot['node_level']}' AND status = 'pending' ORDER BY id ASC LIMIT 1");
            }
         }
      } else {
         // this is not the direct uplink
         // here the active slot is going to be one of this user's slot at this $baseNodeLevel

         // get this user's slot that has the same node_level as the base node level
         $userActiveSlot = UserSlots::findOne("id, referrals_required, referrals_acquired, referral_level, update_uplink, node_level, status", "WHERE referral_code = '$referralsReferralCode' AND node_level = $baseNodeLevel AND referrals_acquired < referrals_required ORDER BY id ASC");

         // no active slot for this user
         if ($userActiveSlot == false) {
            // this condition would never occur
            // because every member only gives up one of his slot at every level for updating uplinks
            error_log('no active slot for this user');
            return;
         }
         $referralsReq = intval($userActiveSlot['referrals_required']);
         $referralsAcq = intval($userActiveSlot['referrals_acquired']);

         // increment the referrals_acquired
         $referralsAcq++;

         UserSlots::update([
            "referrals_acquired" => $referralsAcq,
         ], "WHERE id = {$userActiveSlot['id']}");

         // notify the user that his referral acquired increased
         self::notify($referralsUserId, "$ownerEmail has joined Sports Fans Club with your referral code and is now part of your downline.", '/downlines');

         // check for completed slots
         if ($referralsAcq == $referralsReq) {
            // referrals acquired would never be equal to referrals required if this slot is not the active slot
            // and the slots in this level cannot be active if the slots in the previous levels were not completed

            // * what if:
            // I have two slots in this level and in the previous level,
            // The second slot in the previous level is not completed and hence the active slot.
            // One slot in this level just got completed, of course the completion of one slot in the previous level is enough to complete on slot here - YES
            // how do we account for this

            // get referral benefits for the referral level of the user slot
            // $benefits = ReferralLevels::findOne("id, rank, cash_benefit, benefits", "WHERE rank = '{$userActiveSlot['referral_level']}' ");

            // refactored
            $referralLevel = ReferralLevels::findOne("id, rank", "WHERE rank = '{$userActiveSlot['referral_level']}'");
            $referralBenefit = ReferralBenefits::query("SELECT cash, souvenir FROM referral_benefits WHERE referral_level_id = {$referralLevel['id']} AND slot_id = (SELECT slot_id FROM user_package WHERE user_id = $referralsUserId) LIMIT 1", true)[0];

            // if the slot completed is the active slot
            if ($userPackage['rank'] == $userActiveSlot['referral_level']) {
               // update the status to completed
               UserSlots::update([
                  "status" => 'completed'
               ], "WHERE id = {$userActiveSlot['id']}");

               // notify the user
               self::notify($referralsUserId, "Congratulations!!! Your {$userActiveSlot['referral_level']} slot has been completed.", '/slots');

               // set benefits for the user
               // set the cash benefits
               UserBenefits::create([
                  "user_id" => $referralsUserId,
                  "achievement" => 'Completed a slot in ' . $userActiveSlot['referral_level'],
                  // "cash" => floatval($benefits['cash_benefit']) refactored
                  "cash" => floatval($referralBenefit['cash'])
               ]);

               // notify user of benefit
               self::notify($referralsUserId, "Congratulations!!! Your cash benefit of ₦{$referralBenefit['cash']} is due for accrual for completing a slot in {$userActiveSlot['referral_level']} level.", '/benefits');
               // notify admin of benefit
               self::notify(0, "Notice! {$user['firstname']} {$user['lastname']} ({$user['email']}) has completed a slot at {$userActiveSlot['referral_level']} level and has been accrued a cash benefit of ₦{$referralBenefit['cash']}.", '/benefits');
            }

            // check if all slots have been completed
            $completedAllSlotsInThisLevel = count(UserSlots::findAll("id", "WHERE referral_code = '$referralsReferralCode' AND node_level = {$userActiveSlot['node_level']} AND referral_level = '{$userActiveSlot['referral_level']}' AND status = 'completed' ")) == intval($userPackage['no_slots']);

            // make a slot in the next level active
            if ($completedAllSlotsInThisLevel == true) {

               // if the slot completed is the active slot
               if ($userPackage['rank'] == $userActiveSlot['referral_level']) {
                  // if this slot is the last slot in this level, set the souvenir benefits
                  UserBenefits::create([
                     "user_id" => $referralsUserId,
                     "achievement" => "Completed all {$userPackage['no_slots']} slots in " . $userActiveSlot['referral_level'],
                     "benefit" => $referralBenefit['souvenir']
                  ]);

                  // notify user of benefit
                  self::notify($referralsUserId, "Congratulations!!! Your souvenir reward for completing all slots in {$userActiveSlot['referral_level']} is due.", '/benefits');
                  // notify admin of benefit
                  self::notify(0, "Notice! {$user['firstname']} {$user['lastname']} ({$user['email']}) has completed all slots at {$userActiveSlot['referral_level']} and has been accrued a souvenir reward.", '/benefits');
               }

               // TODO: why not use the value directly (i.e. Trophy)
               // if the user package rank is the last rank on the referral levels. i.e Trophy
               if ($userPackage['rank'] == ReferralLevels::findOne("rank", "ORDER BY id DESC")['rank']) {
                  // notify admin that this user have completed his program
                  self::notify(0, "Notice! {$user['firstname']} {$user['lastname']} ({$user['email']}) has completed all slots at all levels. 🎉🎉🎉", '/benefits');
                  return;
               }

               // else make a slot in the next level active
               // how, it could be filled already
               // get all slots, and make the one that is incomplete the active one
               // NOTE: all slots cannot be incomplete until the slots in the level above are completed

               // new node level
               $newNodeLevel = intval($userActiveSlot['node_level']) + 1;
               // next target rank
               $nextTargetRank = ReferralLevels::findOne("rank", "WHERE id > {$referralLevel['id']}")['rank'];

               // find all slots in the next level
               $allNextLevelSlots = UserSlots::findAll("*", "WHERE referral_code = '$referralsReferralCode' AND referral_level = '$nextTargetRank' AND node_level = '$newNodeLevel' AND status = 'pending' ORDER BY id ASC");

               foreach ($allNextLevelSlots as $slot) {
                  // if slot is completed
                  if ($slot['referrals_acquired'] == $slot['referrals_required']) {
                     // get referral benefits for the referral level of the user slot
                     // $newLevelBenefits = ReferralLevels::findOne("id, rank, cash_benefit, benefits", "WHERE rank = '{$slot['referral_level']}' ");

                     // refactored
                     $referralLevel = ReferralLevels::findOne("id, rank", "WHERE rank = '{$slot['referral_level']}'");
                     $newLevelBenefit = ReferralBenefits::query("SELECT cash, souvenir FROM referral_benefits WHERE referral_level_id = {$referralLevel['id']} AND slot_id = (SELECT slot_id FROM user_package WHERE user_id = $referralsUserId) LIMIT 1", true)[0];

                     // update the status to completed
                     UserSlots::update([
                        "status" => 'completed'
                     ], "WHERE id = {$slot['id']}");

                     // notify the user that this slot has been completed
                     self::notify($referralsUserId, "Congratulations!!! Your {$slot['referral_level']} slot has been completed.", '/slots');

                     // set benefits for the user
                     // set the cash benefits
                     UserBenefits::create([
                        "user_id" => $referralsUserId,
                        "achievement" => 'Completed a slot in ' . $slot['referral_level'],
                        // "cash" => floatval($newLevelBenefits['cash_benefit']) refactored
                        "cash" => floatval($newLevelBenefit['cash'])
                     ]);

                     // notify user of benefit
                     self::notify($referralsUserId, "Congratulations!!! Your cash benefit of ₦{$newLevelBenefit['cash']} is due for accrual for completing a slot in {$slot['referral_level']} level.", '/benefits');
                     // notify admin of benefit
                     self::notify(0, "Notice! {$user['firstname']} {$user['lastname']} ({$user['email']}) has completed a slot at {$slot['referral_level']} level and has been accrued a cash benefit of ₦{$newLevelBenefit['cash']}.", '/benefits');
                  } else {
                     // this slot is not completed

                     // update the status to active
                     UserSlots::update([
                        "status" => 'active'
                     ], "WHERE id = {$slot['id']}");

                     // notify the user that his referral acquired increased
                     self::notify($referralsUserId, "Your slot at {$slot['referral_level']} level has gotten a new member.", '/downlines');

                     // because other slots in this level would definitely not have any activity yet,
                     // break the loop and leave them to remain pending...
                     break;
                  }
               }
            } else {

               // if the slot completed is the active slot
               if ($userPackage['rank'] == $userActiveSlot['referral_level']) {
                  // make the next slot in this level active
                  UserSlots::update([
                     "status" => 'active'
                  ], "WHERE referral_code = '$referralsReferralCode' AND referral_level = '{$userActiveSlot['referral_level']}' AND node_level = '{$userActiveSlot['node_level']}' AND status = 'pending' ORDER BY id ASC LIMIT 1");
               }
            }
         }
      }

      // call this method again if:
      // the current active rank allows for update_uplink
      if ($userActiveSlot['update_uplink'] == true) {
         // the loop continues until a user whose node level is < the nodeCapLevel
         self::updateReferralUplink($referralsReferralCode, $nodeCapLevel, $baseNodeLevel, false);
      }
   }

   public static function notify(int $userId, string $message, string $route = "")
   {

      if ($userId == 0) {
         $name = "Admin";
         $email = "support@sportsfansng.com";
      } else {
         $user = Users::findOne("email, firstname", "WHERE id = $userId");
         $name = $user['firstname'];
         $email = $user['email'];
      }

      Mail::asHTML(
         "<h4>Hi $name,</h4>
         <p>$message</p>"
      )->send(ORG_EMAIL, "$email", "Notification from Sports Fans Club", "support@sportsfansng.com");

      // echo "$userId, $message, $route";
      return Notifications::create([
         "user_id" => utf8_encode($userId),
         "message" => utf8_encode($message),
         "route" => utf8_encode($route)
      ]);
   }

   public static $extraReferrals = [
      "Local" => 0,
      "Foreign" => 1,
      "International" => 2,
      "Continental" => 3,
      "World Cup" => 4,
      "Olympic" => 5
   ];

   public static $totalReferrals = [
      "Local" => 5,
      "Foreign" => 6,
      "International" => 7,
      "Continental" => 8,
      "World Cup" => 9,
      "Olympic" => 10
   ];

   // function to get the referral benefits of a user
   public static function getUserReferralBenefit($userId, $referralLevel)
   {
   }
}
