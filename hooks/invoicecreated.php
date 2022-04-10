<?php
$hook = array(
    'hook' => 'InvoiceCreated',
    'function' => 'InvoiceCreated',
    'description' => array(

        'english' => 'After Invoice Creation'
    ),
    'type' => 'client',
    'extra' => '',
    'defaultmessage' => 'Hello {firstname} {lastname}, Your invoice with id {invoiceid} has been generated. Total amount is {currency}  {total}. The last day of payment is {duedate}. Kindly pay your bill before due date to use services without interruption',
    'variables' => '{firstname}, {lastname}, {duedate}, {total}, {invoiceid}, {currency}'
);
if(!function_exists('InvoiceCreated')){
    function InvoiceCreated($args){

        $api = new zepsonsms();
        $template = $api->getTemplateDetails(__FUNCTION__);
        if($template['active'] == 0){
            return null;
        }
        $settings = $api->apiSettings();
        if(!$settings['api_key'] || !$settings['api_token']){
            return null;
        }

        $userSql = "
        SELECT a.total,a.duedate,b.id as userid,b.firstname,b.lastname,b.currency,`b`.`country`,`b`.`phonenumber` as `gsmnumber` FROM `tblinvoices` as `a`
        JOIN tblclients as b ON b.id = a.userid
        WHERE a.id = '".$args['invoiceid']."'
        LIMIT 1
    ";

        $result = mysql_query($userSql);
        $num_rows = mysql_num_rows($result);
        if($num_rows == 1){
            $UserInformation = mysql_fetch_assoc($result);
            $currency_sql=mysql_query('SELECT code FROM tblcurrencies WHERE id='.$UserInformation['currency']);
            $replace_currency="";
            if(mysql_num_rows($currency_sql) > 0){
                $currency_result=mysql_fetch_assoc($currency_sql);
                $replace_currency=$currency_result['code'];
            }
            $template['variables'] = str_replace(" ","",$template['variables']);
            $replacefrom = explode(",",$template['variables']);
            $replaceto = array($UserInformation['firstname'],$UserInformation['lastname'],$api->changeDateFormat($UserInformation['duedate']),$UserInformation['total'],$args['invoiceid'],$replace_currency);
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
