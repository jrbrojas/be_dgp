<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EntidadStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'departamento_id' => 'required|string',
            'distrito_id' => 'required|string',
            'provincia_id' => 'required|string',
            'nombre' => 'required|string|max:255',
            'fecha_registro' => 'required|date',
            'tipo' => 'required|string|max:255',
            'ubigeo' => 'required|integer',
            'anio' => 'required|integer',
        ];
    }
}
