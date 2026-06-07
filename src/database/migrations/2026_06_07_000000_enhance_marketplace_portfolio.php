<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class EnhanceMarketplacePortfolio extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('comments', 'is_recommended')) {
            Schema::table('comments', function (Blueprint $table) {
                $table->boolean('is_recommended')->default(true)->after('message');
            });
        }

        if (!Schema::hasTable('content_images')) {
            Schema::create('content_images', function (Blueprint $table) {
                $table->id();
                $table->foreignId('content_id')->constrained()->cascadeOnDelete();
                $table->string('path');
                $table->unsignedTinyInteger('sort_order')->default(1);
                $table->timestamps();
            });
        }
    }

    public function down()
    {
        Schema::dropIfExists('content_images');

        if (Schema::hasColumn('comments', 'is_recommended')) {
            Schema::table('comments', function (Blueprint $table) {
                $table->dropColumn('is_recommended');
            });
        }
    }
}
