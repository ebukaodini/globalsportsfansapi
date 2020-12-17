<?php
namespace Migrations;
use Library\Database\Schema;

class migration_2020_12_17_013401_add_target_rank_to_userslots {

   function migrate()
   {

      Schema::alter('user_slots', function(Schema $schema) {
         $schema->add()->varchar('target_rank', 20)->nullable()->after('referrals_required');
      }, false);

   }

}
