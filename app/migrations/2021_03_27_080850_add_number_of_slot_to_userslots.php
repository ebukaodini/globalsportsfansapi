<?php
namespace Migrations;
use Library\Database\Schema;

class migration_2021_03_27_080850_add_number_of_slot_to_userslots {

   function migrate()
   {
      
      Schema::alter('user_slots', function(Schema $schema) {
         $schema->add()->int('no_slots', 10)->not_nullable()->after('slot_id');
      }, false);

   }

}
