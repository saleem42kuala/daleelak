<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\CriteriaResource;
use App\Models\Criteria;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CriteriaController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        return CriteriaResource::collection(Criteria::orderBy('sort_order')->get());
    }
}
