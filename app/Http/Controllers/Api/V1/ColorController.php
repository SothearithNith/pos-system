<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\ColorRequest;
use App\Http\Resources\Api\V1\ColorResource;
use App\Models\Color;
use Illuminate\Http\Request;

class ColorController extends Controller
{
    public function index(Request $request)
    {
        $colors = Color::query()
            ->when($request->has('is_active'), function ($query) use ($request) {
                $query->where('is_active', $request->boolean('is_active'));
            })
            ->latest()
            ->paginate($request->input('per_page', 10));

        return ColorResource::collection($colors)
            ->additional([
                'message' => 'Colors retrieved successfully.'
            ]);
    }

    public function store(ColorRequest $request)
    {
        $color = Color::create($request->validated());

        return (new ColorResource($color))
            ->additional([
                'message' => 'Color created successfully.'
            ]);
    }

    public function show(Color $color)
    {
        return (new ColorResource($color))
            ->additional([
                'message' => 'Color retrieved successfully.'
            ]);
    }

    public function update(ColorRequest $request, Color $color)
    {
        $color->update($request->validated());

        return (new ColorResource($color))
            ->additional([
                'message' => 'Color updated successfully.'
            ]);
    }

    public function toggleStatus(Color $color)
    {
        $color->update([
            'is_active' => !$color->is_active
        ]);

        return (new ColorResource($color->refresh()))
            ->additional([
                'message' => 'Color status updated successfully.'
            ]);
    }

    public function destroy(Color $color)
    {
        $color->delete();

        return response()->json([
            'message' => 'Color deleted successfully.'
        ]);
    }
}
