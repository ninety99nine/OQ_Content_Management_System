<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCampaignsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('campaigns', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('description', 500)->nullable();

            $table->boolean('has_start_date')->default(false);
            $table->timestamp('start_date')->nullable();
            $table->char('start_time', 5)->nullable();

            $table->boolean('has_end_date')->default(false);
            $table->timestamp('end_date')->nullable();
            $table->char('end_time', 5)->nullable();

            $table->unsignedInteger('duration');
            $table->string('frequency');

            $table->json('days_of_the_week')->nullable();

            $table->unsignedInteger('project_id');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('campaigns');
    }
}
