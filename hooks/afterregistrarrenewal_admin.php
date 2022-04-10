<?php
$hook = array(
    'hook' => 'AfterRegistrarRenewal',
    'function' => 'AfterRegistrarRenewal_admin',
    'description' => array(
        'english' => 'When domain is renewed.'
    ),
    'type' => 'admin',
    'extra' => '',
    'defaultmessage' => 'The domain name {domain} has been renewed.',
    'variables' => '{domain}'
);
if(!function_exists('AfterRegistrarRenewal_admin')){
    function AfterRegistrarRenewal_admin($args){
        $api = new zepsonsms();
        $template = $api->getTemplateDetails(__FUNCTION__);
        if($template['active'] == 0){
            return null;
        }
        $settings = $api->apiSettings();
        if(!$settings['api_key'] || !$settings['api_token'] ){
            return null;
        }
        $admingsm = explode(",",$template['admingsm']);

        $template['variables'] = str_replace(" ","",$template['variables']);
        $replacefrom = explode(",",$template['variables']);
        $replaceto = array($args['params']['sld'].".".$args['params']['tld']);
        $message = str_replace($replacefrom,$replaceto,$template['template']);

        foreach($admingsm as $gsm){
            if(!empty($gsm)){
                $api->setGsmnumber( trim($gsm));
                $api->setUserid($args['userid']);
                $api->setMessage($message);
                $api->send();
            }
        }
    }
}

return $hook;
