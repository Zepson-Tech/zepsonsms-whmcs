<?php
$hook = array(
    'hook' => 'ClientLogin',
    'function' => 'ClientLogin_admin',
    'description' => array(
        'english' => 'When client login.'
    ),
    'type' => 'admin',
    'extra' => '',
    'defaultmessage' => 'Client with the name- {firstname} {lastname} logged in to the site.',
    'variables' => '{firstname},{lastname}'
);

if(!function_exists('ClientLogin_admin')){
    function ClientLogin_admin($args){
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

        $result = $api->getClientDetailsBy($args['userid']);
        $num_rows = mysql_num_rows($result);

        if($num_rows == 1){
            $UserInformation = mysql_fetch_assoc($result);

			$template['variables'] = str_replace(" ","",$template['variables']);
			$replacefrom = explode(",",$template['variables']);
			$replaceto = array($UserInformation['firstname'],$UserInformation['lastname']);
			$message = str_replace($replacefrom,$replaceto,$template['template']);

			foreach($admingsm as $gsm){
				if(!empty($gsm)){
					$api->setGsmnumber( trim($gsm));
					$api->setUserid($args['userid']);
					$api->setMessage($message);
				}	$api->send();
            }
        }
    }
}

return $hook;
