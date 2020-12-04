<?php
namespace Migrations;
use Library\Database\Schema;

function migrate()
{
   Schema::create('slots', function(Schema $schema) {
      $schema->int('id')->auto_increment()->primary();
      $schema->varchar('program', 50);
      $schema->int('no_slots');
      $schema->double('cost');
      $schema->text('benefits');
      $schema->timestamp('created_at')->attribute();
      $schema->datetime('updated_at')->attribute("ON UPDATE CURRENT_TIMESTAMP");
   }, false, 'Slots');
}
