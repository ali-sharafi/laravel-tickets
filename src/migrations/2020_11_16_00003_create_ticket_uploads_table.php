<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTicketUploadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('tickets.ticket-uploads-table'), function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ticket_message_id');
            $table->string('path');
            $table->timestamps();

            if (! config('tickets.models.uuid')) {
                $table->foreign('ticket_message_id')
                    ->on(config('tickets.ticket-messages-table'))->references('id');
            }
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists(config('tickets.ticket-uploads-table'));
    }
}
