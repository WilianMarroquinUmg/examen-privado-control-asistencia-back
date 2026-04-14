<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\AsistenciaSesion;

class UpdateAsistenciaSesionApiRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return AsistenciaSesion::$rules;
    }
}

