<?php
namespace Migrations;
use Library\Database\Schema;

class migration_2020_12_05_104741_update_user_member_id {

   function migrate()
   {
      Schema::alter('users', function(Schema $schema) {
         $schema->change('member_id')->varchar('member_id', 10)->nullable()->unique();
      }, false);
   }

}
