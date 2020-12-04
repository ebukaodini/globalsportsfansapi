<?php
namespace Migrations;
use Library\Database\Schema;

function migrate()
{
   Schema::create('users', function(Schema $schema) {
      $schema->double('id')->auto_increment()->primary();
      $schema->varchar('firstname', 50);
      $schema->varchar('lastname', 50);
      $schema->varchar('telephone', 20);
      $schema->varchar('email', 50);
      $schema->varchar('role', 10);
      $schema->text('permissions');
      $schema->varchar('token', 10);
      $schema->varchar('member_id', 10)->unique();
      $schema->varchar('verification_status', 10)->default('unverified'); // verified
      $schema->timestamp('created_at');
      $schema->datetime('updated_at')->attribute("ON UPDATE CURRENT_TIMESTAMP");
   }, false, 'Users');

}
