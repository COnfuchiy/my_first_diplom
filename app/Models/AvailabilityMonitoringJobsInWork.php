<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AvailabilityMonitoringJobsInWork extends Model
{
    use HasFactory;

    protected $fillable = [
        'monitoring_frequently',
        'in_work',
        'num_sites_in_work'
    ];
}
