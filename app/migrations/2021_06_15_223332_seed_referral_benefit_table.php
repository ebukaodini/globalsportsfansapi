<?php
namespace Migrations;
use Library\Database\Schema;

class migration_2021_06_15_223332_seed_referral_benefit_table {

   function migrate()
   {
      Schema::seed('referral_benefits', 
         [
            'referral_level_id' => '1',
            'slot_id' => '1',
            'cash' => '0',
            'souvenir' => 'N/A'
         ],
         [
            'referral_level_id' => '1',
            'slot_id' => '2',
            'cash' => '0',
            'souvenir' => 'DSTV compact plus'
         ],
         [
            'referral_level_id' => '1',
            'slot_id' => '3',
            'cash' => '0',
            'souvenir' => 'DSTV premium'
         ],
         [
            'referral_level_id' => '1',
            'slot_id' => '4',
            'cash' => '0',
            'souvenir' => 'DSTV premium'
         ],
         [
            'referral_level_id' => '1',
            'slot_id' => '5',
            'cash' => '0',
            'souvenir' => 'DSTV premium'
         ],
         [
            'referral_level_id' => '1',
            'slot_id' => '6',
            'cash' => '0',
            'souvenir' => 'DSTV premium'
         ],


         [
            'referral_level_id' => '2',
            'slot_id' => '1',
            'cash' => '0',
            'souvenir' => 'DSTV compact'
         ],
         [
            'referral_level_id' => '2',
            'slot_id' => '2',
            'cash' => '4500',
            'souvenir' => 'N/A'
         ],
         [
            'referral_level_id' => '2',
            'slot_id' => '3',
            'cash' => '13500',
            'souvenir' => 'N/A'
         ],
         [
            'referral_level_id' => '2',
            'slot_id' => '4',
            'cash' => '18000',
            'souvenir' => 'N/A'
         ],
         [
            'referral_level_id' => '2',
            'slot_id' => '5',
            'cash' => '22500',
            'souvenir' => 'N/A'
         ],
         [
            'referral_level_id' => '2',
            'slot_id' => '6',
            'cash' => '27000',
            'souvenir' => 'N/A'
         ],


         [
            'referral_level_id' => '3',
            'slot_id' => '1',
            'cash' => '4500',
            'souvenir' => 'N/A'
         ],
         [
            'referral_level_id' => '3',
            'slot_id' => '2',
            'cash' => '22500',
            'souvenir' => '1 month subscription'
         ],
         [
            'referral_level_id' => '3',
            'slot_id' => '3',
            'cash' => '40500',
            'souvenir' => '1 month subscription'
         ],
         [
            'referral_level_id' => '3',
            'slot_id' => '4',
            'cash' => '72000',
            'souvenir' => '2 month subscription'
         ],
         [
            'referral_level_id' => '3',
            'slot_id' => '5',
            'cash' => '122000',
            'souvenir' => '3 month subscription'
         ],
         [
            'referral_level_id' => '3',
            'slot_id' => '6',
            'cash' => '162000',
            'souvenir' => '4 month subscription'
         ],


         [
            'referral_level_id' => '4',
            'slot_id' => '1',
            'cash' => '22500',
            'souvenir' => '1 month subscription'
         ],
         [
            'referral_level_id' => '4',
            'slot_id' => '2',
            'cash' => '45000',
            'souvenir' => '2 months subscription'
         ],
         [
            'referral_level_id' => '4',
            'slot_id' => '3',
            'cash' => '120000',
            'souvenir' => '2 months subscription'
         ],
         [
            'referral_level_id' => '4',
            'slot_id' => '4',
            'cash' => '288000',
            'souvenir' => '3 months subscription'
         ],
         [
            'referral_level_id' => '4',
            'slot_id' => '5',
            'cash' => '562000',
            'souvenir' => '4 months subscription'
         ],
         [
            'referral_level_id' => '4',
            'slot_id' => '6',
            'cash' => '972000',
            'souvenir' => '5 months subscription'
         ],


         [
            'referral_level_id' => '5',
            'slot_id' => '1',
            'cash' => '150000',
            'souvenir' => '2 months subscription'
         ],
         [
            'referral_level_id' => '5',
            'slot_id' => '2',
            'cash' => '250000',
            'souvenir' => '3 months subscription'
         ],
         [
            'referral_level_id' => '5',
            'slot_id' => '3',
            'cash' => '360000',
            'souvenir' => '3 months subscription'
         ],
         [
            'referral_level_id' => '5',
            'slot_id' => '4',
            'cash' => '480000',
            'souvenir' => '4 months subscription'
         ],
         [
            'referral_level_id' => '5',
            'slot_id' => '5',
            'cash' => '900000',
            'souvenir' => '5 months subscription'
         ],
         [
            'referral_level_id' => '5',
            'slot_id' => '6',
            'cash' => '1200000',
            'souvenir' => '6 months subscription'
         ],


         [
            'referral_level_id' => '6',
            'slot_id' => '1',
            'cash' => '500000',
            'souvenir' => 'Trip to home ground of local club supported to watch live game'
         ],
         [
            'referral_level_id' => '6',
            'slot_id' => '2',
            'cash' => '750000',
            'souvenir' => 'Trip to home ground of local club supported to watch live game'
         ],
         [
            'referral_level_id' => '6',
            'slot_id' => '3',
            'cash' => '1000000',
            'souvenir' => 'Trip to home ground of local club supported to watch live game'
         ],
         [
            'referral_level_id' => '6',
            'slot_id' => '4',
            'cash' => '2000000',
            'souvenir' => '2 weeks all expense paid trip to watch Continental competition involving your country'
         ],
         [
            'referral_level_id' => '6',
            'slot_id' => '5',
            'cash' => '3000000',
            'souvenir' => '2 weeks all expense paid trip to watch World Cup competition involving your country'
         ],
         [
            'referral_level_id' => '6',
            'slot_id' => '6',
            'cash' => '3000000',
            'souvenir' => '2 weeks all expense paid trip to watch Olypic competition involving your country'
         ],


         [
            'referral_level_id' => '7',
            'slot_id' => '1',
            'cash' => '1500000',
            'souvenir' => 'N/A'
         ],
         [
            'referral_level_id' => '7',
            'slot_id' => '2',
            'cash' => '3000000',
            'souvenir' => 'N/A'
         ],
         [
            'referral_level_id' => '7',
            'slot_id' => '3',
            'cash' => '4500000',
            'souvenir' => 'N/A'
         ],
         [
            'referral_level_id' => '7',
            'slot_id' => '4',
            'cash' => '6000000',
            'souvenir' => 'N/A'
         ],
         [
            'referral_level_id' => '7',
            'slot_id' => '5',
            'cash' => '7500000',
            'souvenir' => 'N/A'
         ],
         [
            'referral_level_id' => '7',
            'slot_id' => '6',
            'cash' => '9000000',
            'souvenir' => 'N/A'
         ],

      );
   }

}
