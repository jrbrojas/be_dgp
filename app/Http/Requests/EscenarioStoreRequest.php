<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EscenarioStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'id_formulario' => 'required|string',
            'fecha_inicio' => 'required|date',
            'fecha_fin' => 'required|date',
            'nombre' => 'required|string|max:255',
            'url_base' => 'required|string',
            'tipo' => 'required|string|max:255',
            'ubigeo' => 'required|integer',
            'anio' => 'required|integer',
        ];
    }
}
