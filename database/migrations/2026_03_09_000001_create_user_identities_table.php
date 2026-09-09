<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('user_identities')) {
            return;
        }

        Schema::create('user_identities', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->comment('关联 users.id');
            $table->string('provider', 32)->comment('登录渠道');
            $table->string('identifier', 191)->comment('渠道内唯一标识');
            $table->string('credential', 255)->nullable()->comment('凭证，如邮箱密码哈希');
            $table->json('metadata')->nullable()->comment('渠道扩展信息');
            $table->dateTime('verified_at')->nullable();
            $table->dateTime('last_login_at')->nullable();
            $table->tinyInteger('status')->default(0)->comment('0正常 1禁用');
            $table->timestamps();

            $table->unique(['provider', 'identifier'], 'uk_provider_identifier');
            $table->index('user_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_identities');
    }
};
