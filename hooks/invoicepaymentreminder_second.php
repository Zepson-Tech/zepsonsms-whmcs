<?php
$hook = array(
    'hook' => 'InvoicePaymentReminder',
    'function' => 'InvoicePaymentReminder_secondoverdue',
    'description' => array(
        'english' => 'Invoice payment reminder for second overdue'
    ),
    'type' => 'client',
    'extra' => '',
    'defaultmessage' => 'Hi {firstname} {lastname}, the payment for date {duedate}, associated with your is due. Kindly make the payment at the earliest to enjoy the services.',
    'variables' => '{firstname}, {lastname}, {duedate}'
);

if(!function_exists('InvoicePaymentReminder_secondoverdue')){
    function InvoicePaymentReminder_secondoverdue($args){

        if($args['type'] == "secondoverdue"){
            $api = new zepsonsms();
            $template = $api->getTemplateDetails(__FUNCTION__);
            if($template['active'] == 0){
                return null;
            }
            $settings = $api->apiSettings();
            if(!$settings['api_key'] || !$settings['api_token'] ){
                return null;
            }
        }else{
            return false;
        }

        $result = $api->getClientAndInvoiceDetailsBy($args['invoiceid']);
        $num_rows = mysql_num_rows($result);
        if($num_rows == 1){
            $UserInformation = mysql_fetch_assoc($result);

            $template['variables'] = str_replace(" ","",$template['variables']);
            $replacefrom = explode(",",$template['variables']);
            $replaceto = array($UserInformation['firstname'],$UserInformation['lastname'],$api->changeDateFormat($UserInformation['duedate']));
            $message = str_replace($replacefrom,$replaceto,$template['template']);

            $api->setCountryCode($UserInformation['country']);

            $api->setGsmnumber($UserInformation['gsmnumber']);
            $api->setMessage($message);
            $api->setUserid($UserInformation['userid']);
            $api->send();
        }
    }
}

return $hook;
