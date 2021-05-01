<?php
namespace Migrations;
use Library\Database\Schema;

class migration_2021_04_18_154910_add_slotpackage_to_competition {

   function migrate()
   {
      Schema::alter('competitions', function(Schema $schema) {
         $schema->add()->int('slotpackage')->after('id');
      }, false);
      
   }

}
