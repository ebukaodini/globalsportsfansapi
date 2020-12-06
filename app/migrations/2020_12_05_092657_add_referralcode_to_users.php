<?php
namespace Migrations;
use Library\Database\Schema;

class migration_2020_12_05_092657_add_referralcode_to_users {

   function migrate()
   {
      Schema::alter('users', function(Schema $schema) {
         $schema->add()->varchar('referredby', 10)->nullable()->after('favorite_team');
      }, false);
   }

}
