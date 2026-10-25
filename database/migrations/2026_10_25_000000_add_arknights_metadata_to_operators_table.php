<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('operators', function (Blueprint $table) {
            $table->string('char_id')->nullable()->unique()->after('id');
            $table->string('subclass')->nullable()->after('class');
            $table->string('nation')->nullable()->after('position');
            $table->string('display_number', 16)->nullable()->after('nation');
            $table->json('tags')->nullable()->after('display_number');
            $table->text('usage')->nullable()->after('description');
            $table->string('portrait')->nullable()->after('image');
        });
    }

    public function down(): void
    {
        Schema::table('operators', function (Blueprint $table) {
            $table->dropColumn([
                'char_id',
                'subclass',
                'nation',
                'display_number',
                'tags',
                'usage',
                'portrait',
            ]);
        });
    }
};
