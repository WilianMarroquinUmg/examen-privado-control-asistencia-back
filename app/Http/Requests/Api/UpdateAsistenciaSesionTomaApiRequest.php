<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\AsistenciaSesionToma;

class UpdateAsistenciaSesionTomaApiRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return AsistenciaSesionToma::$rules;
    }
}

