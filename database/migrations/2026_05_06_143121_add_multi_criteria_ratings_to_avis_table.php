<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('avis', function (Blueprint $table) {
            $table->unsignedTinyInteger('rating_cleanliness')->nullable()->after('rating');
            $table->unsignedTinyInteger('rating_communication')->nullable()->after('rating_cleanliness');
            $table->unsignedTinyInteger('rating_location')->nullable()->after('rating_communication');
            $table->unsignedTinyInteger('rating_value')->nullable()->after('rating_location');
        });

        DB::statement('UPDATE avis SET rating_cleanliness = rating, rating_communication = rating, rating_location = rating, rating_value = rating WHERE rating_cleanliness IS NULL');
    }

    public function down(): void
    {
        Schema::table('avis', function (Blueprint $table) {
            $table->dropColumn(['rating_cleanliness', 'rating_communication', 'rating_location', 'rating_value']);
        });
    }
};
