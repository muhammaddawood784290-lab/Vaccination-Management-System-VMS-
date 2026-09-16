<?php

namespace Database\Seeders;

use App\Models\Vaccine;
use Illuminate\Database\Seeder;

class VaccineSeeder extends Seeder
{
    public function run(): void
    {
        $vaccines = [
            [
                'name' => 'Bacillus Calmette-Guerin (BCG)',
                'code' => 'BCG',
                'doses' => 1,
                'age_range' => 'At birth',
                'type' => 'Live attenuated',
                'manufacturer' => 'Serum Institute of India',
                'description' => 'Protects against tuberculosis. Given at birth in countries with high TB prevalence.',
                'status' => 'active',
            ],
            [
                'name' => 'Hepatitis B',
                'code' => 'HEPB',
                'doses' => 3,
                'age_range' => 'Birth, 1 month, 6 months',
                'type' => 'Recombinant',
                'manufacturer' => 'GlaxoSmithKline',
                'description' => 'Protects against Hepatitis B virus infection.',
                'status' => 'active',
            ],
            [
                'name' => 'Oral Polio Vaccine (OPV)',
                'code' => 'OPV',
                'doses' => 4,
                'age_range' => '6 weeks, 10 weeks, 14 weeks, 4-6 years',
                'type' => 'Live attenuated',
                'manufacturer' => 'Sanofi Pasteur',
                'description' => 'Protects against poliomyelitis. Administered orally.',
                'status' => 'active',
            ],
            [
                'name' => 'Pentavalent Vaccine',
                'code' => 'PENTA',
                'doses' => 3,
                'age_range' => '6 weeks, 10 weeks, 14 weeks',
                'type' => 'Combination',
                'manufacturer' => 'Serum Institute of India',
                'description' => 'Protects against Diphtheria, Tetanus, Pertussis, Hepatitis B, and Haemophilus influenzae type b.',
                'status' => 'active',
            ],
            [
                'name' => 'Measles-Mumps-Rubella (MMR)',
                'code' => 'MMR',
                'doses' => 2,
                'age_range' => '9 months, 15 months',
                'type' => 'Live attenuated',
                'manufacturer' => 'Merck & Co.',
                'description' => 'Protects against Measles, Mumps, and Rubella.',
                'status' => 'active',
            ],
            [
                'name' => 'Rotavirus Vaccine',
                'code' => 'ROTAV',
                'doses' => 3,
                'age_range' => '6 weeks, 10 weeks, 14 weeks',
                'type' => 'Live attenuated',
                'manufacturer' => 'GlaxoSmithKline',
                'description' => 'Protects against rotavirus gastroenteritis.',
                'status' => 'active',
            ],
            [
                'name' => 'Pneumococcal Conjugate Vaccine (PCV)',
                'code' => 'PCV',
                'doses' => 4,
                'age_range' => '6 weeks, 10 weeks, 14 weeks, 12-15 months',
                'type' => 'Conjugate',
                'manufacturer' => 'Pfizer',
                'description' => 'Protects against pneumococcal diseases including pneumonia and meningitis.',
                'status' => 'active',
            ],
            [
                'name' => 'Inactivated Polio Vaccine (IPV)',
                'code' => 'IPV',
                'doses' => 1,
                'age_range' => '14 weeks',
                'type' => 'Inactivated',
                'manufacturer' => 'Sanofi Pasteur',
                'description' => 'Injectable polio vaccine used as a booster alongside OPV.',
                'status' => 'active',
            ],
            [
                'name' => 'Diphtheria-Tetanus-Pertussis (DTaP)',
                'code' => 'DTAP',
                'doses' => 5,
                'age_range' => '2 months, 4 months, 6 months, 15-18 months, 4-6 years',
                'type' => 'Toxoid/acellular',
                'manufacturer' => 'Sanofi Pasteur',
                'description' => 'Protects against Diphtheria, Tetanus, and Pertussis (Whooping Cough).',
                'status' => 'active',
            ],
            [
                'name' => 'Human Papillomavirus (HPV)',
                'code' => 'HPV',
                'doses' => 2,
                'age_range' => '9-14 years',
                'type' => 'Recombinant',
                'manufacturer' => 'Merck & Co.',
                'description' => 'Protects against HPV infections that can cause cervical cancer.',
                'status' => 'active',
            ],
        ];

        foreach ($vaccines as $vaccine) {
            Vaccine::create($vaccine);
        }
    }
}
