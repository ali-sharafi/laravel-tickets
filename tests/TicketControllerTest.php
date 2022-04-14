<?php

namespace LaravelTickets\Tests;

use Illuminate\Foundation\Auth\User as BaseUser;
use Illuminate\Support\Facades\Config;
use LaravelTickets\Contract\TicketInterface;
use LaravelTickets\Models\Ticket;
use LaravelTickets\Models\TicketMessage;
use LaravelTickets\Traits\HasTickets;

class TicketControllerTest extends TestCase
{
    /** @test */
    public function test_can_not_close_a_ticket_twice()
    {
        $user = $this->login();
        $ticket = factory(Ticket::class)->create(['user_id' => $user->id, 'state' => TicketInterface::STATE_CLOSED]);

        $this->post(route(
            'tickets.close',
            ['ticket' => $ticket->id]
        ))
            ->assertRedirect()
            ->assertSessionHas(['message' => trans('tickets.ticket_already_closed')]);
    }

    /** @test */
    public function test_can_not_close_ticket_of_others()
    {
        $this->login();
        $ticket = factory(Ticket::class)->create(['user_id' => 2]);

        $this->post(route(
            'tickets.close',
            ['ticket' => $ticket->id]
        ))->assertStatus(403);
    }

    /** @test */
    public function test_can_close_a_ticket()
    {
        $user = $this->login();
        $ticket = factory(Ticket::class)->create(['user_id' => $user->id]);

        $this->post(route(
            'tickets.close',
            ['ticket' => $ticket->id]
        ))
            ->assertRedirect()
            ->assertSessionHas(['message' => trans('tickets.ticket_closed_successfully')]);
    }

    /** @test */
    public function test_can_not_add_message_on_closed_ticket()
    {
        $user = $this->login();
        $ticket = factory(Ticket::class)->create(['user_id' => $user->id, 'state' => TicketInterface::STATE_CLOSED]);

        $this->post(route(
            'tickets.message',
            ['ticket' => $ticket->id]
        ), ['message' => 'Test Message'])
            ->assertRedirect()
            ->assertSessionHas(['message' => trans('tickets.can_not_reply_to_closed_ticket')]);
    }

    /** @test */
    public function test_can_not_add_empty_message()
    {
        $user = $this->login();
        $ticket = factory(Ticket::class)->create(['user_id' => $user->id]);

        $this->post(route(
            'tickets.message',
            ['ticket' => $ticket->id]
        ), ['message' => null])
            ->assertSessionHasErrors(['message']);
    }

    /** @test */
    public function test_can_not_add_message_to_others()
    {
        $this->login();
        $ticket = factory(Ticket::class)->create(['user_id' => 2]);

        $this->post(route(
            'tickets.message',
            ['ticket' => $ticket->id]
        ), ['message' => 'Test Message'])
            ->assertStatus(403);
    }

    /** @test */
    public function test_can_add_new_message_to_a_ticket()
    {
        $this->login();
        $ticketMessage = factory(TicketMessage::class)->make();

        $this->post(route(
            'tickets.message',
            ['ticket' => $ticketMessage->ticket_id]
        ), ['message' => $ticketMessage->message])
            ->assertRedirect()
            ->assertSessionDoesntHaveErrors();
    }

    /** @test */
    public function test_can_visit_single_ticket()
    {
        $user = $this->login();
        $ticket = factory(Ticket::class)->create(['user_id' => $user->id]);

        $this->get('/tickets/' . $ticket->id)
            ->assertStatus(200)
            ->assertSee($ticket->message);
    }

    /** @test */
    public function test_can_visit_ticket_list()
    {
        $user = $this->login();
        factory(Ticket::class)->create(['user_id' => $user->id]);

        Config::set('tickets.admin', User::class);

        $this->get('/tickets')
            ->assertStatus(200)
            ->assertViewHas('tickets');
    }

    /** @test */
    public function test_can_visit_create_page()
    {
        $this->login();
        $this->get('/tickets/create')
            ->assertStatus(200)
            ->assertSee(trans('tickets.open_ticket'));
    }

    /** @test */
    public function test_can_not_add_more_than_max()
    {
        $user = $this->login();
        factory(Ticket::class, 3)->create(['user_id' => $user->id]);

        $ticket = factory(Ticket::class)->make(['message' => factory(TicketMessage::class)->make()->message]);

        $this->post('/tickets', $ticket->toArray())
            ->assertRedirect()
            ->assertSessionHas('message', trans('tickets.reach_max_open_tickets'));
    }

    /** @test */
    public function test_can_add_new_ticket()
    {
        $this->login();
        $ticket = factory(Ticket::class)->make(['message' => 'Test Message']);

        $this->post('/tickets', $ticket->toArray())
            ->assertRedirect()
            ->assertSessionHas('message', trans('tickets.ticket_created_successfully'));

        $this->assertDatabaseCount('tickets', 1);
    }

    /** @test */
    public function test_can_not_add_empty_ticket()
    {
        $this->login();

        $this->post('/tickets', [])
            ->assertRedirect()
            ->assertSessionHasErrors();
    }


    public function login()
    {
        $this->loadLaravelMigrations(['--database' => 'testbench']);
        $this->artisan('migrate', ['--database' => 'testbench'])->run();

        $user = User::forceCreate([
            'name' => 'Ali Sharafi',
            'email' => 'ali-sharafi@laravel.com',
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi',
        ]);

        Config::set('tickets.user', User::class);

        $this->actingAs($user);

        return $user;
    }
}

class User extends BaseUser
{
    use HasTickets;

    protected $table = 'users';
}
