<?php
namespace Migrations;
use Library\Database\Schema;

class migration_2020_12_17_021559_add_initial_requred_referrals_to_userslots {

   function migrate()
   {

      Schema::alter('user_slots', function(Schema $schema) {
         $schema->add()->int('initial_referrals_required')->nullable()->after('slot_program');
      }, false);

   }

}
