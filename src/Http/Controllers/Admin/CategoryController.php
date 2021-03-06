<?php

namespace LaravelTickets\Http\Controllers\Admin;

use LaravelTickets\Models\TicketCategory;
use Illuminate\Support\Facades\Validator;
use LaravelTickets\Http\Controllers\BaseController;

class CategoryController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $items = TicketCategory::all();
        return $this->responseSuccess($items);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store()
    {
        $validator = $this->validateCategoryRequest();
        if ($validator->fails()) {
            return $this->responseError($validator->errors()->first());
        }

        $item = new TicketCategory();
        $item->title = str_replace(' ', '_', strtolower($this->request->get('title')));
        $item->desc = $this->request->get('desc');

        $item->save();

        return $this->responseSuccess();
    }

    /**
     * Update a category
     * @param \LaravelTickets\Models\TicketCategory $category
     * 
     * @return \Illuminate\Http\Response
     */
    public function update(TicketCategory $category)
    {
        $validator = $this->validateCategoryRequest();
        if ($validator->fails()) {
            return $this->responseError($validator->errors()->first());
        }

        $category->title = str_replace(' ', '_', strtolower($this->request->get('title')));
        $category->desc = $this->request->get('desc');

        $category->save();

        return $this->responseSuccess();
    }

    /**
     * Destroy a category
     * @param \LaravelTickets\Models\TicketCategory $category
     * 
     * @return \Illuminate\Http\Response
     */
    public function destroy(TicketCategory $category)
    {
        $category->delete();

        return $this->responseSuccess();
    }

    /**
     * Validate input request
     * 
     * @return \Illuminate\Support\Facades\Validator
     */
    private function validateCategoryRequest()
    {
        $fields = [
            'title' => 'required|string|max:250',
            'desc' => 'required|string|max:250',
        ];

        return Validator::make($this->request->only(array_keys($fields)), $fields);
    }
}
