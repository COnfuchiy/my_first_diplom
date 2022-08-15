<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    use HasFactory;

    public $timestamps = false;


    public function site(){
        $this->belongsTo(MonitoringSite::class);
    }

    protected $fillable=[
        'site_id',
        'path',
    ];
}
