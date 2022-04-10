<?php
$hook = array(
    'hook' => 'TicketOpen',
    'function' => 'TicketOpen_admin',
    'description' => array(
        'english' => 'When new ticket is created.'
    ),
    'type' => 'admin',
    'extra' => '',
    'defaultmessage' => 'A new ticket with the subject ({subject}) has been created.',
    'variables' => '{subject}'
);

if(!function_exists('TicketOpen_admin')){
    function TicketOpen_admin($args){
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
        $replaceto = array($args['subject']);
        $message = str_replace($replacefrom,$replaceto,$template['template']);

        foreach($admingsm as $gsm){
            if(!empty($gsm)){
                $api->setGsmnumber(trim($gsm));
                $api->setUserid($args['userid']);
                $api->setMessage($message);
                $api->send();
            }
        }
    }
}

return $hook;
