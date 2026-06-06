<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateUsersForDigitalAssetPort extends Migration
{
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('handle')->nullable()->unique()->after('id');
            $table->string('nickname')->nullable()->after('name');
            $table->string('avatar_path')->nullable()->after('email_verified_at');
            $table->text('bio')->nullable()->after('avatar_path');
            $table->boolean('notifications_enabled')->default(true)->after('bio');
            $table->boolean('show_following_count')->default(true)->after('notifications_enabled');
            $table->boolean('show_follower_count')->default(true)->after('show_following_count');
        });
    }

    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropUnique(['handle']);
            $table->dropColumn([
                'handle',
                'nickname',
                'avatar_path',
                'bio',
                'notifications_enabled',
                'show_following_count',
                'show_follower_count',
            ]);
        });
    }
}
