<?php

use App\Models\Campaign;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Arr;
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

            $table->enum('schedule_type', Campaign::SCHEDULE_TYPE)->default(Arr::first(Campaign::SCHEDULE_TYPE));
            $table->unsignedInteger('recurring_duration')->nullable();
            $table->string('recurring_frequency')->nullable();

            $table->enum('message_to_send', Campaign::MESSAGE_TO_SEND)->default(Arr::last(Campaign::MESSAGE_TO_SEND));
            $table->json('message_ids');

            $table->boolean('has_start_date')->default(false);
            $table->date('start_date')->nullable();
            $table->char('start_time', 5)->nullable();

            $table->boolean('has_end_date')->default(false);
            $table->date('end_date')->nullable();
            $table->char('end_time', 5)->nullable();

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
