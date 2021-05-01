<?php
namespace Migrations;
use Library\Database\Schema;

class migration_2021_04_03_170218_update_users_with_favourite_team_for_diff_package {

   function migrate()
   {
      Schema::alter('users', function(Schema $schema) {
         // drop the current fav team
         $schema->dropfield('favourite_team');

         // create fields for the fav team for diff packages
         $schema->add()->varchar('favorite_team_local', 50)->after('favourite_sport')->nullable();
         $schema->add()->varchar('favorite_team_foreign', 50)->after('favorite_team_local')->nullable();
         $schema->add()->varchar('favorite_team_international', 50)->after('favorite_team_foreign')->nullable();
         $schema->add()->varchar('favorite_team_continental', 50)->after('favorite_team_international')->nullable();
         $schema->add()->varchar('favorite_team_worldcup', 50)->after('favorite_team_continental')->nullable();
         $schema->add()->varchar('favorite_team_olympic', 50)->after('favorite_team_worldcup')->nullable();
      }, false);
   }

}
