<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // $this->call('UsersTableSeeder');

        DB::table('company')->insert([
            [ 'company_name' => 'Square Pharmaceuticals Ltd'],
            [ 'company_name' => 'Eskayef Bangladesh Ltd'],
            [ 'company_name' => 'Novo Nordisk'],
            [ 'company_name' => 'Radiant Pharmaceuticals Ltd'],
            [ 'company_name' => 'Unimed And Unihealth Ltd'],
            [ 'company_name' => 'Pacific Pharmaceuticals Ltd'],
            [ 'company_name' => 'Active Fine Chemicals Ltd'],
            [ 'company_name' => 'Aristopharma Ltd'],
            [ 'company_name' => 'Incepta Pharmaceuticals Ltd'],
            [ 'company_name' => 'Popular Pharmaceuticals Ltd'],
            [ 'company_name' => 'Healthcare Pharmaceuticals Ltd'],
            [ 'company_name' => 'Globe Pharmaceuticals Ltd'],
            [ 'company_name' => 'Renata Ltd'],
            [ 'company_name' => 'Opsonin Pharma Ltd'],
            [ 'company_name' => 'ACI Ltd'],
            [ 'company_name' => 'Ibn Sina Pharmaceutical Ind. Ltd'],
            [ 'company_name' => 'Beacon Pharmaceuticals Ltd'],
            [ 'company_name' => 'Beximco Pharmaceuticals Ltd'],
            [ 'company_name' => 'Acme Laboratories Ltd'],
            [ 'company_name' => 'Sanofi-Aventis Bangladesh Ltd'],
            [ 'company_name' => 'General Pharmaceuticals Ltd'],
            [ 'company_name' => 'Zuellig Pharma Bangladesh Ltd'],
            [ 'company_name' => 'Orion Pharma Ltd'],
            [ 'company_name' => 'GlaxoSmithKline Bangladesh Ltd'],
            [ 'company_name' => 'Nuvista Pharma Ltd'],
            [ 'company_name' => 'Reckitt Benkiser BD Ltd'],
            [ 'company_name' => 'Novartis (Bangladesh) Ltd'],
            [ 'company_name' => 'Drug International Ltd'],
            [ 'company_name' => 'Sanofi-Aventis Bangladesh Ltd'],
            [ 'company_name' => 'Nipro JMI Pharma Ltd.'],
            [ 'company_name' => 'Biopharma Ltd'],
        ]);
    }
}
