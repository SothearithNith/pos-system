<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\SizeRequest;
use App\Http\Resources\Api\V1\SizeResource;
use App\Models\Color;
use App\Models\Size;
use Illuminate\Http\Request;

class SizeController extends Controller
{
    public function index(Request $request)
    {
        $colors = Size::query()
            ->when($request->has('is_active'), function ($query) use ($request) {
                $query->where('is_active', $request->boolean('is_active'));
            })
            ->orderBy('id', 'desc')
            ->paginate($request->input('per_page', 10));

        return SizeResource::collection($colors)
            ->additional([
                'message' => 'Size retrieved successfully.'
            ]);
    }

    public function store(SizeRequest $request)
    {
        $size = Size::create($request->validated());

        return (new SizeResource($size))->additional(["message" => "Size created successfully."]);
    }

    public function show(Size $size)
    {
        return (new SizeResource($size))
            ->additional([
                'message' => 'Color retrieved successfully with ID ' . $size->id . '.'
            ]);
    }

    public function update(SizeRequest $request, Size $size)
    {
        $size->update($request->validated());

        return (new SizeResource($size))->additional(["message" => "Size updated successfully."]);
    }

    public function destroy(Size $size)
    {
        $size->delete();
        return response()->json([
            'message' => 'Size deleted successfully.'
        ]);
    }

    public function toggleStatus(Size $size)
    {
        $size->update([
            'is_active' => !$size->is_active
        ]);

        return (new SizeResource($size->refresh()))
            ->additional([
                'message' => 'Size status updated successfully.'
            ]);
    }
}
