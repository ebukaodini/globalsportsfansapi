<?php
namespace Migrations;
use Library\Database\Schema;

class migration_2021_03_27_141228_rename_user_slot_to_user_package {

   function migrate()
   {

      Schema::rename('user_slots', 'user_package', 'UserSlots', 'UserPackage');
   
   }

}
