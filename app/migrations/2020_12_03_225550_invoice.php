<?php
namespace Migrations;
use Library\Database\Schema;

function migrate()
{
   Schema::create('invoice', function(Schema $schema) {
      $schema->double('id')->auto_increment()->primary();
      $schema->double('user_id');
      $schema->varchar('invoice_number', 10)->unique();
      $schema->text('invoice_description');
      $schema->double('amount_due');
      $schema->double('amount_paid')->default('0');
      $schema->varchar('payment_method', 20); // banktransfer, 
      $schema->varchar('payment_evidence', 100)->nullable();
      $schema->varchar('status', 10)->default('unpaid'); //paid
      $schema->timestamp('created_at');
      $schema->datetime('updated_at')->attribute("ON UPDATE CURRENT_TIMESTAMP");
      $schema->foreign('user_id', 'users', 'id', 'ON DELETE RESTRICT', 'ON UPDATE CASCADE');
   }, false, 'Invoice');
}
