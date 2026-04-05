<?php

namespace App\Http\Requests\Api\Pensum;

use Illuminate\Foundation\Http\FormRequest;
use App\Models\Pensum\Curso;

class UpdateCursoApiRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return Curso::$rules;
    }
}

