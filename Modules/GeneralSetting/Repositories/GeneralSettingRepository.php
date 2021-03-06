<?php

namespace Modules\GeneralSetting\Repositories;

use Modules\GeneralSetting\Entities\BusinessSetting;
use Modules\GeneralSetting\Entities\GeneralSetting;
use Modules\GeneralSetting\Entities\DateFormat;
use Modules\GeneralSetting\Entities\TimeZone;

class GeneralSettingRepository
{
    public function all()
    {
        return GeneralSetting::first();
    }

    public function getVerificationNotificationAll()
    {
        return BusinessSetting::where('category_type', 'verification and notifications')->get();
    }

    public function getVendorConfigurationAll()
    {
        return BusinessSetting::where('category_type', 'vendor_configuration')->get();
    }

    public function getSmsGatewaysAll()
    {
        return BusinessSetting::where('category_type', 'sms_gateways')->get();
    }

    public function getLanguagesAll()
    {
        return BusinessSetting::where('category_type', 'sms_gateways')->get();
    }

    public function getDateFormatsAll()
    {
        return DateFormat::all();
    }

    public function getTimezonesAll()
    {
        return TimeZone::all();
    }

    public function getGeneralInfoDetails()
    {
        return GeneralSetting::first();
    }

    public function update(array $data)
    {
        return GeneralSetting::first()->update($data);
    }

    public function updateShopLink($shopLinkUrl)
    {
        return GeneralSetting::first()->update(['shop_link_banner'=>$shopLinkUrl]);
    }

    public function updateActivationStatus($data)
    {
        return BusinessSetting::where('id',$data['id'])->update([
            'status' => $data['status'],
        ]);
    }

    public function updateActivationSmsStatus($data)
    {
        foreach ($this->getSmsGatewaysAll() as $key => $sms_gateway) {
            $sms_gateway->status = 0;
            $sms_gateway->save();
        }
        BusinessSetting::where('id',$data['sms_gateway_id'])->update([
            'status' => 1,
        ]);
        foreach ($data['types'] as $key => $type) {
            $this->overWriteEnvFile($type, $data[$type]);
        }
    }

    public function update_smtp_gateway_credential($data)
    {
        $general_setting = $this->getGeneralInfoDetails();
        $general_setting->mail_protocol = $data['mail_gateway'];
        $general_setting->save();

        foreach ($data['types'] as $key => $type) {
            $this->overWriteEnvFile($type, $data[$type]);
        }
    }

    public function overWriteEnvFile($type, $val)
    {
        $path = base_path('.env');
        if (file_exists($path)) {
            $val = '"'.trim($val).'"';
            if(is_numeric(strpos(file_get_contents($path), $type)) && strpos(file_get_contents($path), $type) >= 0){
                file_put_contents($path, str_replace(
                    $type.'="'.env($type).'"', $type.'='.$val, file_get_contents($path)
                ));
            }
            else{
                file_put_contents($path, file_get_contents($path)."\r\n".$type.'='.$val);
            }
        }

    }

    public static function envUpdate($key, $value)
    {
        $path = base_path('.env');

        if (file_exists($path)) {

            file_put_contents($path, str_replace(
                $key . '=' . env($key), $key . '=' . $value, file_get_contents($path)
            ));
        }
    }
    public function updateEmailFooterTemplate($data)
    {
        $general_setting = GeneralSetting::first()->update([
            'mail_footer' => $data['mail_footer']
        ]);
        return true;

    }

}
