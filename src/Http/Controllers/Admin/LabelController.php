<?php

namespace LaravelTickets\Http\Controllers\Admin;

use LaravelTickets\Http\Controllers\BaseController;
use LaravelTickets\Models\TicketLabel;

class LabelController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return $this->responseSuccess(TicketLabel::all());
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        $this->request->validate([
            'name' => 'required',
            'color' => 'required'
        ]);

        TicketLabel::create($this->request->only(['name', 'color']));

        return $this->index();
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Label  $label
     * @return \Illuminate\Http\Response
     */
    public function update(TicketLabel $label)
    {
        $this->request->validate([
            'name' => 'required',
            'color' => 'required'
        ]);

        $label->update($this->request->only(['name', 'color']));

        return $this->index();
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Label  $label
     * @return \Illuminate\Http\Response
     */
    public function destroy(TicketLabel $label)
    {
        $label->delete();

        return $this->index();
    }
}
