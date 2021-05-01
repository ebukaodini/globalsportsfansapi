<?php
namespace Migrations;
use Library\Database\Schema;

class migration_2021_03_28_135552_add_referral_code_to_user_package {

   function migrate()
   {
      
      Schema::alter('user_package', function(Schema $schema) {
         $schema->add()->varchar('referral_code', 10)->not_nullable()->after('slot_program');
      }, false);

   }

}
