<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class TaskReports extends Model
{
    public function taskReportFiles(): BelongsToMany
    {
        return $this->belongsToMany(
            Files::class,
            'task_report_files',
            'task_report_id',
            'file_id'
        );
    }
}
