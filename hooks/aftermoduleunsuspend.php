<?php
$hook = array(
    'hook' => 'AfterModuleUnsuspend',
    'function' => 'AfterModuleUnsuspend',
    'description' => array(
        'english' => 'After module unsuspend'
    ),
    'type' => 'client',
    'extra' => '',
    'defaultmessage' => ' Hello! The services for the domain ({domain}) have now been made active.',
    'variables' => '{firstname},{lastname},{domain}'
);
if(!function_exists('AfterModuleUnsuspend')){
    function AfterModuleUnsuspend($args){
        $type = $args['params']['producttype'];

        if($type == "hostingaccount"){
            $api = new zepsonsms();
            $template = $class->getTemplateDetails(__FUNCTION__);
            if($template['active'] == 0){
                return null;
            }
            $settings = $class->apiSettings();
            if(!$settings['api'] || !$settings['apiparams'] ){
                return null;
            }
        }else{
            return null;
        }

        $result = $api->getClientDetailsBy($args['params']['clientsdetails']['userid']);
        $num_rows = mysql_num_rows($result);
        if($num_rows == 1){
            $UserInformation = mysql_fetch_assoc($result);

            $template['variables'] = str_replace(" ","",$template['variables']);
            $replacefrom = explode(",",$template['variables']);
            $replaceto = array($UserInformation['firstname'],$UserInformation['lastname'],$args['params']['domain']);
            $message = str_replace($replacefrom,$replaceto,$template['template']);

            $api->setCountryCode($UserInformation['country']);
            $api->setGsmnumber($UserInformation['gsmnumber']);
            $api->setUserid($args['params']['clientsdetails']['userid']);
            $api->setMessage($message);
            $api->send();
        }
    }
}
return $hook;
