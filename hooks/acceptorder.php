<?php
$hook = array(
    'hook' => 'AcceptOrder',
    'function' => 'AcceptOrder_SMS',
    'description' => array(
        'english' => 'Post Order Acceptance'
    ),
    'type' => 'client',
    'extra' => '',
    'defaultmessage' => 'Dear {firstname} {lastname}, Your order associated with the ID {orderid} has been approved.',
    'variables' => '{firstname},{lastname},{orderid}'
);
if(!function_exists('AcceptOrder_SMS')){
    function AcceptOrder_SMS($args){

        $api = new zepsonsms();
        $template = $api->getTemplateDetails(__FUNCTION__);
        if($template['active'] == 0){
            return null;
        }
        $settings = $api->apiSettings();

        if(!$settings['api_key'] || !$settings['api_token'] ){
            return null;
        }


        $userSql = "SELECT `a`.`id`,`a`.`firstname`, `a`.`lastname`, `a`.`phonenumber` as `gsmnumber`, `a`.`country`
        FROM `tblclients` as `a`
        WHERE `a`.`id` IN (SELECT userid FROM tblorders WHERE id = '".$args['orderid']."')
        LIMIT 1";

        $result = mysql_query($userSql);
        $num_rows = mysql_num_rows($result);
        if($num_rows == 1){
            $UserInformation = mysql_fetch_assoc($result);

            $template['variables'] = str_replace(" ","",$template['variables']);
            $replacefrom = explode(",",$template['variables']);
            $replaceto = array($UserInformation['firstname'],$UserInformation['lastname'],$args['orderid']);
            $message = str_replace($replacefrom,$replaceto,$template['template']);
            $api->setCountryCode($UserInformation['country']);
            $api->setGsmnumber($UserInformation['gsmnumber']);
            $api->setUserid($UserInformation['id']);
            $api->setMessage($message);
            $api->send();
        }
    }
}

return $hook;
