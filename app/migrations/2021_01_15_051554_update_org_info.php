<?php
namespace Migrations;
use Library\Database\Schema;

class migration_2021_01_15_051554_update_org_info {

   function migrate()
   {
      
      Schema::alter('organisation_info', function(Schema $schema) {
         $schema->change('faq')->long_text('faq');
         $schema->add()->varchar('bank_name', 50)->after('faq');
         $schema->add()->varchar('bank_accountnumber', 10)->after('bank_name');
         $schema->add()->varchar('bank_accountname', 50)->after('bank_accountnumber');
      }, false);

   }

}
