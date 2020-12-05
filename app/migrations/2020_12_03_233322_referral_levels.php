<?php
namespace Migrations;
use Library\Database\Schema;

class migration_2020_12_03_233322_referral_levels {

   function migrate()
   {
      Schema::create('referral_levels', function(Schema $schema) {
         $schema->int('id')->auto_increment()->primary();
         $schema->int('referrals_required');
         $schema->varchar('rank', 20);
         $schema->double('cash_benefit');
         $schema->text('benefits');
         $schema->timestamp('created_at')->attribute();
         $schema->datetime('updated_at')->attribute("ON UPDATE CURRENT_TIMESTAMP");
      }, false, 'ReferralLevels');
   }

}