<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTicketCommentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create(config('laravel-tickets.ticket-comments-table'), function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ticket_id');
            $table->unsignedBigInteger('user_id');
            $table->text('message');
            $table->timestamps();

            if (!config('laravel-tickets.models.uuid')) {
                $table->foreign('user_id')
                    ->on(config('laravel-tickets.admins-table'))->references('id');
                $table->foreign('ticket_id')
                    ->on(config('laravel-tickets.tickets-table'))->references('id');
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
        Schema::dropIfExists(config('laravel-tickets.ticket-comments-table'));
    }
}
