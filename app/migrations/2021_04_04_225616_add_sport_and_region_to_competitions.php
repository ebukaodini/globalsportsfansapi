<?php
namespace Migrations;
use Library\Database\Schema;

class migration_2021_04_04_225616_add_sport_and_region_to_competitions {

   function migrate()
   {
      Schema::alter('competitions', function(Schema $schema) {
         $schema->add()->varchar('region', 50)->nullable()->after('competition');
         $schema->add()->varchar('sport', 100)->nullable()->after('region');
      }, false);
      
   }

}
