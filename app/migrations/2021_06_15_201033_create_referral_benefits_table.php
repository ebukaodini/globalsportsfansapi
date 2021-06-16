<?php

namespace Migrations;

use Library\Database\Schema;

class migration_2021_06_15_201033_create_referral_benefits_table
{

   function migrate()
   {
      Schema::create('referral_benefits', function (Schema $schema) {
         $schema->int('id')->auto_increment()->primary();
         $schema->int('referral_level_id')->index();
         $schema->int('slot_id')->index();
         $schema->double('cash');
         $schema->text('souvenir');
         $schema->timestamp('created_at')->attribute();
         $schema->datetime('updated_at')->attribute("ON UPDATE CURRENT_TIMESTAMP");
      }, false, 'ReferralBenefits');
   }
}
