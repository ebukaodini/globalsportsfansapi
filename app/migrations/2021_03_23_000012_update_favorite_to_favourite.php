<?php
namespace Migrations;
use Library\Database\Schema;

class migration_2021_03_23_000012_update_favorite_to_favourite {

   function migrate()
   {
      
      Schema::alter('users', function(Schema $schema) {
         $schema->change('favorite_sport')->varchar('favourite_sport', 50)->nullable();
         $schema->change('favorite_team')->varchar('favourite_team', 50)->nullable();
      }, false);

   }

}
