<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\CompanyProfileRequest;
use App\Http\Resources\CompanyProfileResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CompanyProfileController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        $user = $request->user();
        $profile = $user->companyProfile;

        if (! $profile) {
            return response()->json([
                'success' => false,
                'message' => 'Profile not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => new CompanyProfileResource($profile),
        ]);
    }

    public function store(CompanyProfileRequest $request): JsonResponse
    {
        $user = $request->user();
        $data = $request->validated();

        $profile = $user->companyProfile()->updateOrCreate(
            ['user_id' => $user->id],
            collect($data)->except(['logo'])->toArray()
        );

        if ($request->hasFile('logo')) {
            if ($profile->logo && Storage::disk('public')->exists($profile->logo)) {
                Storage::disk('public')->delete($profile->logo);
            }
            $filename = Str::uuid() . '.' . $request->file('logo')->extension();
            $path = $request->file('logo')->storeAs('logos', $filename, 'public');
            $profile->update(['logo' => $path]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Profile saved successfully.',
            'data' => new CompanyProfileResource($profile->fresh()),
        ]);
    }

    public function update(CompanyProfileRequest $request): JsonResponse
    {
        return $this->store($request);
    }

    public function destroy(Request $request): JsonResponse
    {
        $user = $request->user();
        $profile = $user->companyProfile;

        if ($profile) {
            if ($profile->logo && Storage::disk('public')->exists($profile->logo)) {
                Storage::disk('public')->delete($profile->logo);
            }
            $profile->delete();
        }

        return response()->json([
            'success' => true,
            'message' => 'Profile deleted successfully.',
        ]);
    }
}
