<?php
namespace Migrations;
use Library\Database\Schema;

class migration_2021_03_28_125351_alter_referral_level_to_varchar_in_userslots {

   function migrate()
   {
      
      Schema::alter('user_slots', function(Schema $schema) {
         $schema->change('referral_level')->varchar('referral_level', 10)->not_nullable();
      }, false);

   }

}
