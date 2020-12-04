<?php
namespace Migrations;
use Library\Database\Schema;

function migrate()
{
   Schema::create('user_slots', function(Schema $schema) {
      $schema->int('id')->auto_increment()->primary();
      $schema->double('user_id');
      $schema->int('slot_id');
      $schema->int('slot_program');
      $schema->varchar('referral_code', 10)->unique();
      $schema->int('referrals');
      $schema->varchar('rank', 20);
      $schema->timestamp('created_at');
      $schema->datetime('updated_at')->attribute("ON UPDATE CURRENT_TIMESTAMP");
      $schema->foreign('user_id', 'users', 'id', 'ON DELETE RESTRICT', 'ON UPDATE CASCADE');
   }, false, 'UserSlots');
}
