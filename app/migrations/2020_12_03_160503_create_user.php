<?php
namespace Migrations;
use Library\Database\Schema;

function migrate()
{
   Schema::create('users', function(Schema $schema) {
      $schema->int('id')->auto_increment()->primary();
      $schema->varchar('firstname', 50);
      $schema->varchar('lastname', 50);
      $schema->varchar('telephone', 20);
      $schema->varchar('email', 50);
      $schema->varchar('role', 10);
      $schema->timestamp('created_at')->attribute();
      $schema->datetime('updated_at')->attribute("ON UPDATE CURRENT_TIMESTAMP");
   }, false, 'users');

}
