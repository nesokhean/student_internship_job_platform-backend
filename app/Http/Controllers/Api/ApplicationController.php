<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\ApplicationRequest;
use App\Http\Requests\UpdateApplicationStatusRequest;
use App\Http\Resources\ApplicationResource;
use App\Models\Application;
use App\Models\JobPosting;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ApplicationController extends Controller
{
    public function apply(ApplicationRequest $request, $jobId): JsonResponse
    {
        $user = $request->user();
        $job = JobPosting::findOrFail($jobId);

        if ($job->status !== 'published') {
            return response()->json([
                'success' => false,
                'message' => 'Job is not available for applications.',
            ], 403);
        }

        if ($job->deadline && Carbon::now()->gt($job->deadline)) {
            return response()->json([
                'success' => false,
                'message' => 'Application deadline has passed.',
            ], 403);
        }

        $student = $user->studentProfile;

        if (! $student) {
            return response()->json([
                'success' => false,
                'message' => 'Student profile not found.',
            ], 404);
        }

        $today = Carbon::today();
        $dailyCount = Application::where('student_id', $student->id)
            ->whereDate('created_at', $today)
            ->count();

        if ($dailyCount >= 5) {
            return response()->json([
                'success' => false,
                'message' => 'Daily application limit (5) reached.',
            ], 429);
        }

        $existing = Application::where('job_posting_id', $job->id)
            ->where('student_id', $student->id)
            ->first();

        if ($existing) {
            return response()->json([
                'success' => false,
                'message' => 'You have already applied for this job.',
            ], 422);
        }

        DB::beginTransaction();
        try {
            $data = [
                'job_posting_id' => $job->id,
                'student_id' => $student->id,
                'cover_letter' => $request->input('cover_letter'),
                'status' => 'pending',
                'applied_at' => now(),
            ];

            if ($request->hasFile('cv')) {
                $filename = Str::uuid() . '.' . $request->file('cv')->extension();
                $path = $request->file('cv')->storeAs('cvs', $filename, 'private');
                $data['cv_path'] = $path;
            } elseif ($student->cv_path) {
                $data['cv_path'] = $student->cv_path;
            }

            $application = Application::create($data);
            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Application submitted successfully.',
                'data' => new ApplicationResource($application->load('jobPosting.companyProfile')),
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function dailyLimit(Request $request): JsonResponse
    {
        $user = $request->user();
        $student = $user->studentProfile;

        if (! $student) {
            return response()->json([
                'success' => false,
                'message' => 'Student profile not found.',
            ], 404);
        }

        $today = Carbon::today();
        $used = Application::where('student_id', $student->id)
            ->whereDate('created_at', $today)
            ->count();

        return response()->json([
            'success' => true,
            'data' => [
                'limit' => 5,
                'used' => $used,
                'remaining' => max(0, 5 - $used),
                'date' => $today->toDateString(),
            ],
        ]);
    }
}
