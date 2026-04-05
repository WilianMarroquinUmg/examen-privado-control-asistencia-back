<?php

namespace App\Http\Requests\Api\Pensum;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Pensum\Facultad;

class UpdateFacultadApiRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return Facultad::$rules;
    }
}

