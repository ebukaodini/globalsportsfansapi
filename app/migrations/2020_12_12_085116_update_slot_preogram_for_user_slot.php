<?php
namespace Migrations;
use Library\Database\Schema;

class migration_2020_12_12_085116_update_slot_preogram_for_user_slot {

   function migrate()
   {
      Schema::alter('user_slots', function(Schema $schema) {
         $schema->change('slot_program')->varchar('slot_program', 50);
      }, false);
   }

}
