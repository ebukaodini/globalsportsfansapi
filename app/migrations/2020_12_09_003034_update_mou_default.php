<?php
namespace Migrations;
use Library\Database\Schema;

class migration_2020_12_09_003034_update_mou_default {

   function migrate()
   {
      Schema::alter('users', function(Schema $schema) {
         $schema->change('mou')->varchar('mou', 10)->default('accept'); // refuse
      }, false);
   }

}
