<?php
namespace Migrations;
use Library\Database\Schema;

class migration_2020_12_03_232056_user_slots {

   function migrate()
   {
      Schema::create('user_slots', function(Schema $schema) {
         $schema->int('id')->auto_increment()->primary();
         $schema->double('user_id');
         $schema->int('slot_id');
         $schema->int('slot_program');
         $schema->varchar('referral_code', 10)->unique();
         // at what point is this number updated
         // whenever the referrals acquired is equal to the referrals required,
         // then the accrued benefits is sent for approval
         // then the next level is set for this user-slot (update the referrals required)
         $schema->int('referrals_required')->default('0');
         $schema->int('referrals_acquired')->default('0');
         $schema->varchar('referredby', 10); // referral code of who referred you / referral code of organisation
         $schema->int('referral_level'); // level of the slot on the slot tree
         $schema->varchar('rank', 20)->default('Manager');
         $schema->varchar('status', 10)->default('pending'); // activated, blocked, completed
         $schema->timestamp('created_at');
         $schema->datetime('updated_at')->attribute("ON UPDATE CURRENT_TIMESTAMP");
         $schema->foreign('user_id', 'users', 'id', 'ON DELETE RESTRICT', 'ON UPDATE CASCADE');
         $schema->foreign('slot_id', 'slots', 'id', 'ON DELETE RESTRICT', 'ON UPDATE CASCADE');
         $schema->foreign('slot_program', 'slots', 'program', 'ON DELETE RESTRICT', 'ON UPDATE CASCADE');
      }, false, 'UserSlots');
   }

}