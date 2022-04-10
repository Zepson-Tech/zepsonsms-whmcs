<?php
$hook = array(
    'hook' => 'DailyCronJob',
    'function' => 'DomainRenewalNotice',
    'description' => array(
        'english' => 'Domain Renewal Notice before {x} days.'
    ),
    'type' => 'client',
    'extra' => '15',
    'defaultmessage' => 'Hi {firstname} {lastname}, your domain- {domain} will expire in {x} days i.e. on {expirydate} . Kindly visit site  to renew it. Thank You!',
    'variables' => '{firstname}, {lastname}, {domain},{expirydate},{x}'
);
if(!function_exists('DomainRenewalNotice')){
    function DomainRenewalNotice($args){

        $api = new zepsonsms();
        $template = $api->getTemplateDetails(__FUNCTION__);
        if($template['active'] == 0){
            return null;
        }
        $settings = $api->apiSettings();
        if(!$settings['api_key'] || !$settings['api_token'] ){
            return null;
        }

        $extra = $template['extra'];
        $sqlDomain = "SELECT  `userid` ,  `domain` ,  `expirydate`
           FROM  `tbldomains`
           WHERE  `status` =  'Active'";
        $resultDomain = mysql_query($sqlDomain);
        while ($data = mysql_fetch_array($resultDomain)) {
            $tarih = explode("-",$data['expirydate']);
            $yesterday = mktime (0, 0, 0, $tarih[1], $tarih[2] - $extra, $tarih[0]);
            $today = date("Y-m-d");
            if (date('Y-m-d', $yesterday) == $today){
                $result = $api->getClientDetailsBy($data['userid']);
                $num_rows = mysql_num_rows($result);
                if($num_rows == 1){
                    $UserInformation = mysql_fetch_assoc($result);
                    $template['variables'] = str_replace(" ","",$template['variables']);
                    $replacefrom = explode(",",$template['variables']);
                    $replaceto = array($UserInformation['firstname'],$UserInformation['lastname'],$data['domain'],$data['expirydate'],$extra);
                    $message = str_replace($replacefrom,$replaceto,$template['template']);

                    $api->setCountryCode($UserInformation['country']);
                    $api->setGsmnumber($UserInformation['gsmnumber']);
                    $api->setMessage($message);
                    $api->setUserid($data['userid']);
                    $api->send();
                }
            }
        }
    }
}
return $hook;
