<?php
namespace Migrations;
use Library\Database\Schema;

function migrate()
{
   Schema::create('organisation_info', function(Schema $schema) {
      $schema->int('id')->auto_increment()->primary();
      $schema->text('about_us');
      $schema->text('disclaimer');
      $schema->text('how_it_works');
      $schema->text('terms_and_condition');
      $schema->text('membership');
      $schema->text('rewards_and_benefits');
      $schema->text('tournaments_and_leagues');
      $schema->json('board_of_trustees');
      $schema->varchar('contact_telephone');
      $schema->varchar('contact_address');
      $schema->varchar('contact_email');
      $schema->json('faq');
      $schema->timestamp('created_at')->attribute();
      $schema->datetime('updated_at')->attribute("ON UPDATE CURRENT_TIMESTAMP");
   }, false, 'OrganisationInfo');
}
