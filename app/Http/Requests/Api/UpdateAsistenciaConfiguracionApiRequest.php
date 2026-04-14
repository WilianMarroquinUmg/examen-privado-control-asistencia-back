<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\AsistenciaConfiguracion;

class UpdateAsistenciaConfiguracionApiRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return AsistenciaConfiguracion::$rules;
    }
}

