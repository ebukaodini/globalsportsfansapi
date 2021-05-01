<?php
namespace Migrations;
use Library\Database\Schema;

class migration_2021_03_28_070414_create_user_slot {

   function migrate()
   {

      // update the referral level to node level
      Schema::alter('users', function(Schema $schema) {
         $schema->change('referral_level')->int('node_level', 10)->nullable();
      }, false);
      
      // update the user package and remove some fields
      Schema::alter('user_package', function(Schema $schema) {
         $schema->dropfield('referrals_required');
         $schema->dropfield('referrals_acquired');
      }, false);

      // create the new user slots
      Schema::create('user_slots', function(Schema $schema) {
         $schema->int('id')->auto_increment()->primary();
         $schema->double('user_id');
         $schema->double('user_package_id');
         $schema->int('no_slots');
         $schema->varchar('referral_code', 10);
         $schema->int('referral_level'); // the level/rank for which the slot was created for
         $schema->int('referrals_required')->default('0');
         $schema->int('referrals_acquired')->default('0');
         $schema->varchar('status', 10)->default('pending'); // active, completed
         // user's slot is created with pending status, then when referrals start getting allocated:
         // if the slot is the only slot, it is set to active
         // this is so that a user with more than one slot do not have more than one active slot
         $schema->timestamp('created_at');
         $schema->datetime('updated_at')->attribute("ON UPDATE CURRENT_TIMESTAMP");
         $schema->foreign('user_id', 'users', 'id', 'ON DELETE RESTRICT', 'ON UPDATE CASCADE');
         $schema->foreign('user_package_id', 'user_package', 'id', 'ON DELETE RESTRICT', 'ON UPDATE CASCADE');
      }, false, 'UserSlots');
   }

}
