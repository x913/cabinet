<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateBaseClientRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'contact_phone' => 'string|max:250'
        ];

       foreach($this->request->get('emails') as $key => $v) {
           $rules['emails.' . $key . '.email'] = 'required|email|max:120';
           $rules['emails.' . $key . '.comment'] = 'max:250';
           $rules['emails.' . $key . '.detail_type'] = 'integer|between:0,7';
       }
        return $rules;
    }
}
