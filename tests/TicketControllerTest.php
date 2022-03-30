<?php

namespace LaravelTickets\Tests;

use Illuminate\Foundation\Auth\User as BaseUser;
use Illuminate\Support\Facades\Config;
use LaravelTickets\Models\Ticket;
use LaravelTickets\Traits\HasTickets;

class TicketControllerTest extends TestCase
{
    /** @test */
    public function test_can_add_new_ticket()
    {
        $this->withoutExceptionHandling();
        $this->login();
        $ticket = factory(Ticket::class)->make();

        $this->post('/tickets', $ticket->toArray())
            ->assertRedirect()
            ->assertSessionHas('message', trans('tickets.ticket_created_successfully'));

        $this->assertDatabaseCount('tickets', 1);
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

        Config::set('laravel-tickets.user',User::class);

        $this->actingAs($user);
    }
}

class User extends BaseUser
{
    use HasTickets;

    protected $table = 'users';
}
