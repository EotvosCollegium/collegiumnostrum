@extends('layouts.app')
@section('title', 'Alumni')

@section('content')
<div class="container">
    <div class="row justify-content-between">
        <div class="col-12 col-md-8">
            <h1>Alumni tagok</h1>
        </div>
        <div class="col-12 col-md-4">
            <div class="float-lg-end">
                @can('create', \App\Models\Alumnus::class)
                {{-- TODO: Links, policy --}}
                <a href="{{ route('alumni.create') }}" role="button" class="btn btn-sm btn-success mb-1"><i class="fas fa-plus-circle"></i> Új hozzáadása </a>
                {{-- TODO: Links, policy --}}
                <a href="{{ route('alumni.import.create') }}" role="button" class="btn btn-sm btn-success mb-1"><i class="fas fa-plus-circle"></i> Importálás </a>
                @endcan
            </div>
        </div>
    </div>

    {{-- TODO: Session flashes --}}
    @if (Session::has('alumnus_created'))
        <div class="alert alert-success" role="alert">
            {{ Session::get('alumnus_created') }} hozzáadva!
        </div>
    @endif

    @if (Session::has('alumnus_deleted'))
        <div class="alert alert-success" role="alert">
            {{ Session::get('alumnus_deleted') }} törölve az adatbázisból!
        </div>
    @endif

    <div class="row mt-3">
    <div class="col-12 col-lg-12">
            <div class="row">
                <div class="col-12 mb-3">
                    <div class="card bg-light">
                        <div class="card-header">
                            Keresés
                        </div>
                        <div class="card-body">
                        <form id="search-form" method="GET" action="{{ route('alumni.search') }}" enctype="multipart/form-data">
                            @csrf
                            <input type="text" class="form-control" name="name" placeholder="Név" value="{{ isset($_GET['name']) ? $_GET['name'] : '' }}">
                            <input type="text" class="form-control"
                                @if(\App\Version::isNostrum())
                                    placeholder="Collegiumi tagság kezdete"
                                @endif
                                @if(\App\Version::isHellas())
                                    placeholder="Szak megkezdésének éve"
                                @endif
                            pattern="\d{4}" maxlength="4" name="start_of_membership" value="{{ isset($_GET['start_of_membership']) ? $_GET['start_of_membership'] : '' }}">
                            <div class="panel panel-default">
                                <div class="panel-body">
                                    <div class="form-group">
                                        <select name="major" class="form-control" id="majorDropdown">
                                            <option value="">Szak kiválasztása</option>
                                            @foreach ($majors_enum as $major)
                                                <option value="{{ $major }}" {{isset($_GET['major']) && $_GET['major'] == $major ? 'selected ' : ''}}>{{ $major }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="panel panel-default">
                                <div class="panel-body">
                                    <div class="form-group">
                                        <select name="further_course" class="form-control" id="furtherCourseDropdown">
                                            <option value="">További pálya kiválasztása</option>
                                            @foreach ($further_courses_enum as $further_course)
                                                <option value="{{ $further_course }}" {{isset($_GET['further_course']) && $_GET['further_course'] == $further_course ? 'selected ' : ''}}>{{ $further_course }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="panel panel-default">
                                <div class="panel-body">
                                    <div class="form-group">
                                        <select name="scientific_degree" class="form-control" id="scientificDegreeDropdown">
                                            <option value="">Tudományos fokozat kiválasztása</option>
                                            @foreach ($scientific_degrees_enum as $scientific_degree)
                                                <option value="{{ $scientific_degree }}" {{isset($_GET['scientific_degree']) && $_GET['scientific_degree'] == $scientific_degree ? 'selected ' : ''}}>{{ $scientific_degree }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="panel panel-default">
                                <div class="panel-body">
                                    <div class="form-group">
                                        <select name="research_field" class="form-control" id="researchFieldDropdown">
                                            <option value="">Kutatási terület kiválasztása</option>
                                            @foreach ($research_fields_enum as $research_field)
                                                <option value="{{ $research_field }}" {{isset($_GET['research_field']) && $_GET['research_field'] == $research_field ? 'selected ' : ''}}>{{ $research_field }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div style="margin-top: 10px;">
                                <button type="submit" class="btn btn-primary">Keresés</button>
                                <button onclick="setFormCleanReset('#search-form');" class="btn btn-danger">Visszaállítás</button>
                            </div>
                        </form>

                        </div>
                    </div>
                </div>
            </div>

        </div>
        @cannot('create', \App\Models\Alumnus::class)
        <p class="lead">Ha nem talál valakit, <a href="mailto:root@eotvos.elte.hu">írjon nekünk</a>.</p>
        @endcan
        <div class="col-12 col-lg-12">
            <div class="row">
                {{-- TODO: Read posts from DB --}}

                @forelse ($alumni as $alumnus)
                    <div class="col-12 col-md-6 col-lg-4 mb-3 d-flex align-self-stretch">
                        <div class="card w-100">

                            @if ($alumnus->is_draft)
                            <div class="card-body bg-info">
                            @else
                            <div class="card-body">
                            @endif
                                {{-- TODO: Title --}}
                                <h5 class="card-title mb-0">{{ $alumnus->name }}</h5>
                                @if ($alumnus->is_draft)
                                <p class="card-text text-danger">Jóváhagyásra vár</p>
                                @endif
                                <p class="small mb-0">
                                    @isset($alumnus->start_of_membership)
                                    <span class="start-of-membership">
                                @if(\App\Version::isNostrum())
                                    Collegiumi tagság kezdete:
                                @endif
                                @if(\App\Version::isHellas())
                                    Szak megkezdésének éve:
                                @endif <strong>{{ $alumnus->start_of_membership }}</strong></span>
                                    <br>
                                    @endisset

                                    @if($alumnus->majors()->exists())
                                     @php
                                        $majors_array = Arr::flatten($alumnus->majors()->select('name')->get()->makeHIdden('pivot')->toArray());
                                    @endphp
                                    <span>
                                        <span>Egyetemi szak(ok): {{ implode(", ", $majors_array) }}</span>
                                    </span>
                                    @endif
                                </p>
                            </div>
                            <div class="card-footer">
                                {{-- TODO: Link --}}
                                <a href="{{ route('alumni.show', $alumnus->id) }}" class="btn btn-primary">
                                    <span>Részletek</span> <i class="fas fa-angle-right"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="alert alert-warning" role="alert">
                            Nem található alumni!
                        </div>
                    </div>
                @endforelse
            </div>
            @if(isset($alumni) && !empty($alumni))
                <div class="d-flex justify-content-center">
                    {{ $alumni->links('pagination::bootstrap-4') }}
                </div>
            @endif
        </div>

    </div>
</div>
@endsection

@section('scripts')

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    $(document).ready(function() {
        $('#majorDropdown').on('change', function() {
            var selectedMajor = $(this).val();

            // Perform actions based on the selected major
            if (selectedMajor) {
                // Example: Display selected major in console
                // console.log('Selected Major:', selectedMajor);

                // Example: Make an AJAX request to fetch related data based on the selected major
                $.ajax({
                    url: '/api/majors/' + selectedMajor,
                    method: 'GET',
                    success: function(response) {
                        // Handle the response data
                        console.log('Response:', response);
                    },
                    error: function(xhr, status, error) {
                        // Handle the error
                        console.error('Error:', error);
                    }
                });
            }
        });

        $('#furtherCourseDropdown').on('change', function() {
            var selectedMajor = $(this).val();

            // Perform actions based on the selected major
            if (selectedMajor) {
                // Example: Display selected major in console
                // console.log('Selected Major:', selectedMajor);

                // Example: Make an AJAX request to fetch related data based on the selected major
                $.ajax({
                    url: '/api/further_courses/' + selectedMajor,
                    method: 'GET',
                    success: function(response) {
                        // Handle the response data
                        console.log('Response:', response);
                    },
                    error: function(xhr, status, error) {
                        // Handle the error
                        console.error('Error:', error);
                    }
                });
            }
        });
    });

    var links = document.getElementsByTagName("a");

    const urlParams = new URLSearchParams(window.location.search);
    urlParams.delete("_token");
    urlParams.delete("page");
    for (var i = 0; i < links.length; i++) {
        if(links[i].href.includes("alumni/search?page=")){
            links[i].href += "&" + urlParams.toString();
        }
    }

    // Clears a form's input fields.
    // Based on https://stackoverflow.com/questions/40734855/reset-button-not-working-in-html-php.
    function setFormCleanReset(formId) {
        let formEl = document.querySelector(formId);

        // Iterate all non-hidden fields, set values to ''
        for(const fieldEl of formEl.querySelectorAll('input:not([type=hidden])')) {

            // @todo check input type and handle "select" etc.
            fieldEl.setAttribute('value', '');
        }

        // For 'select' elements, we select the first option (the empty one).
        for(const selectEl of formEl.querySelectorAll('select:not([type=hidden])')) {
            selectEl.selectedIndex = 0;
        }
    }
</script>

@endsection
