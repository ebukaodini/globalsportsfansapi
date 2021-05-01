<?php

namespace Migrations;

use Library\Database\Schema;

class migration_2021_04_04_213129_add_country_continent_map_to_leagues
{

   function migrate()
   {
      // create the country map
      Schema::create('country_leagues', function (Schema $schema) {
         $schema->double('id')->auto_increment()->primary();
         $schema->varchar('country', 100)->not_nullable();
         $schema->varchar('continent', 50)->not_nullable();
         $schema->varchar('major_football_competition', 100)->nullable();
         $schema->json('football_qualified_teams')->nullable();
         // more competitions and teams would be added later...
         $schema->timestamp('created_at')->attribute();
         $schema->timestamp('updated_at')->default('CURRENT_TIMESTAMP')->attribute("ON UPDATE CURRENT_TIMESTAMP");
      }, false, 'CountryLeagues');

      // create the international competitions map
      Schema::create('competitions', function (Schema $schema) {
         $schema->double('id')->auto_increment()->primary();
         $schema->varchar('competition', 100)->not_nullable();
         $schema->json('qualified_teams')->nullable();
         $schema->timestamp('created_at')->attribute();
         $schema->timestamp('updated_at')->default('CURRENT_TIMESTAMP')->attribute("ON UPDATE CURRENT_TIMESTAMP");
      }, false, 'Competitions');

      // seed the country leagues
      Schema::seed(
         'country_leagues',
         [
            "country" => "Afghanistan",
            "continent" => "Asia"
         ],
         [
            "country" => "Albania",
            "continent" => "Europe"
         ],
         [
            "country" => "Algeria",
            "continent" => "Africa"
         ],
         [
            "country" => "American Samoa",
            "continent" => "Oceania"
         ],
         [
            "country" => "Andorra",
            "continent" => "Europe"
         ],
         [
            "country" => "Angola",
            "continent" => "Africa"
         ],
         [
            "country" => "Anguilla",
            "continent" => "North America"
         ],
         [
            "country" => "Antarctica",
            "continent" => "Antarctica"
         ],
         [
            "country" => "Antigua and Barbuda",
            "continent" => "North America"
         ],
         [
            "country" => "Argentina",
            "continent" => "South America"
         ],
         [
            "country" => "Armenia",
            "continent" => "Asia"
         ],
         [
            "country" => "Aruba",
            "continent" => "North America"
         ],
         [
            "country" => "Australia",
            "continent" => "Oceania"
         ],
         [
            "country" => "Austria",
            "continent" => "Europe"
         ],
         [
            "country" => "Azerbaijan",
            "continent" => "Asia"
         ],
         [
            "country" => "Bahamas",
            "continent" => "North America"
         ],
         [
            "country" => "Bahrain",
            "continent" => "Asia"
         ],
         [
            "country" => "Bangladesh",
            "continent" => "Asia"
         ],
         [
            "country" => "Barbados",
            "continent" => "North America"
         ],
         [
            "country" => "Belarus",
            "continent" => "Europe"
         ],
         [
            "country" => "Belgium",
            "continent" => "Europe"
         ],
         [
            "country" => "Belize",
            "continent" => "North America"
         ],
         [
            "country" => "Benin",
            "continent" => "Africa"
         ],
         [
            "country" => "Bermuda",
            "continent" => "North America"
         ],
         [
            "country" => "Bhutan",
            "continent" => "Asia"
         ],
         [
            "country" => "Bolivia",
            "continent" => "South America"
         ],
         [
            "country" => "Bosnia and Herzegovina",
            "continent" => "Europe"
         ],
         [
            "country" => "Botswana",
            "continent" => "Africa"
         ],
         [
            "country" => "Bouvet Island",
            "continent" => "Antarctica"
         ],
         [
            "country" => "Brazil",
            "continent" => "South America"
         ],
         [
            "country" => "British Indian Ocean Territory",
            "continent" => "Africa"
         ],
         [
            "country" => "Brunei",
            "continent" => "Asia"
         ],
         [
            "country" => "Bulgaria",
            "continent" => "Europe"
         ],
         [
            "country" => "Burkina Faso",
            "continent" => "Africa"
         ],
         [
            "country" => "Burundi",
            "continent" => "Africa"
         ],
         [
            "country" => "Cambodia",
            "continent" => "Asia"
         ],
         [
            "country" => "Cameroon",
            "continent" => "Africa"
         ],
         [
            "country" => "Canada",
            "continent" => "North America"
         ],
         [
            "country" => "Cape Verde",
            "continent" => "Africa"
         ],
         [
            "country" => "Cayman Islands",
            "continent" => "North America"
         ],
         [
            "country" => "Central African Republic",
            "continent" => "Africa"
         ],
         [
            "country" => "Chad",
            "continent" => "Africa"
         ],
         [
            "country" => "Chile",
            "continent" => "South America"
         ],
         [
            "country" => "China",
            "continent" => "Asia"
         ],
         [
            "country" => "Christmas Island",
            "continent" => "Oceania"
         ],
         [
            "country" => "Cocos (Keeling) Islands",
            "continent" => "Oceania"
         ],
         [
            "country" => "Colombia",
            "continent" => "South America"
         ],
         [
            "country" => "Comoros",
            "continent" => "Africa"
         ],
         [
            "country" => "Congo",
            "continent" => "Africa"
         ],
         [
            "country" => "Cook Islands",
            "continent" => "Oceania"
         ],
         [
            "country" => "Costa Rica",
            "continent" => "North America"
         ],
         [
            "country" => "Croatia",
            "continent" => "Europe"
         ],
         [
            "country" => "Cuba",
            "continent" => "North America"
         ],
         [
            "country" => "Cyprus",
            "continent" => "Asia"
         ],
         [
            "country" => "Czech Republic",
            "continent" => "Europe"
         ],
         [
            "country" => "Denmark",
            "continent" => "Europe"
         ],
         [
            "country" => "Djibouti",
            "continent" => "Africa"
         ],
         [
            "country" => "Dominica",
            "continent" => "North America"
         ],
         [
            "country" => "Dominican Republic",
            "continent" => "North America"
         ],
         [
            "country" => "East Timor",
            "continent" => "Asia"
         ],
         [
            "country" => "Ecuador",
            "continent" => "South America"
         ],
         [
            "country" => "Egypt",
            "continent" => "Africa"
         ],
         [
            "country" => "El Salvador",
            "continent" => "North America"
         ],
         [
            "country" => "England",
            "continent" => "Europe"
         ],
         [
            "country" => "Equatorial Guinea",
            "continent" => "Africa"
         ],
         [
            "country" => "Eritrea",
            "continent" => "Africa"
         ],
         [
            "country" => "Estonia",
            "continent" => "Europe"
         ],
         [
            "country" => "Ethiopia",
            "continent" => "Africa"
         ],
         [
            "country" => "Falkland Islands",
            "continent" => "South America"
         ],
         [
            "country" => "Faroe Islands",
            "continent" => "Europe"
         ],
         [
            "country" => "Fiji Islands",
            "continent" => "Oceania"
         ],
         [
            "country" => "Finland",
            "continent" => "Europe"
         ],
         [
            "country" => "France",
            "continent" => "Europe"
         ],
         [
            "country" => "French Guiana",
            "continent" => "South America"
         ],
         [
            "country" => "French Polynesia",
            "continent" => "Oceania"
         ],
         [
            "country" => "French Southern territories",
            "continent" => "Antarctica"
         ],
         [
            "country" => "Gabon",
            "continent" => "Africa"
         ],
         [
            "country" => "Gambia",
            "continent" => "Africa"
         ],
         [
            "country" => "Georgia",
            "continent" => "Asia"
         ],
         [
            "country" => "Germany",
            "continent" => "Europe"
         ],
         [
            "country" => "Ghana",
            "continent" => "Africa"
         ],
         [
            "country" => "Gibraltar",
            "continent" => "Europe"
         ],
         [
            "country" => "Greece",
            "continent" => "Europe"
         ],
         [
            "country" => "Greenland",
            "continent" => "North America"
         ],
         [
            "country" => "Grenada",
            "continent" => "North America"
         ],
         [
            "country" => "Guadeloupe",
            "continent" => "North America"
         ],
         [
            "country" => "Guam",
            "continent" => "Oceania"
         ],
         [
            "country" => "Guatemala",
            "continent" => "North America"
         ],
         [
            "country" => "Guinea",
            "continent" => "Africa"
         ],
         [
            "country" => "Guinea-Bissau",
            "continent" => "Africa"
         ],
         [
            "country" => "Guyana",
            "continent" => "South America"
         ],
         [
            "country" => "Haiti",
            "continent" => "North America"
         ],
         [
            "country" => "Heard Island and McDonald Islands",
            "continent" => "Antarctica"
         ],
         [
            "country" => "Holy See (Vatican City State)",
            "continent" => "Europe"
         ],
         [
            "country" => "Honduras",
            "continent" => "North America"
         ],
         [
            "country" => "Hong Kong",
            "continent" => "Asia"
         ],
         [
            "country" => "Hungary",
            "continent" => "Europe"
         ],
         [
            "country" => "Iceland",
            "continent" => "Europe"
         ],
         [
            "country" => "India",
            "continent" => "Asia"
         ],
         [
            "country" => "Indonesia",
            "continent" => "Asia"
         ],
         [
            "country" => "Iran",
            "continent" => "Asia"
         ],
         [
            "country" => "Iraq",
            "continent" => "Asia"
         ],
         [
            "country" => "Ireland",
            "continent" => "Europe"
         ],
         [
            "country" => "Israel",
            "continent" => "Asia"
         ],
         [
            "country" => "Italy",
            "continent" => "Europe"
         ],
         [
            "country" => "Ivory Coast",
            "continent" => "Africa"
         ],
         [
            "country" => "Jamaica",
            "continent" => "North America"
         ],
         [
            "country" => "Japan",
            "continent" => "Asia"
         ],
         [
            "country" => "Jordan",
            "continent" => "Asia"
         ],
         [
            "country" => "Kazakhstan",
            "continent" => "Asia"
         ],
         [
            "country" => "Kenya",
            "continent" => "Africa"
         ],
         [
            "country" => "Kiribati",
            "continent" => "Oceania"
         ],
         [
            "country" => "Kuwait",
            "continent" => "Asia"
         ],
         [
            "country" => "Kyrgyzstan",
            "continent" => "Asia"
         ],
         [
            "country" => "Laos",
            "continent" => "Asia"
         ],
         [
            "country" => "Latvia",
            "continent" => "Europe"
         ],
         [
            "country" => "Lebanon",
            "continent" => "Asia"
         ],
         [
            "country" => "Lesotho",
            "continent" => "Africa"
         ],
         [
            "country" => "Liberia",
            "continent" => "Africa"
         ],
         [
            "country" => "Libyan Arab Jamahiriya",
            "continent" => "Africa"
         ],
         [
            "country" => "Liechtenstein",
            "continent" => "Europe"
         ],
         [
            "country" => "Lithuania",
            "continent" => "Europe"
         ],
         [
            "country" => "Luxembourg",
            "continent" => "Europe"
         ],
         [
            "country" => "Macao",
            "continent" => "Asia"
         ],
         [
            "country" => "North Macedonia",
            "continent" => "Europe"
         ],
         [
            "country" => "Madagascar",
            "continent" => "Africa"
         ],
         [
            "country" => "Malawi",
            "continent" => "Africa"
         ],
         [
            "country" => "Malaysia",
            "continent" => "Asia"
         ],
         [
            "country" => "Maldives",
            "continent" => "Asia"
         ],
         [
            "country" => "Mali",
            "continent" => "Africa"
         ],
         [
            "country" => "Malta",
            "continent" => "Europe"
         ],
         [
            "country" => "Marshall Islands",
            "continent" => "Oceania"
         ],
         [
            "country" => "Martinique",
            "continent" => "North America"
         ],
         [
            "country" => "Mauritania",
            "continent" => "Africa"
         ],
         [
            "country" => "Mauritius",
            "continent" => "Africa"
         ],
         [
            "country" => "Mayotte",
            "continent" => "Africa"
         ],
         [
            "country" => "Mexico",
            "continent" => "North America"
         ],
         [
            "country" => "Micronesia, Federated States of",
            "continent" => "Oceania"
         ],
         [
            "country" => "Moldova",
            "continent" => "Europe"
         ],
         [
            "country" => "Monaco",
            "continent" => "Europe"
         ],
         [
            "country" => "Mongolia",
            "continent" => "Asia"
         ],
         [
            "country" => "Montenegro",
            "continent" => "Europe"
         ],
         [
            "country" => "Montserrat",
            "continent" => "North America"
         ],
         [
            "country" => "Morocco",
            "continent" => "Africa"
         ],
         [
            "country" => "Mozambique",
            "continent" => "Africa"
         ],
         [
            "country" => "Myanmar",
            "continent" => "Asia"
         ],
         [
            "country" => "Namibia",
            "continent" => "Africa"
         ],
         [
            "country" => "Nauru",
            "continent" => "Oceania"
         ],
         [
            "country" => "Nepal",
            "continent" => "Asia"
         ],
         [
            "country" => "Netherlands",
            "continent" => "Europe"
         ],
         [
            "country" => "Netherlands Antilles",
            "continent" => "North America"
         ],
         [
            "country" => "New Caledonia",
            "continent" => "Oceania"
         ],
         [
            "country" => "New Zealand",
            "continent" => "Oceania"
         ],
         [
            "country" => "Nicaragua",
            "continent" => "North America"
         ],
         [
            "country" => "Niger",
            "continent" => "Africa"
         ],
         [
            "country" => "Nigeria",
            "continent" => "Africa"
         ],
         [
            "country" => "Niue",
            "continent" => "Oceania"
         ],
         [
            "country" => "Norfolk Island",
            "continent" => "Oceania"
         ],
         [
            "country" => "North Korea",
            "continent" => "Asia"
         ],
         [
            "country" => "Northern Ireland",
            "continent" => "Europe"
         ],
         [
            "country" => "Northern Mariana Islands",
            "continent" => "Oceania"
         ],
         [
            "country" => "Norway",
            "continent" => "Europe"
         ],
         [
            "country" => "Oman",
            "continent" => "Asia"
         ],
         [
            "country" => "Pakistan",
            "continent" => "Asia"
         ],
         [
            "country" => "Palau",
            "continent" => "Oceania"
         ],
         [
            "country" => "Palestine",
            "continent" => "Asia"
         ],
         [
            "country" => "Panama",
            "continent" => "North America"
         ],
         [
            "country" => "Papua New Guinea",
            "continent" => "Oceania"
         ],
         [
            "country" => "Paraguay",
            "continent" => "South America"
         ],
         [
            "country" => "Peru",
            "continent" => "South America"
         ],
         [
            "country" => "Philippines",
            "continent" => "Asia"
         ],
         [
            "country" => "Pitcairn",
            "continent" => "Oceania"
         ],
         [
            "country" => "Poland",
            "continent" => "Europe"
         ],
         [
            "country" => "Portugal",
            "continent" => "Europe"
         ],
         [
            "country" => "Puerto Rico",
            "continent" => "North America"
         ],
         [
            "country" => "Qatar",
            "continent" => "Asia"
         ],
         [
            "country" => "Reunion",
            "continent" => "Africa"
         ],
         [
            "country" => "Romania",
            "continent" => "Europe"
         ],
         [
            "country" => "Russian Federation",
            "continent" => "Europe"
         ],
         [
            "country" => "Rwanda",
            "continent" => "Africa"
         ],
         [
            "country" => "Saint Helena",
            "continent" => "Africa"
         ],
         [
            "country" => "Saint Kitts and Nevis",
            "continent" => "North America"
         ],
         [
            "country" => "Saint Lucia",
            "continent" => "North America"
         ],
         [
            "country" => "Saint Pierre and Miquelon",
            "continent" => "North America"
         ],
         [
            "country" => "Saint Vincent and the Grenadines",
            "continent" => "North America"
         ],
         [
            "country" => "Samoa",
            "continent" => "Oceania"
         ],
         [
            "country" => "San Marino",
            "continent" => "Europe"
         ],
         [
            "country" => "Sao Tome and Principe",
            "continent" => "Africa"
         ],
         [
            "country" => "Saudi Arabia",
            "continent" => "Asia"
         ],
         [
            "country" => "Scotland",
            "continent" => "Europe"
         ],
         [
            "country" => "Senegal",
            "continent" => "Africa"
         ],
         [
            "country" => "Serbia",
            "continent" => "Europe"
         ],
         [
            "country" => "Seychelles",
            "continent" => "Africa"
         ],
         [
            "country" => "Sierra Leone",
            "continent" => "Africa"
         ],
         [
            "country" => "Singapore",
            "continent" => "Asia"
         ],
         [
            "country" => "Slovakia",
            "continent" => "Europe"
         ],
         [
            "country" => "Slovenia",
            "continent" => "Europe"
         ],
         [
            "country" => "Solomon Islands",
            "continent" => "Oceania"
         ],
         [
            "country" => "Somalia",
            "continent" => "Africa"
         ],
         [
            "country" => "South Africa",
            "continent" => "Africa"
         ],
         [
            "country" => "South Georgia and the South Sandwich Islands",
            "continent" => "Antarctica"
         ],
         [
            "country" => "South Korea",
            "continent" => "Asia"
         ],
         [
            "country" => "South Sudan",
            "continent" => "Africa"
         ],
         [
            "country" => "Spain",
            "continent" => "Europe"
         ],
         [
            "country" => "Sri Lanka",
            "continent" => "Asia"
         ],
         [
            "country" => "Sudan",
            "continent" => "Africa"
         ],
         [
            "country" => "Suriname",
            "continent" => "South America"
         ],
         [
            "country" => "Svalbard and Jan Mayen",
            "continent" => "Europe"
         ],
         [
            "country" => "Swaziland",
            "continent" => "Africa"
         ],
         [
            "country" => "Sweden",
            "continent" => "Europe"
         ],
         [
            "country" => "Switzerland",
            "continent" => "Europe"
         ],
         [
            "country" => "Syria",
            "continent" => "Asia"
         ],
         [
            "country" => "Tajikistan",
            "continent" => "Asia"
         ],
         [
            "country" => "Tanzania",
            "continent" => "Africa"
         ],
         [
            "country" => "Thailand",
            "continent" => "Asia"
         ],
         [
            "country" => "The Democratic Republic of Congo",
            "continent" => "Africa"
         ],
         [
            "country" => "Togo",
            "continent" => "Africa"
         ],
         [
            "country" => "Tokelau",
            "continent" => "Oceania"
         ],
         [
            "country" => "Tonga",
            "continent" => "Oceania"
         ],
         [
            "country" => "Trinidad and Tobago",
            "continent" => "North America"
         ],
         [
            "country" => "Tunisia",
            "continent" => "Africa"
         ],
         [
            "country" => "Turkey",
            "continent" => "Asia"
         ],
         [
            "country" => "Turkmenistan",
            "continent" => "Asia"
         ],
         [
            "country" => "Turks and Caicos Islands",
            "continent" => "North America"
         ],
         [
            "country" => "Tuvalu",
            "continent" => "Oceania"
         ],
         [
            "country" => "Uganda",
            "continent" => "Africa"
         ],
         [
            "country" => "Ukraine",
            "continent" => "Europe"
         ],
         [
            "country" => "United Arab Emirates",
            "continent" => "Asia"
         ],
         [
            "country" => "United Kingdom",
            "continent" => "Europe"
         ],
         [
            "country" => "United States",
            "continent" => "North America"
         ],
         [
            "country" => "United States Minor Outlying Islands",
            "continent" => "Oceania"
         ],
         [
            "country" => "Uruguay",
            "continent" => "South America"
         ],
         [
            "country" => "Uzbekistan",
            "continent" => "Asia"
         ],
         [
            "country" => "Vanuatu",
            "continent" => "Oceania"
         ],
         [
            "country" => "Venezuela",
            "continent" => "South America"
         ],
         [
            "country" => "Vietnam",
            "continent" => "Asia"
         ],
         [
            "country" => "Virgin Islands, British",
            "continent" => "North America"
         ],
         [
            "country" => "Virgin Islands, U.S.",
            "continent" => "North America"
         ],
         [
            "country" => "Wales",
            "continent" => "Europe"
         ],
         [
            "country" => "Wallis and Futuna",
            "continent" => "Oceania"
         ],
         [
            "country" => "Western Sahara",
            "continent" => "Africa"
         ],
         [
            "country" => "Yemen",
            "continent" => "Asia"
         ],
         [
            "country" => "Zambia",
            "continent" => "Africa"
         ],
         [
            "country" => "Zimbabwe",
            "continent" => "Africa"
         ]
      );
   }
}

/*
[
   [
        "country" => "Afghanistan",
        "continent" => "Asia"
    ],
   [
        "country" => "Albania",
        "continent" => "Europe"
    ],
   [
        "country" => "Algeria",
        "continent" => "Africa"
    ],
   [
        "country" => "American Samoa",
        "continent" => "Oceania"
    ],
   [
        "country" => "Andorra",
        "continent" => "Europe"
    ],
   [
        "country" => "Angola",
        "continent" => "Africa"
    ],
   [
        "country" => "Anguilla",
        "continent" => "North America"
    ],
   [
        "country" => "Antarctica",
        "continent" => "Antarctica"
    ],
   [
        "country" => "Antigua and Barbuda",
        "continent" => "North America"
    ],
   [
        "country" => "Argentina",
        "continent" => "South America"
    ],
   [
        "country" => "Armenia",
        "continent" => "Asia"
    ],
   [
        "country" => "Aruba",
        "continent" => "North America"
    ],
   [
        "country" => "Australia",
        "continent" => "Oceania"
    ],
   [
        "country" => "Austria",
        "continent" => "Europe"
    ],
   [
        "country" => "Azerbaijan",
        "continent" => "Asia"
    ],
   [
        "country" => "Bahamas",
        "continent" => "North America"
    ],
   [
        "country" => "Bahrain",
        "continent" => "Asia"
    ],
   [
        "country" => "Bangladesh",
        "continent" => "Asia"
    ],
   [
        "country" => "Barbados",
        "continent" => "North America"
    ],
   [
        "country" => "Belarus",
        "continent" => "Europe"
    ],
   [
        "country" => "Belgium",
        "continent" => "Europe"
    ],
   [
        "country" => "Belize",
        "continent" => "North America"
    ],
   [
        "country" => "Benin",
        "continent" => "Africa"
    ],
   [
        "country" => "Bermuda",
        "continent" => "North America"
    ],
   [
        "country" => "Bhutan",
        "continent" => "Asia"
    ],
   [
        "country" => "Bolivia",
        "continent" => "South America"
    ],
   [
        "country" => "Bosnia and Herzegovina",
        "continent" => "Europe"
    ],
   [
        "country" => "Botswana",
        "continent" => "Africa"
    ],
   [
        "country" => "Bouvet Island",
        "continent" => "Antarctica"
    ],
   [
        "country" => "Brazil",
        "continent" => "South America"
    ],
   [
        "country" => "British Indian Ocean Territory",
        "continent" => "Africa"
    ],
   [
        "country" => "Brunei",
        "continent" => "Asia"
    ],
   [
        "country" => "Bulgaria",
        "continent" => "Europe"
    ],
   [
        "country" => "Burkina Faso",
        "continent" => "Africa"
    ],
   [
        "country" => "Burundi",
        "continent" => "Africa"
    ],
   [
        "country" => "Cambodia",
        "continent" => "Asia"
    ],
   [
        "country" => "Cameroon",
        "continent" => "Africa"
    ],
   [
        "country" => "Canada",
        "continent" => "North America"
    ],
   [
        "country" => "Cape Verde",
        "continent" => "Africa"
    ],
   [
        "country" => "Cayman Islands",
        "continent" => "North America"
    ],
   [
        "country" => "Central African Republic",
        "continent" => "Africa"
    ],
   [
        "country" => "Chad",
        "continent" => "Africa"
    ],
   [
        "country" => "Chile",
        "continent" => "South America"
    ],
   [
        "country" => "China",
        "continent" => "Asia"
    ],
   [
        "country" => "Christmas Island",
        "continent" => "Oceania"
    ],
   [
        "country" => "Cocos (Keeling) Islands",
        "continent" => "Oceania"
    ],
   [
        "country" => "Colombia",
        "continent" => "South America"
    ],
   [
        "country" => "Comoros",
        "continent" => "Africa"
    ],
   [
        "country" => "Congo",
        "continent" => "Africa"
    ],
   [
        "country" => "Cook Islands",
        "continent" => "Oceania"
    ],
   [
        "country" => "Costa Rica",
        "continent" => "North America"
    ],
   [
        "country" => "Croatia",
        "continent" => "Europe"
    ],
   [
        "country" => "Cuba",
        "continent" => "North America"
    ],
   [
        "country" => "Cyprus",
        "continent" => "Asia"
    ],
   [
        "country" => "Czech Republic",
        "continent" => "Europe"
    ],
   [
        "country" => "Denmark",
        "continent" => "Europe"
    ],
   [
        "country" => "Djibouti",
        "continent" => "Africa"
    ],
   [
        "country" => "Dominica",
        "continent" => "North America"
    ],
   [
        "country" => "Dominican Republic",
        "continent" => "North America"
    ],
   [
        "country" => "East Timor",
        "continent" => "Asia"
    ],
   [
        "country" => "Ecuador",
        "continent" => "South America"
    ],
   [
        "country" => "Egypt",
        "continent" => "Africa"
    ],
   [
        "country" => "El Salvador",
        "continent" => "North America"
    ],
   [
        "country" => "England",
        "continent" => "Europe"
    ],
   [
        "country" => "Equatorial Guinea",
        "continent" => "Africa"
    ],
   [
        "country" => "Eritrea",
        "continent" => "Africa"
    ],
   [
        "country" => "Estonia",
        "continent" => "Europe"
    ],
   [
        "country" => "Ethiopia",
        "continent" => "Africa"
    ],
   [
        "country" => "Falkland Islands",
        "continent" => "South America"
    ],
   [
        "country" => "Faroe Islands",
        "continent" => "Europe"
    ],
   [
        "country" => "Fiji Islands",
        "continent" => "Oceania"
    ],
   [
        "country" => "Finland",
        "continent" => "Europe"
    ],
   [
        "country" => "France",
        "continent" => "Europe"
    ],
   [
        "country" => "French Guiana",
        "continent" => "South America"
    ],
   [
        "country" => "French Polynesia",
        "continent" => "Oceania"
    ],
   [
        "country" => "French Southern territories",
        "continent" => "Antarctica"
    ],
   [
        "country" => "Gabon",
        "continent" => "Africa"
    ],
   [
        "country" => "Gambia",
        "continent" => "Africa"
    ],
   [
        "country" => "Georgia",
        "continent" => "Asia"
    ],
   [
        "country" => "Germany",
        "continent" => "Europe"
    ],
   [
        "country" => "Ghana",
        "continent" => "Africa"
    ],
   [
        "country" => "Gibraltar",
        "continent" => "Europe"
    ],
   [
        "country" => "Greece",
        "continent" => "Europe"
    ],
   [
        "country" => "Greenland",
        "continent" => "North America"
    ],
   [
        "country" => "Grenada",
        "continent" => "North America"
    ],
   [
        "country" => "Guadeloupe",
        "continent" => "North America"
    ],
   [
        "country" => "Guam",
        "continent" => "Oceania"
    ],
   [
        "country" => "Guatemala",
        "continent" => "North America"
    ],
   [
        "country" => "Guinea",
        "continent" => "Africa"
    ],
   [
        "country" => "Guinea-Bissau",
        "continent" => "Africa"
    ],
   [
        "country" => "Guyana",
        "continent" => "South America"
    ],
   [
        "country" => "Haiti",
        "continent" => "North America"
    ],
   [
        "country" => "Heard Island and McDonald Islands",
        "continent" => "Antarctica"
    ],
   [
        "country" => "Holy See (Vatican City State)",
        "continent" => "Europe"
    ],
   [
        "country" => "Honduras",
        "continent" => "North America"
    ],
   [
        "country" => "Hong Kong",
        "continent" => "Asia"
    ],
   [
        "country" => "Hungary",
        "continent" => "Europe"
    ],
   [
        "country" => "Iceland",
        "continent" => "Europe"
    ],
   [
        "country" => "India",
        "continent" => "Asia"
    ],
   [
        "country" => "Indonesia",
        "continent" => "Asia"
    ],
   [
        "country" => "Iran",
        "continent" => "Asia"
    ],
   [
        "country" => "Iraq",
        "continent" => "Asia"
    ],
   [
        "country" => "Ireland",
        "continent" => "Europe"
    ],
   [
        "country" => "Israel",
        "continent" => "Asia"
    ],
   [
        "country" => "Italy",
        "continent" => "Europe"
    ],
   [
        "country" => "Ivory Coast",
        "continent" => "Africa"
    ],
   [
        "country" => "Jamaica",
        "continent" => "North America"
    ],
   [
        "country" => "Japan",
        "continent" => "Asia"
    ],
   [
        "country" => "Jordan",
        "continent" => "Asia"
    ],
   [
        "country" => "Kazakhstan",
        "continent" => "Asia"
    ],
   [
        "country" => "Kenya",
        "continent" => "Africa"
    ],
   [
        "country" => "Kiribati",
        "continent" => "Oceania"
    ],
   [
        "country" => "Kuwait",
        "continent" => "Asia"
    ],
   [
        "country" => "Kyrgyzstan",
        "continent" => "Asia"
    ],
   [
        "country" => "Laos",
        "continent" => "Asia"
    ],
   [
        "country" => "Latvia",
        "continent" => "Europe"
    ],
   [
        "country" => "Lebanon",
        "continent" => "Asia"
    ],
   [
        "country" => "Lesotho",
        "continent" => "Africa"
    ],
   [
        "country" => "Liberia",
        "continent" => "Africa"
    ],
   [
        "country" => "Libyan Arab Jamahiriya",
        "continent" => "Africa"
    ],
   [
        "country" => "Liechtenstein",
        "continent" => "Europe"
    ],
   [
        "country" => "Lithuania",
        "continent" => "Europe"
    ],
   [
        "country" => "Luxembourg",
        "continent" => "Europe"
    ],
   [
        "country" => "Macao",
        "continent" => "Asia"
    ],
   [
        "country" => "North Macedonia",
        "continent" => "Europe"
    ],
   [
        "country" => "Madagascar",
        "continent" => "Africa"
    ],
   [
        "country" => "Malawi",
        "continent" => "Africa"
    ],
   [
        "country" => "Malaysia",
        "continent" => "Asia"
    ],
   [
        "country" => "Maldives",
        "continent" => "Asia"
    ],
   [
        "country" => "Mali",
        "continent" => "Africa"
    ],
   [
        "country" => "Malta",
        "continent" => "Europe"
    ],
   [
        "country" => "Marshall Islands",
        "continent" => "Oceania"
    ],
   [
        "country" => "Martinique",
        "continent" => "North America"
    ],
   [
        "country" => "Mauritania",
        "continent" => "Africa"
    ],
   [
        "country" => "Mauritius",
        "continent" => "Africa"
    ],
   [
        "country" => "Mayotte",
        "continent" => "Africa"
    ],
   [
        "country" => "Mexico",
        "continent" => "North America"
    ],
   [
        "country" => "Micronesia, Federated States of",
        "continent" => "Oceania"
    ],
   [
        "country" => "Moldova",
        "continent" => "Europe"
    ],
   [
        "country" => "Monaco",
        "continent" => "Europe"
    ],
   [
        "country" => "Mongolia",
        "continent" => "Asia"
    ],
   [
        "country" => "Montenegro",
        "continent" => "Europe"
    ],
   [
        "country" => "Montserrat",
        "continent" => "North America"
    ],
   [
        "country" => "Morocco",
        "continent" => "Africa"
    ],
   [
        "country" => "Mozambique",
        "continent" => "Africa"
    ],
   [
        "country" => "Myanmar",
        "continent" => "Asia"
    ],
   [
        "country" => "Namibia",
        "continent" => "Africa"
    ],
   [
        "country" => "Nauru",
        "continent" => "Oceania"
    ],
   [
        "country" => "Nepal",
        "continent" => "Asia"
    ],
   [
        "country" => "Netherlands",
        "continent" => "Europe"
    ],
   [
        "country" => "Netherlands Antilles",
        "continent" => "North America"
    ],
   [
        "country" => "New Caledonia",
        "continent" => "Oceania"
    ],
   [
        "country" => "New Zealand",
        "continent" => "Oceania"
    ],
   [
        "country" => "Nicaragua",
        "continent" => "North America"
    ],
   [
        "country" => "Niger",
        "continent" => "Africa"
    ],
   [
        "country" => "Nigeria",
        "continent" => "Africa"
    ],
   [
        "country" => "Niue",
        "continent" => "Oceania"
    ],
   [
        "country" => "Norfolk Island",
        "continent" => "Oceania"
    ],
   [
        "country" => "North Korea",
        "continent" => "Asia"
    ],
   [
        "country" => "Northern Ireland",
        "continent" => "Europe"
    ],
   [
        "country" => "Northern Mariana Islands",
        "continent" => "Oceania"
    ],
   [
        "country" => "Norway",
        "continent" => "Europe"
    ],
   [
        "country" => "Oman",
        "continent" => "Asia"
    ],
   [
        "country" => "Pakistan",
        "continent" => "Asia"
    ],
   [
        "country" => "Palau",
        "continent" => "Oceania"
    ],
   [
        "country" => "Palestine",
        "continent" => "Asia"
    ],
   [
        "country" => "Panama",
        "continent" => "North America"
    ],
   [
        "country" => "Papua New Guinea",
        "continent" => "Oceania"
    ],
   [
        "country" => "Paraguay",
        "continent" => "South America"
    ],
   [
        "country" => "Peru",
        "continent" => "South America"
    ],
   [
        "country" => "Philippines",
        "continent" => "Asia"
    ],
   [
        "country" => "Pitcairn",
        "continent" => "Oceania"
    ],
   [
        "country" => "Poland",
        "continent" => "Europe"
    ],
   [
        "country" => "Portugal",
        "continent" => "Europe"
    ],
   [
        "country" => "Puerto Rico",
        "continent" => "North America"
    ],
   [
        "country" => "Qatar",
        "continent" => "Asia"
    ],
   [
        "country" => "Reunion",
        "continent" => "Africa"
    ],
   [
        "country" => "Romania",
        "continent" => "Europe"
    ],
   [
        "country" => "Russian Federation",
        "continent" => "Europe"
    ],
   [
        "country" => "Rwanda",
        "continent" => "Africa"
    ],
   [
        "country" => "Saint Helena",
        "continent" => "Africa"
    ],
   [
        "country" => "Saint Kitts and Nevis",
        "continent" => "North America"
    ],
   [
        "country" => "Saint Lucia",
        "continent" => "North America"
    ],
   [
        "country" => "Saint Pierre and Miquelon",
        "continent" => "North America"
    ],
   [
        "country" => "Saint Vincent and the Grenadines",
        "continent" => "North America"
    ],
   [
        "country" => "Samoa",
        "continent" => "Oceania"
    ],
   [
        "country" => "San Marino",
        "continent" => "Europe"
    ],
   [
        "country" => "Sao Tome and Principe",
        "continent" => "Africa"
    ],
   [
        "country" => "Saudi Arabia",
        "continent" => "Asia"
    ],
   [
        "country" => "Scotland",
        "continent" => "Europe"
    ],
   [
        "country" => "Senegal",
        "continent" => "Africa"
    ],
   [
        "country" => "Serbia",
        "continent" => "Europe"
    ],
   [
        "country" => "Seychelles",
        "continent" => "Africa"
    ],
   [
        "country" => "Sierra Leone",
        "continent" => "Africa"
    ],
   [
        "country" => "Singapore",
        "continent" => "Asia"
    ],
   [
        "country" => "Slovakia",
        "continent" => "Europe"
    ],
   [
        "country" => "Slovenia",
        "continent" => "Europe"
    ],
   [
        "country" => "Solomon Islands",
        "continent" => "Oceania"
    ],
   [
        "country" => "Somalia",
        "continent" => "Africa"
    ],
   [
        "country" => "South Africa",
        "continent" => "Africa"
    ],
   [
        "country" => "South Georgia and the South Sandwich Islands",
        "continent" => "Antarctica"
    ],
   [
        "country" => "South Korea",
        "continent" => "Asia"
    ],
   [
        "country" => "South Sudan",
        "continent" => "Africa"
    ],
   [
        "country" => "Spain",
        "continent" => "Europe"
    ],
   [
        "country" => "Sri Lanka",
        "continent" => "Asia"
    ],
   [
        "country" => "Sudan",
        "continent" => "Africa"
    ],
   [
        "country" => "Suriname",
        "continent" => "South America"
    ],
   [
        "country" => "Svalbard and Jan Mayen",
        "continent" => "Europe"
    ],
   [
        "country" => "Swaziland",
        "continent" => "Africa"
    ],
   [
        "country" => "Sweden",
        "continent" => "Europe"
    ],
   [
        "country" => "Switzerland",
        "continent" => "Europe"
    ],
   [
        "country" => "Syria",
        "continent" => "Asia"
    ],
   [
        "country" => "Tajikistan",
        "continent" => "Asia"
    ],
   [
        "country" => "Tanzania",
        "continent" => "Africa"
    ],
   [
        "country" => "Thailand",
        "continent" => "Asia"
    ],
   [
        "country" => "The Democratic Republic of Congo",
        "continent" => "Africa"
    ],
   [
        "country" => "Togo",
        "continent" => "Africa"
    ],
   [
        "country" => "Tokelau",
        "continent" => "Oceania"
    ],
   [
        "country" => "Tonga",
        "continent" => "Oceania"
    ],
   [
        "country" => "Trinidad and Tobago",
        "continent" => "North America"
    ],
   [
        "country" => "Tunisia",
        "continent" => "Africa"
    ],
   [
        "country" => "Turkey",
        "continent" => "Asia"
    ],
   [
        "country" => "Turkmenistan",
        "continent" => "Asia"
    ],
   [
        "country" => "Turks and Caicos Islands",
        "continent" => "North America"
    ],
   [
        "country" => "Tuvalu",
        "continent" => "Oceania"
    ],
   [
        "country" => "Uganda",
        "continent" => "Africa"
    ],
   [
        "country" => "Ukraine",
        "continent" => "Europe"
    ],
   [
        "country" => "United Arab Emirates",
        "continent" => "Asia"
    ],
   [
        "country" => "United Kingdom",
        "continent" => "Europe"
    ],
   [
        "country" => "United States",
        "continent" => "North America"
    ],
   [
        "country" => "United States Minor Outlying Islands",
        "continent" => "Oceania"
    ],
   [
        "country" => "Uruguay",
        "continent" => "South America"
    ],
   [
        "country" => "Uzbekistan",
        "continent" => "Asia"
    ],
   [
        "country" => "Vanuatu",
        "continent" => "Oceania"
    ],
   [
        "country" => "Venezuela",
        "continent" => "South America"
    ],
   [
        "country" => "Vietnam",
        "continent" => "Asia"
    ],
   [
        "country" => "Virgin Islands, British",
        "continent" => "North America"
    ],
   [
        "country" => "Virgin Islands, U.S.",
        "continent" => "North America"
    ],
   [
        "country" => "Wales",
        "continent" => "Europe"
    ],
   [
        "country" => "Wallis and Futuna",
        "continent" => "Oceania"
    ],
   [
        "country" => "Western Sahara",
        "continent" => "Africa"
    ],
   [
        "country" => "Yemen",
        "continent" => "Asia"
    ],
   [
        "country" => "Zambia",
        "continent" => "Africa"
    ],
   [
        "country" => "Zimbabwe",
        "continent" => "Africa"
    ]
]

[
   "Afghanistan", "Åland Islands", "Albania", "Algeria", "American Samoa", "AndorrA", "Angola", "Anguilla", "Antarctica", "Antigua and Barbuda", "Argentina", "Armenia", "Aruba", "Australia", "Austria", "Azerbaijan", "Bahamas", "Bahrain", "Bangladesh", "Barbados", "Belarus", "Belgium", "Belize", "Benin", "Bermuda", "Bhutan", "Bolivia", "Bosnia and Herzegovina", "Botswana", "Bouvet Island", "Brazil", "British Indian Ocean Territory", "Brunei Darussalam", "Bulgaria", "Burkina Faso", "Burundi", "Cambodia", "Cameroon", "Canada", "Cape Verde", "Cayman Islands", "Central African Republic", "Chad", "Chile", "China", "Christmas Island", "Cocos (Keeling) Islands", "Colombia", "Comoros", "Congo", "Congo, The Democratic Republic of the", "Cook Islands", "Costa Rica", "Cote D\'Ivoire", "Croatia", "Cuba", "Cyprus", "Czech Republic", "Denmark", "Djibouti", "Dominica", "Dominican Republic", "Ecuador", "Egypt", "El Salvador", "Equatorial Guinea", "Eritrea", "Estonia", "Ethiopia", "Falkland Islands (Malvinas)", "Faroe Islands", "Fiji", "Finland", "France", "French Guiana", "French Polynesia", "French Southern Territories", "Gabon", "Gambia", "Georgia", "Germany", "Ghana", "Gibraltar", "Greece", "Greenland", "Grenada", "Guadeloupe", "Guam", "Guatemala", "Guernsey", "Guinea", "Guinea-Bissau", "Guyana", "Haiti", "Heard Island and Mcdonald Islands", "Holy See (Vatican City State)", "Honduras", "Hong Kong", "Hungary", "Iceland", "India", "Indonesia", "Iran, Islamic Republic Of", "Iraq", "Ireland", "Isle of Man", "Israel", "Italy", "Jamaica", "Japan", "Jersey", "Jordan", "Kazakhstan", "Kenya", "Kiribati", "Korea, Democratic People\'S Republic of", "Korea, Republic of", "Kuwait", "Kyrgyzstan", "Lao People\'S Democratic Republic", "Latvia", "Lebanon", "Lesotho", "Liberia", "Libyan Arab Jamahiriya", "Liechtenstein", "Lithuania", "Luxembourg", "Macao", "Macedonia, The Former Yugoslav Republic of", "Madagascar", "Malawi", "Malaysia", "Maldives", "Mali", "Malta", "Marshall Islands", "Martinique", "Mauritania", "Mauritius", "Mayotte", "Mexico", "Micronesia, Federated States of", "Moldova, Republic of", "Monaco", "Mongolia", "Montserrat", "Morocco", "Mozambique", "Myanmar", "Namibia", "Nauru", "Nepal", "Netherlands", "Netherlands Antilles", "New Caledonia", "New Zealand", "Nicaragua", "Niger", "Nigeria", "Niue", "Norfolk Island", "Northern Mariana Islands", "Norway", "Oman", "Pakistan", "Palau", "Palestinian Territory, Occupied", "Panama", "Papua New Guinea", "Paraguay", "Peru", "Philippines", "Pitcairn", "Poland", "Portugal", "Puerto Rico", "Qatar", "Reunion", "Romania", "Russian Federation", "RWANDA", "Saint Helena", "Saint Kitts and Nevis", "Saint Lucia", "Saint Pierre and Miquelon", "Saint Vincent and the Grenadines", "Samoa", "San Marino", "Sao Tome and Principe", "Saudi Arabia", "Senegal", "Serbia and Montenegro", "Seychelles", "Sierra Leone", "Singapore", "Slovakia", "Slovenia", "Solomon Islands", "Somalia", "South Africa", "South Georgia and the South Sandwich Islands", "Spain", "Sri Lanka", "Sudan", "Suriname", "Svalbard and Jan Mayen", "Swaziland", "Sweden", "Switzerland", "Syrian Arab Republic", "Taiwan, Province of China", "Tajikistan", "Tanzania, United Republic of", "Thailand", "Timor-Leste", "Togo", "Tokelau", "Tonga", "Trinidad and Tobago", "Tunisia", "Turkey", "Turkmenistan", "Turks and Caicos Islands", "Tuvalu", "Uganda", "Ukraine", "United Arab Emirates", "United Kingdom", "United States", "United States Minor Outlying Islands", "Uruguay", "Uzbekistan", "Vanuatu", "Venezuela", "Viet Nam", "Virgin Islands, British", "Virgin Islands, U.S.", "Wallis and Futuna", "Western Sahara", "Yemen", "Zambia", "Zimbabwe"
]
*/