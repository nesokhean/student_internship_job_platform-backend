<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Models\Application;
use App\Models\CompanyProfile;
use App\Models\JobPosting;
use App\Models\StudentProfile;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard(): JsonResponse
    {
        return response()->json([
            'success' => true,
            'data' => [
                'total_users' => User::count(),
                'total_students' => User::where('role', 'student')->count(),
                'total_companies' => User::where('role', 'company')->count(),
                'total_jobs' => JobPosting::count(),
                'published_jobs' => JobPosting::where('status', 'published')->count(),
                'closed_jobs' => JobPosting::where('status', 'closed')->count(),
                'total_applications' => Application::count(),
                'pending_applications' => Application::where('status', 'pending')->count(),
                'reviewing_applications' => Application::where('status', 'reviewing')->count(),
                'accepted_applications' => Application::where('status', 'accepted')->count(),
                'rejected_applications' => Application::where('status', 'rejected')->count(),
            ],
        ]);
    }

    public function users(Request $request): JsonResponse
    {
        $query = User::query();

        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->has('role')) {
            $query->where('role', $request->input('role'));
        }

        if ($request->has('status')) {
            $query->where('status', $request->input('status'));
        }

        $users = $query->paginate(10);

        return response()->json([
            'success' => true,
            'data' => UserResource::collection($users),
        ]);
    }

    public function showUser($id): JsonResponse
    {
        $user = User::findOrFail($id);
        return response()->json([
            'success' => true,
            'data' => new UserResource($user),
        ]);
    }

    public function updateUserStatus(Request $request, $id): JsonResponse
    {
        $user = User::findOrFail($id);

        if ($user->id === $request->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot change your own status.',
            ], 403);
        }

        $request->validate([
            'status' => ['required', 'in:active,blocked'],
        ]);

        if ($request->input('status') === 'blocked') {
            $user->tokens()->delete();
        }

        $user->update(['status' => $request->input('status')]);

        return response()->json([
            'success' => true,
            'message' => 'User status updated successfully.',
            'data' => new UserResource($user->fresh()),
        ]);
    }

    public function deleteUser($id): JsonResponse
    {
        $user = User::findOrFail($id);

        if ($user->id === request()->user()->id) {
            return response()->json([
                'success' => false,
                'message' => 'You cannot delete your own account.',
            ], 403);
        }

        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'User deleted successfully.',
        ]);
    }

    public function students(): JsonResponse
    {
        $students = User::where('role', 'student')->with('studentProfile')->paginate(10);

        return response()->json([
            'success' => true,
            'data' => UserResource::collection($students),
        ]);
    }

    public function companies(): JsonResponse
    {
        $companies = User::where('role', 'company')->with('companyProfile')->paginate(10);

        return response()->json([
            'success' => true,
            'data' => UserResource::collection($companies),
        ]);
    }

    public function jobs(): JsonResponse
    {
        $jobs = JobPosting::with('companyProfile')->paginate(10);

        return response()->json([
            'success' => true,
            'data' => \App\Http\Resources\JobPostingResource::collection($jobs),
        ]);
    }

    public function applications(): JsonResponse
    {
        $applications = Application::with('jobPosting.companyProfile', 'studentProfile.user')->paginate(10);

        return response()->json([
            'success' => true,
            'data' => \App\Http\Resources\ApplicationResource::collection($applications),
        ]);
    }
}
