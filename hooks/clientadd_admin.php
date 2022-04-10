<?php
$hook = array(
    'hook' => 'ClientAdd',
    'function' => 'ClientAdd_admin',
    'description' => array(
        'english' => 'When client is added.'
    ),
    'type' => 'admin',
    'extra' => '',
    'defaultmessage' => 'New customer has been added to the website.',
    'variables' => ''
);
if(!function_exists('ClientAdd_admin')){
    function ClientAdd_admin($args){
        $api = new zepsonsms();
        $template = $api->getTemplateDetails(__FUNCTION__);
        if($template['active'] == 0){
            return null;
        }
        $settings = $api->apiSettings();
        if(!$settings['api'] || !$settings['apiparams'] ){
            return null;
        }
        $admingsm = explode(",",$template['admingsm']);

        foreach($admingsm as $gsm){
            if(!empty($gsm)){
                $api->setGsmnumber(trim($gsm));
                $api->setUserid($args['userid']);
                $api->setMessage($template['template']);
                $api->send();
            }
        }
    }
}
return $hook;
