<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\JobPostingRequest;
use App\Http\Resources\JobPostingResource;
use App\Models\JobPosting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class CompanyJobController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $company = $user->companyProfile;

        if (! $company) {
            return response()->json([
                'success' => false,
                'message' => 'Company profile not found.',
            ], 404);
        }

        $jobs = $company->jobPostings()->paginate(10);

        return response()->json([
            'success' => true,
            'data' => JobPostingResource::collection($jobs),
        ]);
    }

    public function show(Request $request, $id): JsonResponse
    {
        $job = JobPosting::findOrFail($id);
        Gate::authorize('update', $job);

        return response()->json([
            'success' => true,
            'data' => new JobPostingResource($job),
        ]);
    }

    public function store(JobPostingRequest $request): JsonResponse
    {
        $user = $request->user();
        $company = $user->companyProfile;

        if (! $company) {
            return response()->json([
                'success' => false,
                'message' => 'Company profile not found.',
            ], 404);
        }

        $data = $request->validated();
        $data['company_id'] = $company->id;
        $data['status'] = $data['status'] ?? 'draft';

        $job = JobPosting::create($data);

        return response()->json([
            'success' => true,
            'message' => 'Job posting created successfully.',
            'data' => new JobPostingResource($job),
        ], 201);
    }

    public function update(JobPostingRequest $request, $id): JsonResponse
    {
        $job = JobPosting::findOrFail($id);
        Gate::authorize('update', $job);

        $job->update($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Job posting updated successfully.',
            'data' => new JobPostingResource($job->fresh()),
        ]);
    }

    public function destroy($id): JsonResponse
    {
        $job = JobPosting::findOrFail($id);
        Gate::authorize('delete', $job);

        $job->delete();

        return response()->json([
            'success' => true,
            'message' => 'Job posting deleted successfully.',
        ]);
    }

    public function publish($id): JsonResponse
    {
        $job = JobPosting::findOrFail($id);
        Gate::authorize('update', $job);

        $job->update(['status' => 'published']);

        return response()->json([
            'success' => true,
            'message' => 'Job posting published successfully.',
            'data' => new JobPostingResource($job->fresh()),
        ]);
    }

    public function close($id): JsonResponse
    {
        $job = JobPosting::findOrFail($id);
        Gate::authorize('update', $job);

        $job->update(['status' => 'closed']);

        return response()->json([
            'success' => true,
            'message' => 'Job posting closed successfully.',
            'data' => new JobPostingResource($job->fresh()),
        ]);
    }
}
