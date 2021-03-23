<?php
namespace Migrations;
use Library\Database\Schema;

class migration_2021_03_22_232719_add_nationality_to_user_table {

   function migrate()
   {

      Schema::alter('users', function(Schema $schema) {
         $schema->add()->varchar('nationality', 100)->nullable()->after('middlename');
      }, false);

   }

}
