<?php
namespace Migrations;
use Library\Database\Schema;

class migration_2020_12_12_090611_seed_referral_levels {

   function migrate()
   {
      Schema::seed('referral_levels', 
         [
            'referrals_required' => 0,
            'rank' => 'Manager',
            'cash_benefit' => 0.00,
            'benefits' => 'DSTV package and premium subscription'
         ],
         [
            'referrals_required' => 5,
            'rank' => 'Coach',
            'cash_benefit' => 11250.00,
            'benefits' => 'None'
         ],
         [
            'referrals_required' => 25,
            'rank' => 'Players',
            'cash_benefit' => 56250.00,
            'benefits' => 'Six months DSTV premium subscription'
         ],
         [
            'referrals_required' => 125,
            'rank' => 'Supporters',
            'cash_benefit' => 281250.00,
            'benefits' => 'Six months DSTV premium subscription'
         ],
         [
            'referrals_required' => 625,
            'rank' => 'Stadium',
            'cash_benefit' => 1406250.00,
            'benefits' => 'One year DSTV premium subscription and favourite team souvenirs'
         ],
         [
            'referrals_required' => 3125,
            'rank' => 'Fans',
            'cash_benefit' => 7031250.00,
            'benefits' => 'All expense paid trip to home ground of favouruite club to watch one live match'
         ],
         [
            'referrals_required' => 15625,
            'rank' => 'Trophy',
            'cash_benefit' => 35156250.00,
            'benefits' => 'Exit from the tournament with Trophy (Parting Gift)'
         ]
      );
   }

}
