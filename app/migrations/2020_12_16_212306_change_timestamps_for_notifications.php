<?php
namespace Migrations;
use Library\Database\Schema;

class migration_2020_12_16_212306_change_timestamps_for_notifications {

   function migrate()
   {
      
      Schema::alter('invoice', function(Schema $schema) {
         $schema->change('created_at')->timestamp('created_at', false);
         $schema->change('updated_at')->timestamp('updated_at', true);
      }, false);

      Schema::alter('notifications', function(Schema $schema) {
         $schema->change('created_at')->timestamp('created_at', false);
         $schema->change('updated_at')->timestamp('updated_at', true);
      }, false);

      Schema::alter('organisation_info', function(Schema $schema) {
         $schema->change('created_at')->timestamp('created_at', false);
         $schema->change('updated_at')->timestamp('updated_at', true);
      }, false);

      Schema::alter('permissions', function(Schema $schema) {
         $schema->change('created_at')->timestamp('created_at', false);
         $schema->change('updated_at')->timestamp('updated_at', true);
      }, false);

      Schema::alter('referral_levels', function(Schema $schema) {
         $schema->change('created_at')->timestamp('created_at', false);
         $schema->change('updated_at')->timestamp('updated_at', true);
      }, false);

      Schema::alter('slots', function(Schema $schema) {
         $schema->change('created_at')->timestamp('created_at', false);
         $schema->change('updated_at')->timestamp('updated_at', true);
      }, false);

      Schema::alter('users', function(Schema $schema) {
         $schema->change('created_at')->timestamp('created_at', false);
         $schema->change('updated_at')->timestamp('updated_at', true);
      }, false);

      Schema::alter('user_benefits', function(Schema $schema) {
         $schema->change('created_at')->timestamp('created_at', false);
         $schema->change('updated_at')->timestamp('updated_at', true);
      }, false);

      Schema::alter('user_slots', function(Schema $schema) {
         $schema->change('created_at')->timestamp('created_at', false);
         $schema->change('updated_at')->timestamp('updated_at', true);
      }, false);

   }

}
