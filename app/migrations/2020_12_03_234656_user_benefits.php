<?php
namespace Migrations;
use Library\Database\Schema;

function migrate()
{
   // this is where the user's accrued benefit should come to
   Schema::create('user_benefits', function(Schema $schema) {
      $schema->int('id')->auto_increment()->primary();
      $schema->varchar('status', 10)->default('');
      $schema->timestamp('created_at');
      $schema->datetime('updated_at')->attribute("ON UPDATE CURRENT_TIMESTAMP");
   }, false, 'user_benefits');
}
