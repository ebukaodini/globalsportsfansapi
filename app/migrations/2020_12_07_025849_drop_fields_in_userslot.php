<?php
namespace Migrations;
use Library\Database\Schema;

class migration_2020_12_07_025849_drop_fields_in_userslot {

   function migrate()
   {
      Schema::alter('user_slots', function(Schema $schema) {
         $schema->dropfield('referral_code');
         $schema->dropfield('referredby');
         $schema->dropfield('referral_level');
      }, false);
   }

}
