<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ApplicationResource;
use App\Models\Application;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class StudentApplicationController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $student = $user->studentProfile;

        if (! $student) {
            return response()->json([
                'success' => false,
                'message' => 'Student profile not found.',
            ], 404);
        }

        $applications = $student->applications()
            ->with('jobPosting.companyProfile')
            ->paginate(10);

        return response()->json([
            'success' => true,
            'data' => ApplicationResource::collection($applications),
        ]);
    }

    public function show(Request $request, $id): JsonResponse
    {
        $application = Application::with('jobPosting.companyProfile')->findOrFail($id);
        Gate::authorize('view', $application);

        return response()->json([
            'success' => true,
            'data' => new ApplicationResource($application),
        ]);
    }

    public function destroy($id): JsonResponse
    {
        $application = Application::findOrFail($id);
        Gate::authorize('delete', $application);

        if ($application->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Only pending applications can be cancelled.',
            ], 403);
        }

        $application->delete();

        return response()->json([
            'success' => true,
            'message' => 'Application cancelled successfully.',
        ]);
    }
}
