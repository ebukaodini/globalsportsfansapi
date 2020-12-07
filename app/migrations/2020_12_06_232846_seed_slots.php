<?php
namespace Migrations;
use Library\Database\Schema;

class migration_2020_12_06_232846_seed_slots {

   function migrate()
   {
      Schema::seed('slots', 
         [
            'program' => 'Local',
            'no_slots' => 1,
            'cost' => 50000,
            'benefits' => 'All benefits accrued are local to the home country'
         ],
         [
            'program' => 'Foreign',
            'no_slots' => 2,
            'cost' => 100000,
            'benefits' => 'Benefits include all-expense paid trip to home ground of one team supported outside home country'
         ],
         [
            'program' => 'International',
            'no_slots' => 3,
            'cost' => 150000,
            'benefits' => 'Benefits include all-expense paid trip to watch one home and away game of team supported in international competition'
         ],
         [
            'program' => 'Continental',
            'no_slots' => 4,
            'cost' => 200000,
            'benefits' => 'One week all-expense paid trip to watch one continental game involving your favourite club'
         ],
         [
            'program' => 'World Cup',
            'no_slots' => 5,
            'cost' => 250000,
            'benefits' => 'Two weeks all expense paid trip to watch one continental game involving your favourite club'
         ],
         [
            'program' => 'Olympic',
            'no_slots' => 6,
            'cost' => 300000,
            'benefits' => 'Two weeks all expense paid trip to watch home country at the olympics'
         ]
      );

   }

}
