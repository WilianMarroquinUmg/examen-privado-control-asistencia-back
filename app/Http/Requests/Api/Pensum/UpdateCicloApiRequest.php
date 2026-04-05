<?php

namespace App\Http\Requests\Api\Pensum;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Pensum\Ciclo;

class UpdateCicloApiRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return Ciclo::$rules;
    }
}

