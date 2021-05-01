<?php
namespace Migrations;
use Library\Database\Schema;

class migration_2021_03_28_152527_add_node_level_to_userslot {

   function migrate()
   {
      Schema::alter('user_slots', function(Schema $schema) {
         // this was added so that the user whose downline is being updated get to increment his slot referrel_acquired at this node level
         // this node level is gotten by incrementing on the user's node level
         $schema->add()->double('node_level')->not_nullable()->after('referral_level');
         // this was added to indicate if the slot can update the uplink after being updated
         // this is set to true for the first slot only, and false for every other slot
         $schema->add()->boolean('update_uplink')->not_nullable()->after('referrals_acquired'); // true, false
      }, false);

      Schema::alter('users', function(Schema $schema) {
         $schema->change('node_level')->double('node_level')->nullable();
         $schema->dropfield('mou');
      }, false);
   }

}
