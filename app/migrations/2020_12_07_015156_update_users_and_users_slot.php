<?php
namespace Migrations;
use Library\Database\Schema;

class migration_2020_12_07_015156_update_users_and_users_slot {

   function migrate()
   {
      Schema::alter('users_slot', function(Schema $schema) {
         $schema->dropfield('referral_code');
         $schema->dropfield('referredby');
         $schema->dropfield('referral_level');
      }, false);

      Schema::alter('users', function(Schema $schema) {
         $schema->add()->varchar('referral_code', 10)->nullable()->after('referredby');//->unique();
         $schema->add()->int('referral_level')->nullable()->after('referral_code');
      }, false);


      
   }

}
