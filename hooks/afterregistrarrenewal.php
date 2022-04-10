<?php
$hook = array(
    'hook' => 'AfterRegistrarRenewal',
    'function' => 'AfterRegistrarRenewal',
    'description' => array(
        'english' => 'After domain renewal'
    ),
    'type' => 'client',
    'extra' => '',
    'defaultmessage' => 'Dear {firstname} {lastname}, Your domain {domain} is successfully renewed.',
    'variables' => '{firstname},{lastname},{domain}'
);
if(!function_exists('AfterRegistrarRenewal')){
    function AfterRegistrarRenewal($args){
    $api = new zepsonsms();
    $template = $api->getTemplateDetails(__FUNCTION__);
    if($template['active'] == 0){
        return null;
    }
    $settings = $api->apiSettings();
    if(!$settings['api_key'] || !$settings['api_token'] ){
        return null;
    }

    $result = $api->getClientDetailsBy($args['params']['userid']);
    $num_rows = mysql_num_rows($result);
    if($num_rows == 1){
        $UserInformation = mysql_fetch_assoc($result);

        $template['variables'] = str_replace(" ","",$template['variables']);
        $replacefrom = explode(",",$template['variables']);
        $replaceto = array($UserInformation['firstname'],$UserInformation['lastname'],$args['params']['sld'].".".$args['params']['tld']);
        $message = str_replace($replacefrom,$replaceto,$template['template']);

        $api->setCountryCode($UserInformation['country']);
        $api->setGsmnumber($UserInformation['gsmnumber']);
        $api->setUserid($args['params']['userid']);
        $api->setMessage($message);
        $api->send();
    }

}
}

return $hook;
