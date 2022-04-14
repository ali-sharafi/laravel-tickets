<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use LaravelTickets\Contract\TicketInterface;

class CreateTicketMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('tickets.ticket-messages-table'), function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ticket_id');
            $table->unsignedBigInteger('user_id');
            $table->tinyInteger('user_type')->default(TicketInterface::USER);
            $table->text('message');
            $table->timestamps();

            if (!config('tickets.models.uuid')) {
                $table->foreign('user_id')
                    ->on(config('tickets.users-table'))->references('id');
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
        Schema::dropIfExists(config('tickets.ticket-messages-table'));
    }
}
