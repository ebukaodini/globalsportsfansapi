<?php
namespace Migrations;
use Library\Database\Schema;

class migration_2020_12_13_230708_extend_status_length_on_invoice {

   function migrate()
   {
      Schema::alter('invoice', function(Schema $schema) {
         $schema->change('status')->varchar('status', 20)->default('unpaid'); // unverified payment
      }, false);
   }

}
