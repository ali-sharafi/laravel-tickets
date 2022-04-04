<?php

namespace LaravelTickets\Tests;

use LaravelTickets\Models\TicketLabel;

class LabelControllerTest extends TestCase
{
    /** @test */
    public function test_can_add_new_label()
    {
        $label = factory(TicketLabel::class)->make()->toArray();

        $this->post('/api/tickets/labels', $label)
            ->assertStatus(200)
            ->assertJson(['status' => 'success', 'data' => [$label]]);

        $this->assertDatabaseHas('ticket_labels', $label);
    }

    /** @test */
    public function test_can_get_labels()
    {
        $label = factory(TicketLabel::class)->create()->toArray();

        $this->get('/api/tickets/labels')
            ->assertStatus(200)
            ->assertJson(['status' => 'success', 'data' => [$label]]);
    }

    /** @test */
    public function test_can_update_a_label()
    {
        $label = factory(TicketLabel::class)->create();
        $updatedLabel = factory(TicketLabel::class)->make()->toArray();

        $this->patch('/api/tickets/labels/' . $label->id, $updatedLabel)
            ->assertStatus(200)
            ->assertJson(['status' => 'success', 'data' => [$updatedLabel]]);
    }

    /** @test */
    public function test_can_delete_a_label()
    {
        $label = factory(TicketLabel::class)->create();

        $this->delete('/api/tickets/labels/' . $label->id)
            ->assertStatus(200)
            ->assertJson(['status' => 'success']);

        $this->assertDatabaseCount('ticket_labels', 0);
    }
}
