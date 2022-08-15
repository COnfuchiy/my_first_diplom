<?php

namespace App\Http\Requests;

use App\Rules\SiteUrlsTextAreaRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class SiteSaveRequest extends FormRequest
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
            'page_select_type' => 'required|in:manual,sitemap',
            'manual_input_text_area' => ['nullable', 'required_if:page_select_type,manual', new SiteUrlsTextAreaRule],
            'sitemap_url' => 'nullable|required_if:page_select_type,sitemap|url',
            'monitoring_period' => 'required|integer|min:5',
            'timeout' => 'nullable|integer|min:0|max:10',
            'ssl_check' => 'nullable|boolean',
            'ssl_notify_num_days' => 'nullable|required_if:ssl_check,1|integer|min:0',
            'seo_psi_mobile_check' => 'nullable|boolean',
            'seo_psi_desktop_check' => 'nullable|boolean',
            'seo_psi_mobile_min_value' => 'nullable|required_if:seo_psi_mobile_check,1|integer|min:0|max:100',
            'seo_psi_desktop_min_value' => 'nullable|required_if:seo_psi_desktop_check,1|integer|min:0|max:100',
            'meta_check' => 'nullable|boolean',
            'chat_id' => 'required|exists:App\Models\TelegramChats,telegram_id',
            'availability_report_clear_num_days' => 'nullable|integer|min:0',
            'performance_report_clear_num_days' => 'nullable|integer|min:0',
        ];
        if(!isset($_POST['_method']) || $_POST['_method']!=='PATCH'){
            $rules['domain'] = [
                'required',
                'url',
                function ($attribute, $value, $fail) {
                    $monitoringSites = Auth::user()->sites;
                    if(count($monitoringSites)){
                        $sitesQuery = $monitoringSites->toQuery()->where('domain',$value);
                        if(count($sitesQuery->get())){
                            $fail("Домен $value уже существует в системе");
                        }
                    }
                },
            ];
        }
        return $rules;
    }

    /**
     * Prepare the data for validation.
     *
     * @return void
     */
    protected function prepareForValidation()
    {
        $this->merge(
            [
                'domain' => "https://$this->domain",
            ]
        );
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array
     */
    public function attributes()
    {
        return [
            'sitemap_url' => '«Адрес sitemap.xml»',
            'monitoring_period' => '«Переодичность мониторинга»',
            'timeout' => '«Таймаут запроса»',
            'ssl_notify_num_days' => '«Количество дней до истечения срока SSL-сертификата для уведомления»',
            'seo_psi_mobile_min_value' => '«Минимальное значение общего рейтинга производительности для мобильных устройств»',
            'seo_psi_desktop_min_value' => '«Минимальное значение общего рейтинга производительности для компьютеров»',
        ];
    }
}
