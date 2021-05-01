<?php
namespace Migrations;
use Library\Database\Schema;

class migration_2021_03_29_035526_update_notification_schema {

   function migrate()
   {
      
      Schema::alter('notifications', function(Schema $schema) {
         $schema->change('route')->varchar('route', 100)->nullable();
      }, false);

   }

}
