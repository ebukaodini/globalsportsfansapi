<?php
namespace Migrations;
use Library\Database\Schema;

class migration_2020_12_16_233129_telephone_number_on_users_table_cannot_be_unique_and_null {

   function migrate()
   {

      Schema::alter('users', function(Schema $schema) {
         $schema->change('telephone')->varchar('telephone', 20)->nullable();
      }, false);

   }

}
