<?php
namespace Migrations;
use Library\Database\Schema;

class migration_2021_04_22_130034_add_payment {

   function migrate()
   {
      Schema::create('payments', function(Schema $schema) {
         $schema->double('id')->auto_increment()->primary();
         $schema->varchar('invoice_number', 50);
         $schema->varchar('reference', 50);
         $schema->varchar('email', 100);
         $schema->varchar('domain', 50)->nullable(); // test or live
         $schema->double('amount_paid')->nullable();
         $schema->varchar('ip_address', 50)->nullable();
         $schema->varchar('payment_service', 20)->nullable(); // paystack or flutterwave
         $schema->varchar('channel', 10)->nullable(); // the payment channel: card, bank, ussd, etc
         $schema->varchar('currency', 10)->nullable(); // NGN etc
         $schema->varchar('gateway_response', 50)->nullable();
         $schema->varchar('status', 10)->default('pending');
         $schema->datetime('paid_at')->nullable();
         $schema->timestamp('created_at')->attribute();
         $schema->datetime('updated_at')->attribute("ON UPDATE CURRENT_TIMESTAMP");
      }, false, 'Payments');

   }

}
