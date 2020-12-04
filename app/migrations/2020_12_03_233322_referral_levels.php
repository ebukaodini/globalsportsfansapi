<?php
namespace Migrations;
use Library\Database\Schema;

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
