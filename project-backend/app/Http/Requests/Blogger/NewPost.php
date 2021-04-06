<?php

namespace App\Http\Requests\Blogger;

use Illuminate\Foundation\Http\FormRequest;

class NewPost extends FormRequest
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
        return [
            "title" => "required|min:5|regex:/^[a-zA-Z ]+$/u",
            "description" => "required|min:25",
            "category_id" => "required",
            "status" => "required"
        ];
    }
}
