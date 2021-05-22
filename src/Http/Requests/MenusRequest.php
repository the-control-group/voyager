<?php

namespace TCG\Voyager\Http\Requests;

class MenusRequest extends BaseRequest
{
    /**
     * @inheritDoc
     */
    public function rules()
    {
        return [
            "title" => "$this->rule",
            "url" => "$this->rule",
            "route" => "sometimes",
            "parameters" => "sometimes",
            "color" => "sometimes",
            "icon_class" => "sometimes",
            "id" => "sometimes",
        ];
    }
}
