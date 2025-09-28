<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up()
    {
        $tables = [
            'further_courses',
            'majors',
            'research_fields',
            'scientific_degrees',
            'university_faculties',
        ];
        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->boolean('show_in_enums')->default(false);
            });
            // Set show_in_enums=true for existing rows
            DB::table($table)->update(['show_in_enums' => true]);
        }
    }

    public function down()
    {
        $tables = [
            'further_courses',
            'majors',
            'research_fields',
            'scientific_degrees',
            'university_faculties',
        ];
        foreach ($tables as $table) {
            Schema::table($table, function (Blueprint $table) {
                $table->dropColumn('show_in_enums');
            });
        }
    }
};
