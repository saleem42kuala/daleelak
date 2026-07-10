<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\CityResource;
use App\Models\City;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CityController extends Controller
{
    public function index(Request $request): AnonymousResourceCollection
    {
        $cities = City::query()
            ->when($request->integer('country_id'), fn ($q, $id) => $q->where('country_id', $id))
            ->orderBy('name_ar')
            ->get();

        return CityResource::collection($cities);
    }
}
