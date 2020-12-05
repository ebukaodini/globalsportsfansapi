<?php
namespace Migrations;
use Library\Database\Schema;

class migration_2020_12_04_222353_notifications {

   function migrate()
   {
      Schema::create('notifications', function(Schema $schema) {
         $schema->int('id')->auto_increment()->primary();
         $schema->double('user_id');
         $schema->varchar('message', 200);
         $schema->varchar('route', 100);
         $schema->varchar('status', 10)->default('unread'); // read
         $schema->timestamp('created_at');
         $schema->datetime('updated_at')->attribute("ON UPDATE CURRENT_TIMESTAMP");
      }, false, 'Notifications');
   }

}