<?php
namespace Migrations;
use Library\Database\Schema;

class migration_2020_12_03_234656_user_benefits {

   function migrate()
   {
      // this is where the user's accrued benefit should come to
      Schema::create('user_benefits', function(Schema $schema) {
         $schema->int('id')->auto_increment()->primary();
         $schema->double('user_id');
         $schema->varchar('achievement', 200);
         $schema->double('cash')->default('0');
         $schema->text('benefit')->nullable();
         $schema->varchar('status', 10)->default('pending'); // cancelled, given
         $schema->timestamp('created_at');
         $schema->datetime('updated_at')->attribute("ON UPDATE CURRENT_TIMESTAMP");
         $schema->foreign('user_id', 'users', 'id', 'ON DELETE RESTRICT', 'ON UPDATE CASCADE');
      }, false, 'UserBenefits');
   }

}