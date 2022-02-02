<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;

class CreateWkDeviceTable extends Migration
{
    public function up()
    {
        Schema::create(config('wk-core.table.device.devices'), function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->nullableMorphs('host');
            $table->string('serial')->nullable();
            $table->string('identifier');
            $table->unsignedBigInteger('order')->nullable();
            $table->boolean('is_enabled')->default(0);

            $table->string('type')->nullable();
            $table->string('ver_os')->nullable();
            $table->string('ver_driver')->nullable();
            $table->string('ver_agent')->nullable();
            $table->string('ver_app')->nullable();

            $table->timestampsTz();
            $table->softDeletes();

            $table->index('serial');
            $table->index('identifier');
            $table->index('is_enabled');
            $table->index('type');
            $table->index('ver_agent');
            $table->index('ver_app');
        });
        if (!config('wk-device.onoff.core-lang_core')) {
            Schema::create(config('wk-core.table.device.devices_lang'), function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->morphs('morph');
                $table->unsignedBigInteger('user_id')->nullable();
                $table->string('code');
                $table->string('key');
                $table->text('value')->nullable();
                $table->boolean('is_current')->default(1);

                $table->timestampsTz();
                $table->softDeletes();

                $table->foreign('user_id')->references('id')
                    ->on(config('wk-core.table.user'))
                    ->onDelete('set null')
                    ->onUpdate('cascade');
            });
        }
    }

    public function down() {
        Schema::dropIfExists(config('wk-core.table.device.devices_lang'));
        Schema::dropIfExists(config('wk-core.table.device.devices'));
    }
}
