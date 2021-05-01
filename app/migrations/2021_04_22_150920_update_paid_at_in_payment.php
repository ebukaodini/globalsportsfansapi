<?php
namespace Migrations;
use Library\Database\Schema;

class migration_2021_04_22_150920_update_paid_at_in_payment {

   function migrate()
   {

      Schema::alter('payments', function(Schema $schema) {
         $schema->change('paid_at')->varchar('paid_at', 50)->nullable();
      }, false);

   }

}
