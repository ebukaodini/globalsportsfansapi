<?php
namespace Migrations;
use Library\Database\Schema;

class migration_2021_04_03_174253_correct_error_on_users {

   function migrate()
   {
      
      Schema::alter('users', function(Schema $schema) {
         $schema->change('favorite_team_local')->varchar('favourite_team_local', 50)->nullable();
         $schema->change('favorite_team_foreign')->varchar('favourite_team_foreign', 50)->nullable();
         $schema->change('favorite_team_international')->varchar('favourite_team_international', 50)->nullable();
         $schema->change('favorite_team_continental')->varchar('favourite_team_continental', 50)->nullable();
         $schema->change('favorite_team_worldcup')->varchar('favourite_team_worldcup', 50)->nullable();
         $schema->change('favorite_team_olympic')->varchar('favourite_team_olympic', 50)->nullable();
      }, false);
      
   }

}
