<?php
namespace Migrations;
use Library\Database\Schema;

class migration_2020_12_16_215336_change_fields_with_blob_text_json_with_defaults {

   function migrate()
   {

      Schema::alter('users', function(Schema $schema) {
         $schema->change('permissions')->text('permissions')->nullable();
      }, false);

      Schema::alter('organisation_info', function(Schema $schema) {
         $schema->change('mou')->text('mou');
      }, false);
      

   }

}
