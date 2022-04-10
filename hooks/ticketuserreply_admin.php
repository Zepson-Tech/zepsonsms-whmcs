<?php
$hook = array(
    'hook' => 'TicketUserReply',
    'function' => 'TicketUserReply_admin',
    'description' => array(
        'english' => 'When user has replied on the ticket.'
    ),
    'type' => 'admin',
    'extra' => '',
    'defaultmessage' => 'User has replied on the ticket with the subject ({subject})',
    'variables' => '{subject}'
);

if(!function_exists('TicketUserReply_admin')){
    function TicketUserReply_admin($args){
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
                $api->setGsmnumber( trim($gsm));
                $api->setUserid($args['userid']);
                $api->setMessage($message);
                $api->send();
            }
        }
    }
}

return $hook;
