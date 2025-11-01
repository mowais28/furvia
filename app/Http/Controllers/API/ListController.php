<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\CertificationList;
use App\Models\DegreeList;
use App\Models\ListCertification;
use App\Models\ListDegree;
use App\Models\ListService;
use App\Models\ListSkill;
use App\Models\ServiceList;
use App\Models\SkillList;
use Illuminate\Http\Request;

class ListController extends Controller
{
    public function getSingle($type)
    {
        $allowed = [
            'degrees' => ListDegree::all(),
            'certifications' => ListCertification::all(),
            'skills' => ListSkill::all(),
            'services' => ListService::all(),
        ];

        if (!isset($allowed[$type])) {
            return response()->json(['status' => 'error', 'message' => 'Invalid type.'], 400);
        }

        $model = $allowed[$type];
        return response()->json([
            'status' => 'success',
            'data' => $model,
        ]);
    }
}
