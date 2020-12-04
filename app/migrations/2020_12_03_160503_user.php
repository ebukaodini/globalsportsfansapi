<?php
namespace Migrations;
use Library\Database\Schema;

function migrate()
{
   Schema::create('users', function(Schema $schema) {
      $schema->double('id')->auto_increment()->primary();
      $schema->varchar('firstname', 50);
      $schema->varchar('lastname', 50);
      $schema->varchar('middlename', 50);
      $schema->varchar('telephone', 20);
      $schema->varchar('email', 100);
      $schema->varchar('residential_address', 200);
      $schema->varchar('occupation', 50);
      $schema->varchar('accountnumber', 10);
      $schema->varchar('accountname', 50);
      $schema->varchar('bankname', 50);
      $schema->varchar('nextofkin_name', 10);
      $schema->varchar('mou', 100);
      $schema->varchar('favorite_sport', 50);
      $schema->varchar('favorite_team', 50);
      $schema->varchar('role', 10);
      $schema->text('permissions');
      $schema->varchar('token', 10);
      $schema->varchar('member_id', 10)->unique();
      $schema->varchar('verification_status', 10)->default('unverified'); // verified
      $schema->timestamp('created_at');
      $schema->datetime('updated_at')->attribute("ON UPDATE CURRENT_TIMESTAMP");
   }, false, 'Users');

}
