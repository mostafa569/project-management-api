<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $query = Task::query()->with(['project', 'users']);

        // Search
        if ($search = $request->query('search')) {
            $query->where('title', 'like', "%{$search}%")
                  ->orWhere('details', 'like', "%{$search}%");
        }

        // Filters
        if ($priority = $request->query('priority')) {
            $query->where('priority', $priority);
        }
        if ($request->has('is_completed')) {
            $query->where('is_completed', $request->query('is_completed'));
        }
        if ($projectId = $request->query('project_id')) {
            $query->where('project_id', $projectId);
        }

        // Sorting
        $sort = $request->query('sort', 'id');
        $order = $request->query('order', 'asc');
        $query->orderBy($sort, $order);

        if ($request->query('withTrashed')) {
            $query->withTrashed();
        }

        $perPage = $request->query('per_page', 10);
        $tasks = $query->paginate($perPage);

        return TaskResource::collection($tasks);
    }

    public function store(StoreTaskRequest $request)
    {
        $task = Task::create($request->validated());
        if ($request->has('assignee_ids')) {
            $task->users()->sync($request->assignee_ids);
        }
        $task->load(['project', 'users']);
        return new TaskResource($task);
    }

    public function show($id)
    {
        $task = Task::with(['project', 'users'])->find($id);

        if (!$task) {
            throw new NotFoundHttpException('Task not found');
        }

        return new TaskResource($task);
    }

    public function update(UpdateTaskRequest $request, $id)
    {
        $task = Task::find($id);

        if (!$task) {
            throw new NotFoundHttpException('Task not found');
        }

        $task->update($request->validated());
        if ($request->has('assignee_ids')) {
            $task->users()->sync($request->assignee_ids);
        }
        $task->load(['project', 'users']);
        return new TaskResource($task);
    }

    public function destroy($id)
    {
        $task = Task::find($id);

        if (!$task) {
            throw new NotFoundHttpException('Task not found');
        }

        $task->delete();
        return response()->json(['message' => 'Task soft deleted'], 200);
    }

    public function restore($id)
    {
        $task = Task::onlyTrashed()->find($id);

        if (!$task) {
            throw new NotFoundHttpException('Trashed task not found');
        }

        $task->restore();
        $task->load(['project', 'users']);
        return new TaskResource($task);
    }

    public function trashed(Request $request)
    {
        $query = Task::onlyTrashed()->with(['project', 'users']);
        $perPage = $request->query('per_page', 10);
        $tasks = $query->paginate($perPage);

        if ($tasks->isEmpty()) {
            return response()->json(['message' => 'No trashed tasks found'], 200);
        }

        return TaskResource::collection($tasks);
    }
}