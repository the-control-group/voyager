<?php


namespace TCG\Voyager\Http\Requests;

use App\Exceptions\PublicException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\ValidationException;

abstract class BaseRequest extends FormRequest
{

    /**
     * @desc Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rule(){}
    /**
     * @desc the initial value for rule
     *
     * @var string
     */
    public $rule = "required";

    /**
     * @desc Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * @desc this function resolve validation error message
     *
     * @param  Validator  $validator
     *
     * @return void
     * @throws PublicException
     * @throws ValidationException
     */
    protected function failedValidation(Validator $validator)
    {
        $errors = (new ValidationException($validator))->errors();
        throw ValidationException::withMessages($errors);
    }

    /**
     * @desc this function to check if request is update request
     * @return bool
     */
    protected function isUpdatedRequest()
    {
        return request()->isMethod("PUT") || request()->isMethod("PATCH");
    }

    /**
     * @desc this function to return all required rule for an image
     * @return string
     */
    protected function imageRule()
    {
        if ($this->isUpdatedRequest()) {
            $this->rule = 'sometimes';
        }
        return "$this->rule|image|mimes:jpeg,png,jpg,gif,svg|max:2048";
    }

    /**
     * @desc this function to return all required rule for date request parameter
     * @return string
     */
    protected function dateRules()
    {
        if ($this->isUpdatedRequest()) {
            $this->rule = 'sometimes';
        }
        return "$this->rule|after:now";
    }

}
