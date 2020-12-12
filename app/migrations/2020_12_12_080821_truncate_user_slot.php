<?php
namespace Migrations;
use Library\Database\Schema;

class migration_2020_12_12_080821_truncate_user_slot {

   function migrate()
   {
      Schema::truncate('invoice');
      Schema::truncate('user_slots');
   }

}
