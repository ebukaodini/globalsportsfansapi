<?php
namespace Migrations;
use Library\Database\Schema;

class migration_2020_12_03_160503_user {
   function migrate()
   {
      Schema::create('users', function(Schema $schema) {
         $schema->double('id')->auto_increment()->primary();
         // authorization fields
         $schema->varchar('telephone', 20)->unique();
         $schema->varchar('email', 100)->unique();
         $schema->varchar('password', 200);
         $schema->varchar('token', 10)->nullable();
         $schema->varchar('role', 10)->default('member');
         $schema->text('permissions')->default('dashboard');
         // bio data
         $schema->varchar('firstname', 50)->nullable();
         $schema->varchar('lastname', 50)->nullable();
         $schema->varchar('middlename', 50)->nullable();
         $schema->varchar('residential_address', 200)->nullable();
         $schema->varchar('occupation', 50)->nullable();
         // documents
         $schema->varchar('profile_picture', 100)->nullable();
         $schema->varchar('mou', 100)->nullable(); // memorandum of understanding
         // next of kin details
         $schema->varchar('nextofkin_name', 100)->nullable();
         $schema->varchar('nextofkin_telephone', 20)->nullable();
         $schema->varchar('nextofkin_residential_address', 200)->nullable();
         // bank details
         $schema->varchar('accountnumber', 10)->nullable();
         $schema->varchar('accountname', 50)->nullable();
         $schema->varchar('bankname', 50)->nullable();
         // sport details
         $schema->varchar('favorite_sport', 50)->nullable();
         $schema->varchar('favorite_team', 50)->nullable();
         // meta data
         $schema->varchar('member_id', 10)->unique();
         $schema->varchar('verification_status', 10)->default('unverified'); // verified
         $schema->timestamp('created_at');
         $schema->datetime('updated_at')->attribute("ON UPDATE CURRENT_TIMESTAMP");
      }, false, 'Users');

   }

}
