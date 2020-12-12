<?php
namespace Migrations;
use Library\Database\Schema;

class migration_2020_12_12_115556_update_users_default_memberid_to_null {

   function migrate()
   {
      Schema::alter('users', function(Schema $schema) {
         $schema->change('member_id')->varchar('member_id', 10)->nullable();
      }, false);
   }

}
