<?php

namespace LaravelTickets\Tests;

use LaravelTickets\Models\TicketCategory;

class CategoryControllerTest extends TestCase
{
    /** @test */
    public function test_can_add_new_category()
    {
        $category = factory(TicketCategory::class)->make()->toArray();

        $this->post('/tickets/categories', $category)
            ->assertStatus(200)
            ->assertJson(['status' => 'success']);

        $this->assertDatabaseCount('ticket_categories', 1);
    }

    /** @test */
    public function test_can_get_categories()
    {
        $category = factory(TicketCategory::class)->create();

        $this->get('/tickets/categories')
            ->assertStatus(200)
            ->assertJson(['status' => 'success', 'data' => [$category->toArray()]]);
    }

    /** @test */
    public function test_can_update_a_category()
    {
        $category = factory(TicketCategory::class)->create();
        $updatedCategory = factory(TicketCategory::class)->make();

        $this->patch('/tickets/categories/' . $category->id, $updatedCategory->toArray())
            ->assertStatus(200)
            ->assertJson(['status' => 'success']);

        $this->assertDatabaseHas('ticket_categories', [
            'title' => str_replace(' ', '_', strtolower($updatedCategory->title)),
            'desc' => $updatedCategory->desc
        ]);
    }

    /** @test */
    public function test_can_delete_a_category()
    {
        $category = factory(TicketCategory::class)->create();

        $this->delete('/tickets/categories/' . $category->id)
            ->assertStatus(200)
            ->assertJson(['status' => 'success']);

        $this->assertDatabaseMissing('ticket_categories', $category->toArray());
    }
}
