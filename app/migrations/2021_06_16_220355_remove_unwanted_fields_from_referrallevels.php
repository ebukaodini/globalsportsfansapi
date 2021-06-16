<?php
namespace Migrations;
use Library\Database\Schema;

class migration_2021_06_16_220355_remove_unwanted_fields_from_referrallevels {

   function migrate()
   {
      Schema::alter('referral_levels', function(Schema $schema) {
         $schema->dropfield('benefits');
         $schema->dropfield('cash_benefit');
      }, false);
   }

}
