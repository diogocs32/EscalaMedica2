<?php

namespace App\Http\Requests;

use App\Rules\SemSobreposicaoDeHorario;
use Illuminate\Foundation\Http\FormRequest;

class StoreAlocacaoRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Ajustar conforme sistema de autenticação
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'plantonista_id' => [
                'required',
                'integer',
                'exists:plantonistas,id',
                new SemSobreposicaoDeHorario(
                    $this->input('plantonista_id'),
                    $this->input('vaga_id'),
                    $this->input('data_plantao'),
                    $this->route('alocacao') // Para casos de update
                )
            ],
            'vaga_id' => [
                'required',
                'integer',
                'exists:vagas,id'
            ],
            'data_plantao' => [
                'required',
                'date',
                'after_or_equal:today'
            ],
            'observacoes' => [
                'nullable',
                'string',
                'max:1000'
            ],
            'status' => [
                'sometimes',
                'string',
                'in:agendado,em_andamento,concluido,cancelado'
            ]
        ];
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'plantonista_id.required' => 'O plantonista é obrigatório.',
            'plantonista_id.exists' => 'O plantonista selecionado não existe.',
            'vaga_id.required' => 'A vaga é obrigatória.',
            'vaga_id.exists' => 'A vaga selecionada não existe.',
            'data_plantao.required' => 'A data do plantão é obrigatória.',
            'data_plantao.date' => 'A data do plantão deve ser uma data válida.',
            'data_plantao.after_or_equal' => 'A data do plantão não pode ser anterior a hoje.',
            'observacoes.max' => 'As observações não podem ter mais que 1000 caracteres.',
            'status.in' => 'O status deve ser: agendado, em_andamento, concluido ou cancelado.'
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes(): array
    {
        return [
            'plantonista_id' => 'plantonista',
            'vaga_id' => 'vaga',
            'data_plantao' => 'data do plantão',
            'observacoes' => 'observações',
            'status' => 'status'
        ];
    }
}
