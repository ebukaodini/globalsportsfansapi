<?php
namespace Migrations;
use Library\Database\Schema;

class migration_2020_12_04_224640_permissions {

   function migrate()
   {
      Schema::create('permissions', function(Schema $schema) {
         $schema->int('id')->auto_increment()->primary();
         $schema->varchar('role', 20);
         $schema->text('permissions');
         $schema->timestamp('created_at');
         $schema->datetime('updated_at')->attribute("ON UPDATE CURRENT_TIMESTAMP");
      }, false, 'Permissions');
   }

}