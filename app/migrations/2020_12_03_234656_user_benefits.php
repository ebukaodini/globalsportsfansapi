<?php
namespace Migrations;
use Library\Database\Schema;

function migrate()
{
   Schema::create('user_benefits', function(Schema $schema) {
      $schema->int('id')->auto_increment()->primary();
      $schema->timestamp('created_at')->attribute();
      $schema->datetime('updated_at')->attribute("ON UPDATE CURRENT_TIMESTAMP");
   }, false, 'user_benefits');

   // Schema::seed('user_benefits', 
   //    [
   //       'field' => 'value',
   //       'field' => 'value',
   //    ],
   //    [
   //       'field' => 'value',
   //       'field' => 'value',
   //    ],
   //    ...
   // );

   // Schema::alter('user_benefits', function(Schema $schema) {
   //    $schema->change('id')->double('id');
   //    $schema->change('created_at')->datetime('created_at');
   //    $schema->change('updated_at')->datetime('updated_at');
   // }, false);

   // Schema::drop('user_benefits');
}
