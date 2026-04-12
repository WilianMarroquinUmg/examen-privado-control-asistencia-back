<?php

namespace App\Http\Requests\Api\EspacioTrabajo;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\EspacioTrabajo\TrabajoEspacio;

class UpdateTrabajoEspacioApiRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return TrabajoEspacio::$rules;
    }
}

