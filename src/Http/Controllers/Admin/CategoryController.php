<?php

namespace LaravelTickets\Http\Controllers\Admin;

use App\Models\Language;
use LaravelTickets\Models\TicketCategory;
use Illuminate\Http\Request;
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
        $languages = Language::all();
        $items = $items->map(function ($i) use ($languages) {
            $item = [
                'id' => $i->id,
            ];
            foreach ($languages as $language) {
                $item['title_' . $language->locale] = __trans('ticket_departments', $i->translation . '_title', $language->locale, ' ');
                $item['desc_' . $language->locale] = __trans('ticket_departments', $i->translation . '_desc', $language->locale, ' ');
            }
            return $item;
        });
        return $this->responseSuccess(['items' => $items,]);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $fields = [
            'title_en' => 'required|string|max:250',
            'desc_en' => 'required|string|max:250',
        ];
        $validator = Validator::make($request->only(array_keys($fields)), $fields);
        if ($validator->fails()) {
            return $this->responseError($validator->errors()->first());
        }

        if ($request->get('id')) {
            $item = TicketCategory::find($request->get('id'));
        } else {
            $item = new TicketCategory();
            $item->translation = str_replace(' ', '_', strtolower($request->get('title_en')));
        }
        $item->save();
        foreach (Language::all() as $language) {
            translateService()->updateTranslation('ticket_departments', [['item' => $item->translation . '_title', 'text' => $request->get('title_' . $language->locale)]], $language->locale);
            translateService()->updateTranslation('ticket_departments', [['item' => $item->translation . '_desc', 'text' => $request->get('desc_' . $language->locale)]], $language->locale);
        }
        return $this->responseSuccess();
    }
}
