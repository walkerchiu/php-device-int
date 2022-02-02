<?php

namespace WalkerChiu\Device\Models\Forms;

use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Validator;
use WalkerChiu\Core\Models\Forms\FormRequest;

class DeviceFormRequest extends FormRequest
{
    /**
     * @Override Illuminate\Foundation\Http\FormRequest::getValidatorInstance
     */
    protected function getValidatorInstance()
    {
        $request = Request::instance();
        $data = $this->all();
        if (
            $request->isMethod('put')
            && empty($data['id'])
            && isset($request->id)
        ) {
            $data['id'] = (int) $request->id;
            $this->getInputSource()->replace($data);
        }

        return parent::getValidatorInstance();
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return Array
     */
    public function attributes()
    {
        return [
            'host_type'   => trans('php-device::device.host_type'),
            'host_id'     => trans('php-device::device.host_id'),

            'serial'      => trans('php-device::device.serial'),
            'identifier'  => trans('php-device::device.identifier'),
            'order'       => trans('php-device::device.order'),
            'is_enabled'  => trans('php-device::device.is_enabled'),

            'type'        => trans('php-device::device.type'),
            'ver_os'      => trans('php-device::device.ver_os'),
            'ver_driver'  => trans('php-device::device.ver_driver'),
            'ver_agent'   => trans('php-device::device.ver_agent'),
            'ver_app'     => trans('php-device::device.ver_app'),

            'name'        => trans('php-device::device.name'),
            'description' => trans('php-device::device.description'),
            'location'    => trans('php-device::device.location')
        ];
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return Array
     */
    public function rules()
    {
        $rules = [
            'host_type'   => 'required_with:host_id|string',
            'host_id'     => 'required_with:host_type|integer|min:1',

            'serial'      => '',
            'identifier'  => 'required|string|max:255',
            'order'       => 'nullable|numeric|min:0',
            'is_enabled'  => 'boolean',

            'type'        => '',
            "ver_os"      => '',
            'ver_driver'  => '',
            'ver_agent'   => '',
            'ver_app'     => '',

            'name'        => 'required|string|max:255',
            'description' => '',
            'location'    => ''
        ];

        $request = Request::instance();
        if (
            $request->isMethod('put')
            && isset($request->id)
        ) {
            $rules = array_merge($rules, ['id' => ['required','integer','min:1','exists:'.config('wk-core.table.device.devices').',id']]);
        } elseif ($request->isMethod('post')) {
            $rules = array_merge($rules, ['id' => ['nullable','integer','min:1','exists:'.config('wk-core.table.device.devices').',id']]);
        }

        return $rules;
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return Array
     */
    public function messages()
    {
        return [
            'id.integer'               => trans('php-core::validation.integer'),
            'id.min'                   => trans('php-core::validation.min'),
            'id.exists'                => trans('php-core::validation.exists'),
            'host_type.required_with'  => trans('php-core::validation.required_with'),
            'host_type.string'         => trans('php-core::validation.string'),
            'host_id.required_with'    => trans('php-core::validation.required_with'),
            'host_id.integer'          => trans('php-core::validation.integer'),
            'host_id.min'              => trans('php-core::validation.min'),
            'identifier.required'      => trans('php-core::validation.required'),
            'identifier.string'        => trans('php-core::validation.required'),
            'identifier.max'           => trans('php-core::validation.max'),
            'order.numeric'            => trans('php-core::validation.numeric'),
            'order.min'                => trans('php-core::validation.min'),
            'is_enabled.boolean'       => trans('php-core::validation.boolean'),

            'name.required'            => trans('php-core::validation.required'),
            'name.string'              => trans('php-core::validation.string'),
            'name.max'                 => trans('php-core::validation.max')
        ];
    }

    /**
     * Configure the validator instance.
     *
     * @param \Illuminate\Validation\Validator  $validator
     * @return void
     */
    public function withValidator($validator)
    {
        $validator->after( function ($validator) {
            $data = $validator->getData();
            if (isset($data['identifier'])) {
                $result = config('wk-core.class.device.device')::where('identifier', $data['identifier'])
                                ->when(isset($data['id']), function ($query) use ($data) {
                                    return $query->where('id', '<>', $data['id']);
                                  })
                                ->exists();
                if ($result)
                    $validator->errors()->add('identifier', trans('php-core::validation.unique', ['attribute' => trans('php-device::device.identifier')]));
            }
        });
    }
}
