<?php
namespace Migrations;
use Library\Database\Schema;
use Models\OrganisationInfo;

class migration_2020_12_08_055404_update_orginfo_and_mou_for_users {

   function migrate()
   {
      
      Schema::alter('users', function(Schema $schema) {
         $schema->change('mou')->varchar('mou', 10)->default('accepted'); // unaccepted
      }, false);

      Schema::alter('organisation_info', function(Schema $schema) {
         $schema->add()->text('mou')->default('Memorandum of Understanding')->after('terms_and_condition')->nullable();
      }, false);

   }

}
