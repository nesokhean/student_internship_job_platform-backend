<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StudentProfileRequest;
use App\Http\Resources\StudentProfileResource;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class StudentProfileController extends Controller
{
    public function show(Request $request): JsonResponse
    {
        $user = $request->user();
        $profile = $user->studentProfile;

        if (! $profile) {
            return response()->json([
                'success' => false,
                'message' => 'Profile not found.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => new StudentProfileResource($profile),
        ]);
    }

    public function store(StudentProfileRequest $request): JsonResponse
    {
        $user = $request->user();
        $data = $request->validated();

        $profile = $user->studentProfile()->updateOrCreate(
            ['user_id' => $user->id],
            collect($data)->except(['profile_image', 'cv'])->toArray()
        );

        if ($request->hasFile('profile_image')) {
            if ($profile->profile_image && Storage::disk('public')->exists($profile->profile_image)) {
                Storage::disk('public')->delete($profile->profile_image);
            }
            $filename = Str::uuid() . '.' . $request->file('profile_image')->extension();
            $path = $request->file('profile_image')->storeAs('profile_images', $filename, 'public');
            $profile->update(['profile_image' => $path]);
        }

        if ($request->hasFile('cv')) {
            if ($profile->cv_path && Storage::disk('private')->exists(str_replace('private/', '', $profile->cv_path) === $profile->cv_path ? $profile->cv_path : str_replace('private/', '', $profile->cv_path))) {
                Storage::disk('private')->delete($profile->cv_path);
            }
            $filename = Str::uuid() . '.' . $request->file('cv')->extension();
            $path = $request->file('cv')->storeAs('cvs', $filename, 'private');
            $profile->update(['cv_path' => $path]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Profile saved successfully.',
            'data' => new StudentProfileResource($profile->fresh()),
        ]);
    }

    public function update(StudentProfileRequest $request): JsonResponse
    {
        return $this->store($request);
    }

    public function destroy(Request $request): JsonResponse
    {
        $user = $request->user();
        $profile = $user->studentProfile;

        if ($profile) {
            if ($profile->profile_image && Storage::disk('public')->exists($profile->profile_image)) {
                Storage::disk('public')->delete($profile->profile_image);
            }
            if ($profile->cv_path && Storage::disk('private')->exists($profile->cv_path)) {
                Storage::disk('private')->delete($profile->cv_path);
            }
            $profile->delete();
        }

        return response()->json([
            'success' => true,
            'message' => 'Profile deleted successfully.',
        ]);
    }

    public function uploadCv(Request $request): JsonResponse
    {
        $request->validate([
            'cv' => ['required', 'mimes:pdf,doc,docx', 'max:5120'],
        ]);

        $user = $request->user();
        $profile = $user->studentProfile()->firstOrCreate(['user_id' => $user->id]);

        if ($profile->cv_path && Storage::disk('private')->exists($profile->cv_path)) {
            Storage::disk('private')->delete($profile->cv_path);
        }

        $filename = Str::uuid() . '.' . $request->file('cv')->extension();
        $path = $request->file('cv')->storeAs('cvs', $filename, 'private');
        $profile->update(['cv_path' => $path]);

        return response()->json([
            'success' => true,
            'message' => 'CV uploaded successfully.',
            'data' => ['cv_path' => $path],
        ]);
    }

    public function downloadCv(Request $request): \Symfony\Component\HttpFoundation\BinaryFileResponse|JsonResponse
    {
        $user = $request->user();
        $profile = $user->studentProfile;

        if (! $profile || ! $profile->cv_path || ! Storage::disk('private')->exists($profile->cv_path)) {
            return response()->json([
                'success' => false,
                'message' => 'CV not found.',
            ], 404);
        }

        return Storage::disk('private')->download($profile->cv_path);
    }

    public function deleteCv(Request $request): JsonResponse
    {
        $user = $request->user();
        $profile = $user->studentProfile;

        if ($profile && $profile->cv_path && Storage::disk('private')->exists($profile->cv_path)) {
            Storage::disk('private')->delete($profile->cv_path);
            $profile->update(['cv_path' => null]);
        }

        return response()->json([
            'success' => true,
            'message' => 'CV deleted successfully.',
        ]);
    }
}
