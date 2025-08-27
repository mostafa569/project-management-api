<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Http\Resources\ProjectResource;
use App\Models\Project;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $query = Project::query()->with('tasks');

        // Search
        if ($search = $request->query('search')) {
            $query->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
        }

        // Filters
        if ($status = $request->query('status')) {
            $query->where('status', $status);
        }

        // Sorting
        $sort = $request->query('sort', 'id');
        $order = $request->query('order', 'asc');
        $query->orderBy($sort, $order);

        if ($request->query('withTrashed')) {
            $query->withTrashed();
        }

        $perPage = $request->query('per_page', 10);
        $projects = $query->paginate($perPage);

        return ProjectResource::collection($projects);
    }

    public function store(StoreProjectRequest $request)
    {
        $project = Project::create($request->validated());
        return new ProjectResource($project);
    }

    public function show($id)
    {
        $project = Project::with('tasks')->find($id);

        if (!$project) {
            throw new NotFoundHttpException('Project not found');
        }

        return new ProjectResource($project);
    }

    public function update(UpdateProjectRequest $request, $id)
    {
        $project = Project::find($id);

        if (!$project) {
            throw new NotFoundHttpException('Project not found');
        }

        $project->update($request->validated());
        return new ProjectResource($project);
    }

    public function destroy($id)
    {
        $project = Project::find($id);

        if (!$project) {
            throw new NotFoundHttpException('Project not found');
        }

        $project->delete();
        return response()->json(['message' => 'Project soft deleted'], 200);
    }

    public function restore($id)
    {
        $project = Project::onlyTrashed()->find($id);

        if (!$project) {
            throw new NotFoundHttpException('Trashed project not found');
        }

        $project->restore();
        $project->load('tasks');
        return new ProjectResource($project);
    }

    public function trashed(Request $request)
    {
        $perPage = $request->query('per_page', 10);
        $projects = Project::onlyTrashed()->with('tasks')->paginate($perPage);

        if ($projects->isEmpty()) {
            return response()->json(['message' => 'No trashed projects found'], 200);
        }

        return ProjectResource::collection($projects);
    }
}