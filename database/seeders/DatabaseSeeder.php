<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $alumni = \App\Models\Alumnus::factory(30)->create();

        foreach ([
        'angol/anglisztika',
        'biológia',
        'egyéb',
        'fizika',
        'földrajz',
        'földtudomány',
        'gazdálkodás és menedzsment',
        'germanisztika (holland)',
        'germanisztika (német)',
        'germanisztika (skandinavisztika)',
        'keleti nyelvek és kultúrák (arab)',
        'keleti nyelvek és kultúrák (hebraisztika)',
        'keleti nyelvek és kultúrák (indológia)',
        'keleti nyelvek és kultúrák (iranisztika)',
        'keleti nyelvek és kultúrák (japán)',
        'keleti nyelvek és kultúrák (koreai)',
        'keleti nyelvek és kultúrák (kínai)',
        'keleti nyelvek és kultúrák (mongol)',
        'keleti nyelvek és kultúrák (tibeti)',
        'keleti nyelvek és kultúrák (török)',
        'keleti nyelvek és kultúrák (újgörög)',
        'kereskedelem és marketing',
        'kommunikáció- és médiatudomány',
        'kémia',
        'könyvtár/(informatikus)-könyvtáros',
        'környezettan',
        'magyar',
        'matematika',
        'művészettörténet',
        'nemzetközi gazdálkodás',
        'nemzetközi tanulmányok',
        'népművelés',
        'néprajz',
        'pedagógia',
        'programtervező informatikus/programozó matematikus',
        'pszichológia',
        'pénzügy és számvitel',
        'régészet',
        'szabad bölcsészet - esztétika',
        'szabad bölcsészet - film',
        'szabad bölcsészet - filozófia',
        'szabad bölcsészet - művészettörténet',
        'szlavisztika (bolgár)',
        'szlavisztika (cseh)',
        'szlavisztika (horvát)',
        'szlavisztika (lengyel)',
        'szlavisztika (orosz)',
        'szlavisztika (szerb)',
        'szlavisztika (szlovák)',
        'szlavisztika (szlovén)',
        'szlavisztika (ukrán)',
        'szociológia',
        'szociális munka',
        'tudományos szocializmus',
        'történelem',
        'zenekultúra',
        'ókori nyelvek és kultúrák (asszirológia)',
        'ókori nyelvek és kultúrák (egyiptológia)',
        'ókori nyelvek és kultúrák (klasszika-filológia, latin, ógörög)',
        'újlatin nyelvek és kultúrák (francia)',
        'újlatin nyelvek és kultúrák (olasz)',
        'újlatin nyelvek és kultúrák (portugál)',
        'újlatin nyelvek és kultúrák (román)',
        'újlatin nyelvek és kultúrák (spanyol)',
] as $major) {
            \App\Models\Major::factory()->create([
                'name' => $major,
            ]);
        }
        foreach (\App\Models\FurtherCourse::$further_courses_enum as $further_course) {
            \App\Models\FurtherCourse::factory()->create([
                'name' => $further_course,
            ]);
        }
        foreach (\App\Models\ResearchField::$research_fields_enum as $research_field) {
            \App\Models\ResearchField::factory()->create([
                'name' => $research_field,
            ]);
        }
        foreach (\App\Models\UniversityFaculty::$university_faculties_enum as $university_faculty) {
            \App\Models\UniversityFaculty::factory()->create([
                'name' => $university_faculty,
            ]);
        }
        foreach (\App\Models\ScientificDegree::$scientific_degrees_enum as $scientific_degree) {
            \App\Models\ScientificDegree::factory()->create([
                'name' => $scientific_degree,
            ]);
        }

        $majors = DB::table('majors')->pluck('id');
        $further_courses = DB::table('further_courses')->pluck('id');
        $scientific_degrees = DB::table('scientific_degrees')->pluck('id');
        $research_fields = DB::table('research_fields')->pluck('id');
        $university_faculties = DB::table('university_faculties')->pluck('id');





        $alumni->each(function ($alumnus) use (&$majors, &$further_courses, &$scientific_degrees, &$research_fields, &$university_faculties) {
            // Add major
            $alumnus->majors()->sync(
                $majors->random(rand(1,4))
            );

            // Add further course
            $alumnus->further_courses()->sync(
                $further_courses->random(rand(1,4))
            );

            // Add scientific degree with random year
            $degree_cnt = rand(1,4);
            $degree_ids = $scientific_degrees->random($degree_cnt);
            $years = collect(range(1990, 2010))
                        ->map(function (int $year) {
                            //this way there are also going to be nulls
                            return ($year < 2000) ? null : $year;
                        })
                        ->random($degree_cnt);
            for ($i=0; $i<$degree_cnt; ++$i) {
                $alumnus->scientific_degrees()->attach(
                    $degree_ids[$i],
                    ['year' => $years[$i]]
                );
            }

            // Add research field
            $alumnus->research_fields()->sync(
                $research_fields->random(rand(1,4))
            );

            // Add universityfaculty
            $alumnus->university_faculties()->sync(
                $university_faculties->random(rand(1,4))
            );

        });

        \App\Models\User::factory()->create([
             'name' => 'Admin',
             'email' => 'root@eotvos.elte.hu',
             'is_admin' => true,
             'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // the string 'password' encrypted
        ]);
    }
}
