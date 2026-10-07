<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\JobPostingRequest;
use App\Http\Resources\JobPostingResource;
use App\Models\JobPosting;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class JobPostingController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = JobPosting::with('companyProfile')->where('status', 'published');

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('requirements', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%");
            });
        }

        if ($request->has('job_type')) {
            $query->where('job_type', $request->input('job_type'));
        }

        if ($request->has('location')) {
            $query->where('location', 'like', '%' . $request->input('location') . '%');
        }

        if ($request->has('salary_min')) {
            $query->where('salary_max', '>=', $request->input('salary_min'));
        }

        if ($request->has('salary_max')) {
            $query->where('salary_min', '<=', $request->input('salary_max'));
        }

        if ($request->has('deadline')) {
            $query->whereDate('deadline', '>=', $request->input('deadline'));
        }

        $perPage = $request->input('per_page', 10);
        $jobs = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => JobPostingResource::collection($jobs),
            'pagination' => [
                'current_page' => $jobs->currentPage(),
                'per_page' => $jobs->perPage(),
                'total' => $jobs->total(),
                'last_page' => $jobs->lastPage(),
            ],
        ]);
    }

    public function show($id): JsonResponse
    {
        $job = JobPosting::with('companyProfile')->where('status', 'published')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => new JobPostingResource($job),
        ]);
    }
}
