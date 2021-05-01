<?php
namespace Migrations;
use Library\Database\Schema;

class migration_2021_04_19_181019_update_favourite_teams_in_users {

   function migrate()
   {
      Schema::alter('users', function(Schema $schema) {
         $schema->change('favourite_team_local')->varchar('favourite_team_local', 200)->nullable();
         $schema->change('favourite_team_foreign')->varchar('favourite_team_foreign', 200)->nullable();
         $schema->change('favourite_team_international')->varchar('favourite_team_international', 200)->nullable();
         $schema->change('favourite_team_continental')->varchar('favourite_team_continental', 200)->nullable();
         $schema->change('favourite_team_worldcup')->varchar('favourite_team_worldcup', 200)->nullable();
         $schema->change('favourite_team_olympic')->varchar('favourite_team_olympic', 200)->nullable();
      }, false);
   }

}
