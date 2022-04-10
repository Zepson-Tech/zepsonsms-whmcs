<?php
$hook = array(
    'hook' => 'ClientChangePassword',
    'function' => 'ClientChangePassword',
    'description' => array(
        'english' => 'After client change password'
    ),
    'type' => 'client',
    'extra' => '',
    'variables' => '{firstname},{lastname}',
    'defaultmessage' => 'Hi {firstname} {lastname}, password has been changed successfully.',
);

if(!function_exists('ClientChangePassword')){
    function ClientChangePassword($args){
        $api = new zepsonsms();
        $template = $api->getTemplateDetails(__FUNCTION__);
        if($template['active'] == 0){
            return null;
        }
        $settings = $api->apiSettings();
        if(!$settings['api_token'] || !$settings['api_key'] ){
            return null;
        }

        $result = $api->getClientDetailsBy($args['userid']);
        $num_rows = mysql_num_rows($result);
        if($num_rows == 1){
            $UserInformation = mysql_fetch_assoc($result);
            $template['variables'] = str_replace(" ","",$template['variables']);
            $replacefrom = explode(",",$template['variables']);
            $replaceto = array($UserInformation['firstname'],$UserInformation['lastname']);
            $message = str_replace($replacefrom,$replaceto,$template['template']);

            $api->setCountryCode($UserInformation['country']);
            $api->setGsmnumber($UserInformation['gsmnumber']);
            $api->setUserid($args['userid']);
            $api->setMessage($message);
            $api->send();
        }
    }
}

return $hook;
