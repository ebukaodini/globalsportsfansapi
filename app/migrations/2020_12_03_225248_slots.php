<?php
namespace Migrations;
use Library\Database\Schema;

class migration_2020_12_03_225248_slots {

   function migrate()
   {
      Schema::create('slots', function(Schema $schema) {
         $schema->int('id')->auto_increment()->primary();
         $schema->varchar('program', 50);
         $schema->int('no_slots'); // actual number of slot given
         $schema->double('cost'); // cost of acquiring this slot
         $schema->text('benefits'); // benefits attached
         $schema->timestamp('created_at');
         $schema->datetime('updated_at')->attribute("ON UPDATE CURRENT_TIMESTAMP");
      }, false, 'Slots');
   }

}
