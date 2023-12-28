<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\PhoneCode;

class PhoneCodeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $phoneCodes = $this->getPhoneCodes();

        foreach ($phoneCodes as $country => $code) {
            PhoneCode::create([
                'country' => $country,
                'code' => $code,
            ]);
        }
}

/**
     * Get phone codes from JSON data.
     *
     * @return array
     */
    private function getPhoneCodes(): array
    {
        $json = '{"Afghanistan": "93", "Åland": "358", "Albania": "355", "Algeria": "213", 
            "American Samoa": "1", "Andorra": "376", "Angola": "244", "Anguilla": "1", 
            "Antigua and Barbuda": "1", "Argentina": "54", "Armenia": "374", "Artsakh": "374",
            "Aruba": "297", "Ascension Island": "247", "Australia": "61", "Austria": "43",
            "Azerbaijan": "994", "Bahamas": "1", "Bahrain": "973", "Bangladesh": "880",
            "Barbados": "1", "Belarus": "375", "Belgium": "32", "Belize": "501",
            "Benin": "229", "Bermuda": "1", "Bhutan": "975","Bolivia": "591","Bonaire": "599",
            "Bosnia and Herzegovina": "387","Botswana": "267","Brazil": "55",
            "British Indian Ocean Territory": "246","British Virgin Islands": "1",
            "Brunei": "673","Bulgaria": "359","Burkina Faso": "226","Burma": "95","Burundi": "257",
            "Cambodia": "855","Cameroon": "237","Canada": "1","Cape Verde": "238",
            "Cayman Islands": "1","Central African Republic": "236","Chad": "235",
            "Chagos Islands": "246","Chile": "56","China": "86","Christmas Island": "61",
            "Cocos (Keeling) Islands": "61","Colombia": "57","Comoros": "269",
            "Congo, Dem. Rep. of (Zaire)": "243","Congo, Republic of the": "242",
            "Cook Islands": "682","Costa Rica": "506","Côte d\'Ivoire": "225","Croatia": "385",
            "Cuba": "53","Curaçao": "599", "Cyprus": "357","Czech Republic": "420","Denmark": "45",
            "Diego Garcia": "246","Djibouti": "253","Dominica": "1","Dominican Republic": "1",
            "East Timor": "670","Easter Island": "56","Ecuador": "593","Egypt": "20",
            "El Salvador": "503","England": "44","Equatorial Guinea": "240","Eritrea": "291",
            "Estonia": "372","Eswatini": "268","Ethiopia": "251",
            "Falkland Islands (Malvinas)": "500","Faroe Islands": "298","Fiji": "679",
            "Finland": "358","France": "33","French Guiana": "594","French Polynesia": "689",
            "Gabon": "241","Gambia": "220","Georgia": "995","Germany": "49","Ghana": "233",
            "Gibraltar": "350","Greece": "30","Greenland": "299","Grenada": "1","Guadeloupe": "590",
            "Guam": "1","Guatemala": "502","Guernsey": "44", "Guinea": "224", 
            "Guinea-Bissau": "245", "Guyana": "592","Haiti": "509","Honduras": "504",
            "Hong Kong": "852","Hungary": "36","Iceland": "354","India": "91","Indonesia": "62",
            "Iran": "98","Iraq": "964","Ireland": "353","Isle of Man": "44","Israel": "972",
            "Italy": "39","Jamaica": "1","Japan": "81","Jersey": "44","Jordan": "962",
            "Kazakhstan": "7","Kenya": "254","Kiribati": "686","Korea, North": "850",
            "Korea, South": "82","Kosovo": "383","Kuwait": "965","Kyrgyzstan": "996","Laos": "856",
            "Latvia": "371","Lebanon": "961","Lesotho": "266","Liberia": "231","Libya": "218",
            "Liechtenstein": "423","Lithuania": "370","Luxembourg": "352","Macau": "853",
            "Madagascar": "261","Malawi": "265","Malaysia": "60","Maldives": "960","Mali": "223",
            "Malta": "356","Marshall Islands": "692","Martinique": "596","Mauritania": "222",
            "Mauritius": "230","Mayotte": "262","Mexico": "52",
            "Micronesia, Federated States of": "691","Moldova": "373","Monaco": "377",
            "Mongolia": "976","Montenegro": "382","Montserrat": "1","Morocco": "212",
            "Mozambique": "258","Namibia": "264","Nauru": "674","Nepal": "977","Netherlands": "31",
            "New Caledonia": "687","New Zealand": "64","Nicaragua": "505","Niger": "227",
            "Nigeria": "234","Niue": "683","Norfolk Island": "672","North Macedonia": "389",
            "Northern Ireland": "44","Northern Mariana Islands": "1","Norway": "47",
            "Oman": "968","Pakistan": "92","Palau": "680","Palestine, State of": "970",
            "Panama": "507","Papua New Guinea": "675","Paraguay": "595","Peru": "51",
            "Philippines": "63","Pitcairn Islands": "64","Poland": "48","Portugal": "351",
            "Puerto Rico": "1","Qatar": "974","Réunion": "262","Romania": "40","Russia": "7",
            "Rwanda": "250","Saba": "599","Saint Barthélemy": "590",
            "Saint Helena, Ascension and Tristan da Cunha": "290","Saint Kitts and Nevis": "1",
            "Saint Lucia": "1","Saint Martin (French part)": "590",
            "Saint Pierre and Miquelon": "508","Saint Vincent and the Grenadines": "1",
            "Samoa": "685","San Marino": "378","São Tomé and Príncipe": "239","Saudi Arabia": "966",         
            "Scotland": "44","Senegal": "221","Serbia": "381","Seychelles": "248",
            "Sierra Leone": "232","Singapore": "65","Sint Eustatius": "599",
            "Sint Maarten (Dutch part)": "1","Slovakia": "421","Slovenia": "386",
            "Solomon Islands": "677","Somalia": "252","South Africa": "27",
            "South Georgia and the South Sandwich Islands": "500","South Sudan": "211",
            "Spain": "34","Sri Lanka": "94","Sudan": "249","Suriname": "597",
            "Svalbard and Jan Mayen": "47","Sweden": "46","Switzerland": "41","Syria": "963",
            "Taiwan": "886","Tajikistan": "992","Tanzania": "255","Thailand": "66","Togo": "228",
            "Tokelau": "690","Tonga": "676","Trinidad and Tobago": "1","Tristan da Cunha": "290",
            "Tunisia": "216","Turkey": "90","Turkmenistan": "993","Turks and Caicos Islands": "1",
            "Tuvalu": "688","Uganda": "256","Ukraine": "380","United Arab Emirates": "971",
            "United Kingdom": "44","United States": "1","Uruguay": "598","Uzbekistan": "998",
            "Vanuatu": "678","Vatican City": "39","Venezuela": "58","Vietnam": "84",
            "Virgin Islands, British": "1","Virgin Islands, U.S.": "1","Wales": "44",
            "Wallis and Futuna": "681","Western Sahara": "212","Yemen": "967","Zambia": "260",
            "Zimbabwe": "263"
    }';

        return json_decode($json, true);
    }


}
