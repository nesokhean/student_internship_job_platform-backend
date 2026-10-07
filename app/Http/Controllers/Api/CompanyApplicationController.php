<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateApplicationStatusRequest;
use App\Http\Resources\ApplicationResource;
use App\Models\Application;
use App\Models\JobPosting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class CompanyApplicationController extends Controller
{
    public function index(Request $request, $jobId): JsonResponse
    {
        $job = JobPosting::findOrFail($jobId);
        Gate::authorize('viewAny', [Application::class, $job]);

        $applications = $job->applications()
            ->with('studentProfile.user')
            ->paginate(10);

        return response()->json([
            'success' => true,
            'data' => ApplicationResource::collection($applications),
        ]);
    }

    public function show(Request $request, $id): JsonResponse
    {
        $application = Application::with('studentProfile.user', 'jobPosting')->findOrFail($id);
        Gate::authorize('view', $application);

        return response()->json([
            'success' => true,
            'data' => new ApplicationResource($application),
        ]);
    }

    public function updateStatus(UpdateApplicationStatusRequest $request, $id): JsonResponse
    {
        $application = Application::with('jobPosting')->findOrFail($id);
        Gate::authorize('update', $application);

        $newStatus = $request->input('status');
        $allowed = [
            'pending' => ['reviewing', 'accepted', 'rejected'],
            'reviewing' => ['accepted', 'rejected'],
            'accepted' => [],
            'rejected' => [],
        ];

        if (! in_array($newStatus, $allowed[$application->status] ?? [])) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid status transition.',
            ], 422);
        }

        $application->update(['status' => $newStatus]);

        return response()->json([
            'success' => true,
            'message' => 'Application status updated successfully.',
            'data' => new ApplicationResource($application->fresh()->load('studentProfile.user')),
        ]);
    }
}
