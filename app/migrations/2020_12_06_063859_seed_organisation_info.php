<?php
namespace Migrations;
use Library\Database\Schema;

class migration_2020_12_06_063859_seed_organisation_info {

   function migrate()
   {
      Schema::drop('organisation_info');

      Schema::create('organisation_info', function(Schema $schema) {
         $schema->int('id')->auto_increment()->primary();
         $schema->text('about_us');
         $schema->text('disclaimer');
         $schema->text('how_it_works');
         $schema->text('terms_and_condition');
         $schema->text('membership');
         $schema->text('rewards_and_benefits');
         $schema->text('tournaments_and_leagues');
         $schema->varchar('contact_telephone', 50);
         $schema->varchar('contact_address', 200);
         $schema->varchar('contact_email', 100);
         $schema->json('faq');
         $schema->timestamp('created_at');
         $schema->datetime('updated_at')->attribute("ON UPDATE CURRENT_TIMESTAMP");
      }, false, 'OrganisationInfo');
      
      Schema::seed('organisation_info', 
         [
            'about_us' => 'About Us',
            'disclaimer' => 'Disclaimer',
            'how_it_works' => 'How It Works',
            'terms_and_condition' => 'Terms and Conditions',
            'membership' => 'Membership',
            'rewards_and_benefits' => 'Rewards and Benefits',
            'tournaments_and_leagues' => 'Tournaments and Leagues',
            'contact_telephone' => 'Contact Telephone',
            'contact_address' => 'Contact Address',
            'contact_email' => 'contact Email',
            'faq' => json_encode([
               'how do i pay' => 'make payment to our bank account'
            ])
         ]
      );
   }

}
