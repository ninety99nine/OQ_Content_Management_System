<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSubscriberMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subscriber_messages', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('message_id')->nullable();
            $table->unsignedInteger('subscriber_id')->nullable();
            $table->unsignedTinyInteger('sent_sms_count')->nullable();
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
        Schema::dropIfExists('subscriber_messages');
    }
}
