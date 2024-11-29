<?php

namespace App\Observers;

use App\Models\ProjectCoordinator;

class ProjectCoordinatorObserver
{
    /**
     * Handle the ProjectCoordinator "created" event.
     */
    public function created(ProjectCoordinator $projectCoordinator): void
    {
        // berikan role project coordinator pada user dari teacher yang diambil dari project coordinator
        $projectCoordinator->teacher->userable->user->assignRole('project_coordinator');
    }

    /**
     * Handle the ProjectCoordinator "updated" event.
     */
    public function updated(ProjectCoordinator $projectCoordinator): void
    {
        // periksa apakah teacher id berubah, 
        // jika ada teacher baru maka berikan role project coordinator pada user dari teacher yang baru 
        // dan hapus role dari teacher yang lama
        if ($projectCoordinator->isDirty('teacher_id')) {
            $projectCoordinator->teacher->userable->user->removeRole('project_coordinator');
            $projectCoordinator->teacher->userable->user->assignRole('project_coordinator');
        }
    }

    /**
     * Handle the ProjectCoordinator "deleted" event.
     */
    public function deleted(ProjectCoordinator $projectCoordinator): void
    {
        // periksa apakah teacher id berubah, 
        // jika ada teacher baru maka berikan role project coordinator pada user dari teacher yang baru 
        // dan hapus role dari teacher yang lama
        if ($projectCoordinator->isDirty('teacher_id')) {
            $projectCoordinator->teacher->userable->user->removeRole('project_coordinator');
            $projectCoordinator->teacher->userable->user->assignRole('project_coordinator');
        }
    }

    /**
     * Handle the ProjectCoordinator "restored" event.
     */
    public function restored(ProjectCoordinator $projectCoordinator): void
    {
        //
    }

    /**
     * Handle the ProjectCoordinator "force deleted" event.
     */
    public function forceDeleted(ProjectCoordinator $projectCoordinator): void
    {
        //
    }
}
