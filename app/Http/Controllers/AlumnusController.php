<?php

namespace App\Http\Controllers;

use App\Models\Alumnus;
use \App\Models\UniversityFaculty;
use \App\Models\ResearchField;
use \App\Models\FurtherCourse;
use \App\Models\Major;
use \App\Models\ScientificDegree;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;
use App\Policies\AlumnusPolicy;

class AlumnusController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();
        if ($user && $user->can('create', Alumnus::class)) {
            //if there is a draft pair, we only show the drafts
            $idsHavingDraftPairs = DB::table('alumni')->where('is_draft', false)->whereNotNull('pair_id')->pluck('id');

            return view('alumni.index', [
                'alumni' => \App\Models\Alumnus::whereNotIn('id', $idsHavingDraftPairs)
                    ->orderBy('is_draft', 'desc')->orderBy('name') // the first ones will be those having a draft
                    ->paginate(12),
                'majors_enum' => Major::majorsEnum(),
                'further_courses_enum' => FurtherCourse::$further_courses_enum,
                'scientific_degrees_enum' => ScientificDegree::$scientific_degrees_enum,
                'research_fields_enum' => ResearchField::$research_fields_enum,
            ]);
        } else {
            return view('alumni.index', [
                'alumni' => \App\Models\Alumnus::where('is_draft', false)
                    ->orderBy('name')
                    ->paginate(12),
                'majors_enum' => Major::majorsEnum(),
                'further_courses_enum' => FurtherCourse::$further_courses_enum,
                'scientific_degrees_enum' => ScientificDegree::$scientific_degrees_enum,
                'research_fields_enum' => ResearchField::$research_fields_enum,
            ]);
        }
    }

    public function searchAlumni(Request $request)
    {
        // TODO: megoldani hogy pagination oldal váltásnál is megmaradjon a szűrés
        // vagy legalább ha üres a request akkor legyen pagination

        $name = $request->input('name');
        $start_of_membership = $request->input('start_of_membership');
        $major = $request->input('major');
        $further_course = $request->input('further_course');
        $scientific_degree = $request->input('scientific_degree');
        $research_field = $request->input('research_field');

        $query = Alumnus::query();

        if (isset($name)) {
            $query->where('name', 'LIKE', "%$name%");
        }

        if (isset($start_of_membership)) {
            $query->where('start_of_membership', $start_of_membership);
        }

        if (isset($major)) {
            $query->whereHas('majors', function (Builder $q) use ($major) {
                $q->where('name', $major);
            });
        }

        if (isset($further_course)) {
            $query->whereHas('further_courses', function (Builder $q) use ($further_course) {
                $q->where('name', $further_course);
            });
        }

        if (isset($scientific_degree)) {
            $query->whereHas('scientific_degrees', function (Builder $q) use ($scientific_degree) {
                $q->where('name', $scientific_degree);
            });
        }

        if (isset($research_field)) {
            $query->whereHas('research_fields', function (Builder $q) use ($research_field) {
                $q->where('name', $research_field);
            });
        }

        $user = Auth::user();
        if ($user && $user->can('create', Alumnus::class)) {
            $idsHavingDraftPairs = DB::table('alumni')->where('is_draft', false)->whereNotNull('pair_id')->pluck('id');
            $alumni = $query->whereNotIn('id', $idsHavingDraftPairs)->orderBy('name')->paginate(12);
        } else {
            $alumni = $query->where('is_draft', false)->orderBy('name')->paginate(12);
        }

        return view('alumni.index', [
            'alumni' => $alumni,
            'search' => true,
            'majors_enum' => Major::majorsEnum(),
            'further_courses_enum' => FurtherCourse::$further_courses_enum,
            'scientific_degrees_enum' => ScientificDegree::$scientific_degrees_enum,
            'research_fields_enum' => ResearchField::$research_fields_enum,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('alumni.create_or_edit', [
            'university_faculties' => UniversityFaculty::$university_faculties_enum,
            'majors' => Major::majorsEnum(),
            'further_courses' => FurtherCourse::$further_courses_enum,
            'scientific_degrees' => ScientificDegree::$scientific_degrees_enum,
            'research_fields' => ResearchField::$research_fields_enum,
        ]);
    }

    /**
     * Show the form for importing new alumni from a spreadsheet file.
     *
     * @return \Illuminate\Http\Response
     */
    public function import_create()
    {
        if(!Auth::user() || !Auth::user()->is_admin) abort(403);
        return view('alumni.import', ['']);
    }

    /**
     * Validates a store/update request and returns an array containing the validated keys and values.
     */
    private static function validateRequest(Request $request): array
    {
        // TODO: kar, szak stb.
        return $request->validate(
            [
                'name' => 'required|min:3',
                'email' => 'nullable|email',
                'birth_date' => 'nullable|numeric|gt:1930',
                'birth_place' => 'nullable|min:3',
                'high_school' => 'nullable|min:3',
                'graduation_date' => 'nullable|numeric|gt:1930',
                'further_course_detailed' => 'nullable|max:2000',
                'start_of_membership' => 'nullable|numeric|gt:1930',
                'recognations' => 'nullable|max:2000',
                'research_field_detailed' => 'nullable|max:2000',
                'links' => 'nullable|max:2000',
                'works' => 'nullable|max:2000',
                'university_faculties' => 'nullable|array',
                'majors' => 'nullable|array',
                'further_courses' => 'nullable|array',
                'scientific_degrees' => 'nullable|array',
                'research_fields' => 'nullable|array',
                'dla_year' => 'nullable|numeric|gt:1930',
                'hab_year' => 'nullable|numeric|gt:1930',
                'mta_year' => 'nullable|numeric|gt:1930',
                'candidate_year' => 'nullable|numeric|gt:1930',
                'doctor_year' => 'nullable|numeric|gt:1930',
                'phd_year' => 'nullable|numeric|gt:1930',
            ]
        );
    }

    /**
     * Validates and then creates an alumnus from
     * a given request, a given draft bit and a given pair id (the latter can be null too).
     * This function is used both in `store` and in `update`.
     */
    private static function validateAndStore(Request $request, bool $isDraft, ?int $pairId) : Alumnus
    {
        $validated = AlumnusController::validateRequest($request);

        // TODO: id?
        $alumnus = Alumnus::factory()->create([
            'is_draft' => $isDraft,
            'pair_id' => $pairId,
            'name' => $validated['name'],
            'email' => $validated['email'],
            'birth_date' => $validated['birth_date'],
            'birth_place' => $validated['birth_place'],
            'high_school' => $validated['high_school'],
            'graduation_date' => $validated['graduation_date'],
            'further_course_detailed' => $validated['further_course_detailed'],
            'start_of_membership' => $validated['start_of_membership'],
            'recognations' => $validated['recognations'],
            'research_field_detailed' => $validated['research_field_detailed'],
            'links' => $validated['links'],
            'works' => $validated['works'],
        ]);
        AlumnusController::synchroniseConnections($alumnus, $validated);
        return $alumnus;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $user = Auth::user();
        // it will be a draft if:
        //   they are guests, or
        //   they are registered and cannot create non-draft entries but can create drafts
        $isDraft = (!$user) || (!$user->can('create', Alumnus::class) && $user->can('createDraft', Alumnus::class));

        $alumnus = AlumnusController::validateAndStore($request, $isDraft, null);

        // University faculty
        // olyan unifaculty létrehozása, ami még nincs, a többi id lekérése database-ből és szinkronizálás
        if (isset($validated["university_faculties"])) {
            $existing_university_faculties = Arr::flatten(UniversityFaculty::select('name')->get()->makeHIdden('pivot')->toArray());
            $missing_university_faculties = array_diff($validated['university_faculties'], $existing_university_faculties);
            foreach ($missing_university_faculties as $faculty) {
                UniversityFaculty::factory()->create([
                    'name' => $faculty
                ]);
            }
            $university_faculty_ids = Arr::flatten(UniversityFaculty::select('id')->whereIn('name', $validated['university_faculties'])->get()->makeHIdden('pivot')->toArray());
            $alumnus->university_faculties()->sync($university_faculty_ids);
        }

        // Major
        if (isset($validated["majors"])) {
            $existing_majors = Arr::flatten(Major::select('name')->get()->makeHIdden('pivot')->toArray());
            $missing_majors = array_diff($validated['majors'], $existing_majors);
            foreach ($missing_majors as $major) {
                Major::factory()->create([
                    'name' => $major
                ]);
            }
            $major_ids = Arr::flatten(Major::select('id')->whereIn('name', $validated['majors'])->get()->makeHIdden('pivot')->toArray());
            $alumnus->majors()->sync($major_ids);
        }

        // Further courses
        if (isset($validated["further_courses"])) {
            $existing_further_courses = Arr::flatten(FurtherCourse::select('name')->get()->makeHIdden('pivot')->toArray());
            $missing_further_courses = array_diff($validated['further_courses'], $existing_further_courses);
            foreach ($missing_further_courses as $further_course) {
                FurtherCourse::factory()->create([
                    'name' => $further_course
                ]);
            }
            $ids = Arr::flatten(FurtherCourse::select('id')->whereIn('name', $validated['further_courses'])->get()->makeHIdden('pivot')->toArray());
            $alumnus->further_courses()->sync($ids);
        }

        // Scientific degree
        if (isset($validated["scientific_degrees"])) {
            foreach ($validated["scientific_degrees"] as $degree_name) {
                // here we assume we only choose from the pre-defined degrees (hence firstOrFail)
                $degree = ScientificDegree::where('name', $degree_name)->firstOrFail();
                $alumnus->scientific_degrees()->sync([$degree->id => ['year' =>
                    isset($validated['doctor_year']) && strcmp($degree_name, 'egyetemi doktor') == 0 ? $validated['doctor_year'] :
                    (isset($validated['candidate_year']) && strcmp($degree_name, 'kandidátus') == 0 ? $validated['candidate_year'] :
                    (isset($validated['mta_year']) && strcmp($degree_name, 'tudományok doktora/MTA doktora') == 0 ? $validated['mta_year'] :
                    (isset($validated['hab_year']) && strcmp($degree_name, 'habilitáció') == 0 ? $validated['hab_year'] :
                    (isset($validated['phd_year']) && strcmp($degree_name, 'PhD') == 0 ? $validated['phd_year'] :
                    (isset($validated['dla_year']) && strcmp($degree_name, 'DLA') == 0 ? $validated['dla_year'] : null)))))
                ]]);
            }
        }

        // Research fields
        if (isset($validated["research_fields"])) {
            $existing_research_fields = Arr::flatten(ResearchField::select('name')->get()->makeHIdden('pivot')->toArray());
            $missing_research_fields = array_diff($validated['research_fields'], $existing_research_fields);
            foreach ($missing_research_fields as $research_field) {
                ResearchField::factory()->create([
                    'name' => $research_field
                ]);
            }
            $ids = Arr::flatten(ResearchField::select('id')->whereIn('name', $validated['research_fields'])->get()->makeHIdden('pivot')->toArray());
            $alumnus->research_fields()->sync($ids);
        }

        Session::flash('alumnus_created', $alumnus->name);

        if ($isDraft) {
            return Redirect::route('alumni.index')
                     ->with('success','Az adatokat elmentettük; egy adminisztrátor jóváhagyása után lesznek elérhetőek. Köszönjük!');
        } else {
            return Redirect::route('alumni.show', $alumnus);
        }
    }

    const ALPHABET=["A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z",
                    "AA","AB","AC"];

    /**Extracts the rows from the worksheet into an array, from $startingRow (starting from zero) until the end, from the first column until $lastColumn (starting from zero).
     * $lastColumn must be <26 for now.
     */
    public static function worksheet_to_array(\PhpOffice\PhpSpreadsheet\Worksheet\Worksheet $worksheet, int $startingRow, int $lastColumn): array
    {
        $highestRow = $worksheet->getHighestRow();
        $arr=array();
        $lastColLet = AlumnusController::ALPHABET[$lastColumn];
        for ($i = $startingRow+1; $i <= $highestRow; ++$i) //indexing starts from 1 here
        {
            $arr[] = array_values( //converting to an indexed array; otherwise it would have to be indexed by letters
                $worksheet->rangeToArray( //appending it to the end
                    "A$i:$lastColLet$i",     // The worksheet range that we want to retrieve
                    NULL,        // Value that should be returned for empty cells
                    TRUE,        // Should formulas be calculated (the equivalent of getCalculatedValue() for each cell)
                    TRUE,        // Should values be formatted (the equivalent of getFormattedValue() for each cell)
                    TRUE         // Should the array be indexed by cell row and cell column
                )[$i] //it would otherwise add an extra dimension
            );
        }
        return $arr;
    }

    /**Maps extensions to PhpSpreadsheet's file descriptors. */
    const EXTENSION_TO_DESCRIPTOR = [
        'txt' => 'Csv', //Laravel recognizes csv files as txt
        'ods' => 'Ods',
        'xls' => 'Xls',
        'xlsx' => 'Xlsx'
    ];

    /**
     * Breaks lists of faculties, degrees etc. into arrays and searches for the corresponding ids.
     * If there is no corresponding id, it leaves the string in the array and sets the first element of the tuple left in the array to false.
     * Otherwise, it sets it to true and puts the id there.
     * If $longstring is null, it returns an empty array.
     */
    private static function ids_from_string(string $separator, ?string $longstring, string $table): array {
        if (!isset($longstring)) return [];
        return array_filter(array_map(function($string) use ($table): array {
            $string = trim($string);

            $id = DB::table($table)->where('name', $string)->value('id');
            if (isset($id)) return [true, $id];
            else return [false, $string];
        }, explode($separator, $longstring)),
        function($object) {return isset($object);});
    }

    /**
     * Returns whether there is at least one non-null value in an array.
     * Is useful for filtering empty rows.
     */
    private static function hasNonNull(array $arr): bool {
        foreach ($arr as $val) {
            if (!is_null($val)) return true;
        }
        return false;
    }

    /**
     * Maps a degree name to the corresponding index in the worksheet.
     */
    const DEGREE_NAME_TO_INDEX = [
        'egyetemi doktor' => 12,
        'kandidátus' => 13,
        'tudományok doktora/MTA doktora' => 14,
        'PhD' => 15,
        'habilitáció' => 16,
        'DLA' => 17,
    ];

    /**
     * Handles a request with an uploaded worksheet file that contains more than one alumni.
     * Extracts the data and stores it in new Alumnus objects.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function import_store(Request $request)
    {
        if(!Auth::user() || !Auth::user()->is_admin) abort(403);
        $request->validate(
            [
                'file' =>  'file',
            ]
        );

        $file = $request->file('file');
        if (null == $file)
        {
            return redirect()->back()->with('message', 'Nincs kiválasztva fájl.');
        }

        $extension = $file->extension(); //Laravel guesses the extension based on file content
        if (!isset( AlumnusController::EXTENSION_TO_DESCRIPTOR[$extension] )) {
            return redirect()->back()->with('error', 'Nem támogatott fájlformátum.');
        }

        $reader = \PhpOffice\PhpSpreadsheet\IOFactory::createReader(
            AlumnusController::EXTENSION_TO_DESCRIPTOR[$extension]
        );
        $reader->setReadDataOnly(true); //we don't care about formatting
        //maybe a ReadFilter for only the cells in the appropriate columns?
        $spreadsheet = $reader->load($file->getRealPath());
        $worksheet = $spreadsheet->getActiveSheet();

        $rows = AlumnusController::worksheet_to_array($worksheet, 1, 26);

        //TODO: validating data from spreadsheet

        $len = count($rows);
        if ($len > 0)
        {
            $firstone = $rows[0][0];
            foreach ($rows as $row) if (AlumnusController::hasNonNull($row))
            {
                //$row[7] contains the faculties
                //$row[8] contains the majors
                //$row[9] contains the further courses
                //$row[11] has the scientific degrees, with dates in 12-17
                //$row[19] has the research fields


                $alumnus = Alumnus::factory()->create([
                    'is_draft' => false,
                    'name' => $row[1],
                    'email' => $row[2],
                    'birth_date' => $row[3],
                    'birth_place' => $row[4],
                    'high_school' => $row[5],
                    'graduation_date' => $row[6],

                    'further_course_detailed' => $row[10],
                    'start_of_membership' => $row[18],
                    'recognations' => $row[19],
                    'research_field_detailed' => $row[21],
                    'links' => $row[22],
                    'works' => $row[23],
                ]);

                foreach (AlumnusController::ids_from_string(';',$row[7],'university_faculties') as $tuple)
                {
                    if ($tuple[0]) {
                        $id = $tuple[1];
                    } else {
                        $id = UniversityFaculty::create([
                            'name' => $tuple[1],
                        ])->id;
                    }

                    DB::table('alumnus_university_faculty')->insert([
                        'alumnus_id' => $alumnus->id,
                        'university_faculty_id' => $id,
                    ]);
                }
                foreach (AlumnusController::ids_from_string(';',$row[8],'majors') as $tuple)
                {
                    if ($tuple[0]) {
                        $id = $tuple[1];
                    } else {
                        $id = Major::create([
                            'name' => $tuple[1],
                        ])->id;
                    }

                    DB::table('alumnus_major')->insert([
                        'alumnus_id' => $alumnus->id,
                        'major_id' => $id,
                    ]);
                }
                foreach (AlumnusController::ids_from_string(';',$row[9],'further_courses') as $tuple)
                {
                    if ($tuple[0]) {
                        $id = $tuple[1];
                    } else {
                        $id = FurtherCourse::create([
                            'name' => $tuple[1],
                        ])->id;
                    }

                    DB::table('alumnus_further_course')->insert([
                        'alumnus_id' => $alumnus->id,
                        'further_course_id' => $id,
                    ]);
                }
                foreach (AlumnusController::ids_from_string(';',$row[20],'research_fields') as $tuple)
                {
                    if ($tuple[0]) {
                        $id = $tuple[1];
                    } else {
                        $id = ResearchField::create([
                            'name' => $tuple[1],
                        ])->id;
                    }

                    DB::table('alumnus_research_field')->insert([
                        'alumnus_id' => $alumnus->id,
                        'research_field_id' => $id,
                    ]);
                }

                // We have to add a degree to the list of degrees if the corresponding year is given.
                // (Often, the year is filled in but the degree is not listed.)
                $degree_names = $row[11];
                $index_to_degree_name = array_flip(AlumnusController::DEGREE_NAME_TO_INDEX);
                for ($i=12; $i<=17; ++$i) {
                    if (!is_null($row[$i])) {
                        $degree_name = $index_to_degree_name[$i];
                        if (!str_contains($degree_names, $degree_name)) {
                            $degree_names .= ";" . $degree_name;
                        }
                    }
                }
                foreach (AlumnusController::ids_from_string(';',$degree_names,'scientific_degrees') as $tuple)
                {
                    if ($tuple[0]) {
                        $degree = ScientificDegree::find($tuple[1]);
                    } else {
                        $degree = ScientificDegree::create([
                            'name' => $tuple[1],
                        ]);
                    }
                    if (array_key_exists($degree->name, AlumnusController::DEGREE_NAME_TO_INDEX)) {
                        $year = $row[ AlumnusController::DEGREE_NAME_TO_INDEX[$degree->name] ];
                        //check whether this is null?
                        DB::table('alumnus_scientific_degree')->insert([
                            'alumnus_id' => $alumnus->id,
                            'scientific_degree_id' => $degree->id,
                            'year' => $year,
                        ]);
                    } else { //for an unknown
                        DB::table('alumnus_scientific_degree')->insert([
                            'alumnus_id' => $alumnus->id,
                            'scientific_degree_id' => $degree->id,
                        ]);
                    }
                }
            }

            --$len;
            //for some reason this does not work
            return redirect()->route('alumni.index')
                ->with('message', "$firstone és $len másik alumnus hozzáadva");
        } else
        {
            return redirect()->back()
                ->with('message', 'A feltöltött fájl nem tartalmaz alumnusokat.');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Alumnus  $alumnus
     * @return \Illuminate\Http\Response
     */
    public function show(Alumnus $alumnus)
    {
        if ($alumnus->is_draft) {
            $this->authorize('viewDraft', Alumnus::class);
        }
        return view('alumni.show', [
            'alumnus' => $alumnus,
        ]);

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Alumnus  $alumnus
     * @return \Illuminate\Http\Response
     */
    public function edit(Alumnus $alumnus)
    {
        $user = Auth::user();
        if (!$user || $user->can('update', $alumnus) || $user->can('createDraftFor', $alumnus)) { //now this is true for everyone
            return view('alumni.create_or_edit', [
                'university_faculties' => UniversityFaculty::$university_faculties_enum,
                'majors' => Major::majorsEnum(),
                'further_courses' => FurtherCourse::$further_courses_enum,
                'scientific_degrees' => ScientificDegree::$scientific_degrees_enum,
                'research_fields' => ResearchField::$research_fields_enum,
                'alumnus' => $alumnus,
            ]);
        } else abort(403);
    }

    /**
     * Synchronises the alumnus' connections with other tables.
     */
    private static function synchroniseConnections(Alumnus $alumnus, array $validated): void {
        if (isset($validated["university_faculties"])) {
            $ids = UniversityFaculty::all()->whereIn('name', $validated['university_faculties'])->pluck('id')->toArray();
            $alumnus->university_faculties()->sync($ids);
        }

        if (isset($validated["majors"])) {
            $ids = Major::all()->whereIn('name', $validated['majors'])->pluck('id')->toArray();
            $alumnus->majors()->sync($ids);
        }

        if (isset($validated["further_courses"])) {
            $ids = FurtherCourse::all()->whereIn('name', $validated['further_courses'])->pluck('id')->toArray();
            $alumnus->further_courses()->sync($ids);
        }

        if (isset($validated["research_fields"])) {
            $ids = ResearchField::all()->whereIn('name', $validated['research_fields'])->pluck('id')->toArray();
            $alumnus->research_fields()->sync($ids);
        }

        // Scientific degree
        $alumnus->scientific_degrees()->detach(); //first detaching everything
        if (isset($validated["scientific_degrees"])) {
            foreach ($validated["scientific_degrees"] as $degree_name) {
                // here we assume we only choose from the pre-defined degrees (hence firstOrFail)
                $degree = ScientificDegree::where('name', $degree_name)->firstOrFail();
                $alumnus->scientific_degrees()->attach([$degree->id => ['year' =>
                    isset($validated['doctor_year']) && strcmp($degree_name, 'egyetemi doktor') == 0 ? $validated['doctor_year'] :
                    (isset($validated['candidate_year']) && strcmp($degree_name, 'kandidátus') == 0 ? $validated['candidate_year'] :
                    (isset($validated['mta_year']) && strcmp($degree_name, 'tudományok doktora/MTA doktora') == 0 ? $validated['mta_year'] :
                    (isset($validated['hab_year']) && strcmp($degree_name, 'habilitáció') == 0 ? $validated['hab_year'] :
                    (isset($validated['phd_year']) && strcmp($degree_name, 'PhD') == 0 ? $validated['phd_year'] :
                    (isset($validated['dla_year']) && strcmp($degree_name, 'DLA') == 0 ? $validated['dla_year'] : null)))))
                ]]);
            }
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Alumnus  $alumnus
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Alumnus $alumnus)
    {
        // TODO: scientific degree and years somehow and in the seader create every field!!

        $user = Auth::user();
        // it will be a draft if:
        //   they are guests, or
        //   they are registered and cannot create non-draft entries but can create drafts
        if((!$user) || !$user->can('update', $alumnus)) {

            $this->authorize('createDraftFor', $alumnus); //this also ensures $alumnus is not a draft

            $draftAlumnus = AlumnusController::validateAndStore($request, true, $alumnus->id);
            $alumnus->pair_id = $draftAlumnus->id;
            $alumnus->save();

            Session::flash('draft_changes_saved');
            return Redirect::route('alumni.show', $alumnus);
        } else {
            //they are no guests and they can edit the non-draft directly
            //this also ensures $alumnus is not a draft

            $validated = AlumnusController::validateRequest($request);
            $alumnus->update([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'birth_date' => $validated['birth_date'],
                'birth_place' => $validated['birth_place'],
                'high_school' => $validated['high_school'],
                'graduation_date' => $validated['graduation_date'],
                'further_course_detailed' => $validated['further_course_detailed'],
                'start_of_membership' => $validated['start_of_membership'],
                'recognations' => $validated['recognations'],
                'research_field_detailed' => $validated['research_field_detailed'],
                'links' => $validated['links'],
                'works' => $validated['works'],
            ]);
            AlumnusController::synchroniseConnections($alumnus, $validated);

            Session::flash('alumnus_updated', $alumnus->name);
            return Redirect::route('alumni.show', $alumnus);
        }
    }

    /**
     * Accept a draft created from outside.
     * Changes the id to the original's id, then deletes the original.
     * If there is no original, it simply changes the is_draft bit to false.
     *
     * @param  \App\Models\Alumnus  $alumnus
     * @return \Illuminate\Http\Response
     */
    public function accept(Alumnus $alumnus)
    {
        $this->authorize('accept', $alumnus); //this also guarantees that $alumnus really is a draft

        if ($alumnus->pair_id) {
            //the order is important!
            $originalPairId = $alumnus->pair_id;
            $originalPair = Alumnus::find($originalPairId);
            $alumnus->pair_id = null;
            $alumnus->is_draft = false;
            $alumnus->save();

            $originalPair->delete();

            $alumnus->id = $originalPairId; //because of onUpdate('cascade'), this will update the connection tables, too
            $alumnus->save();
        } else { //if it is null
            $alumnus->is_draft = false;
            $alumnus->save();
        }

        Session::flash('alumnus_accepted', $alumnus->name);
        return redirect()->route('alumni.show', $alumnus);
    }

    /**
     * Reject a draft created from outside. Simply deletes the draft.
     *
     * @param  \App\Models\Alumnus  $alumnus
     * @return \Illuminate\Http\Response
     */
    public function reject(Alumnus $alumnus)
    {
        $this->authorize('reject', $alumnus); //this also guarantees that $alumnus really is a draft

        $pairId = $alumnus->pair_id;

        Session::flash('alumnus_rejected', $alumnus->name);

        if ($pairId) { //if there is a non-draft pair
            $pairAlumnus = Alumnus::find($pairId);
            $pairAlumnus->pair_id = null;
            $pairAlumnus->save();
            $alumnus->delete();
            return redirect()->route('alumni.show', $pairAlumnus)->with('message', "Módosítások elutasítva és törölve");
        } else {
            $alumnus->delete();
            return redirect()->route('alumni.index')->with('message', "Módosítások elutasítva és törölve");
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Alumnus  $alumnus
     * @return \Illuminate\Http\Response
     */
    public function destroy(Alumnus $alumnus)
    {
        if(!Auth::user() || !Auth::user()->is_admin) abort(403);
        // TODO: authorize
        $alumnus->delete();
        Session::flash('alumnus_deleted', $alumnus->name);
        return Redirect::route('alumni.index');

    }
}

