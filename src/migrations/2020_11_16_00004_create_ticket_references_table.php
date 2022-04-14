<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTicketReferencesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('tickets.ticket-references-table'), function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ticket_id');
            $table->morphs('referenceable');
            $table->timestamps();

            if (! config('tickets.models.uuid')) {
                $table->foreign('ticket_id')
                    ->on(config('tickets.tickets-table'))->references('id');
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
        Schema::dropIfExists(config('tickets.ticket-references-table'));
    }
}
