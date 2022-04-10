<?php
/* WHMCS SMS Addon with GNU/GPL Licence
 * zepsonsms - https://portal.zepsonsms.co.tz/
 * zepsonsms Global - https://zepsonsms.co.tz/
 * Version 1.1
 *
 *
 * Licence: GPLv3 (http://www.gnu.org/licenses/gpl-3.0.txt)
 * */
if (!defined("WHMCS"))
	die("This file cannot be accessed directly");


function zepsonsms_config() {
    $configarray = [
            "name" => "Zepson SMS WHMCS notification addon",
            "description" => "Zepson SMS- WHMCS SMS Addon. To Create Account <a href=\"https://zepsonsms.co.tz\" target='_blank'>Zepson SMS</a> or <a href=\"https://portal.zepsonsms.co.tz\" target='_blank'>Zepson SMS Portal</a>",
            "version" => "1.1.0",
            "author" => "<img src='https://zepsonsms.co.tz/assets/images/logo.png' width='80'>",
            "language" => "english",
        ];
    return $configarray;
}

function zepsonsms_activate() {

    $query = "CREATE TABLE IF NOT EXISTS `mod_zepsonsms_messages` (`id` int(11) NOT NULL AUTO_INCREMENT,`group_id` varchar(40) NOT NULL,`to` varchar(15) DEFAULT NULL,`text` text,`uid` varchar(50) DEFAULT NULL,`status` varchar(10) DEFAULT NULL,`errors` text,`logs` text,`user` int(11) DEFAULT NULL,`datetime` datetime NOT NULL,PRIMARY KEY (`id`)) ENGINE=MyISAM DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;";
	mysql_query($query);

    $query = "CREATE TABLE IF NOT EXISTS `mod_zepsonsms_settings` (`id` int(11) NOT NULL AUTO_INCREMENT,`api_key` varchar(100) CHARACTER SET utf8 NOT NULL,`api_token` varchar(500) CHARACTER SET utf8 NOT NULL,`sender_id` varchar(500) CHARACTER SET utf8 NULL,`wantsmsfield` int(11) DEFAULT NULL,`gsmnumberfield` int(11) DEFAULT NULL,`dateformat` varchar(12) CHARACTER SET utf8 DEFAULT NULL,`version` varchar(6) CHARACTER SET utf8 DEFAULT NULL,PRIMARY KEY (`id`)) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;";
	mysql_query($query);

    $query = "INSERT INTO `mod_zepsonsms_settings` (`api_key`, `api_token`,`sender_id`, `wantsmsfield`, `gsmnumberfield`,`dateformat`, `version`) VALUES ('none', 'none','DEMO_SMS', 0, 0,'%d.%m.%y','1.0.0');";
	mysql_query($query);

    $query = "CREATE TABLE IF NOT EXISTS `mod_zepsonsms_templates` (`id` int(11) NOT NULL AUTO_INCREMENT,`name` varchar(50) CHARACTER SET utf8 NOT NULL,`type` enum('client','admin') CHARACTER SET utf8 NOT NULL,`admingsm` varchar(255) CHARACTER SET utf8 NOT NULL,`template` varchar(240) CHARACTER SET utf8 NOT NULL,`variables` varchar(500) CHARACTER SET utf8 NOT NULL,`active` tinyint(1) NOT NULL,`extra` varchar(3) CHARACTER SET utf8 NOT NULL,`description` text CHARACTER SET utf8,PRIMARY KEY (`id`)) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;";
	mysql_query($query);

    $query = "CREATE TABLE IF NOT EXISTS `mod_zepsonsms_otp` (`id` int(11) NOT NULL AUTO_INCREMENT,`otp` varchar(50) CHARACTER SET utf8 NOT NULL,`type` enum('client','admin') CHARACTER SET utf8 DEFAULT 'client',`relid` int(10) DEFAULT 0,`request` varchar(50) CHARACTER SET utf8 NOT NULL,`text` text,`status` tinyint(1) DEFAULT 0, `datetime` datetime NOT NULL, `phonenumber` text, PRIMARY KEY (`id`)) ENGINE=MyISAM  DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;";
	mysql_query($query);

    $query="
    INSERT INTO `mod_zepsonsms_templates` (`id`, `name`, `type`, `admingsm`, `template`, `variables`, `active`, `extra`, `description`) VALUES
    (1, 'InvoicePaymentReminder_Reminder', 'client', '', 'Hi {firstname} {lastname}, The due date for the payment is {duedate}. Kindly make the payment for uninterrupted services.', '{firstname}, {lastname}, {duedate}', 1, '', '{\"english\":\"Invoice Payment Reminder\"}'),
    (2, 'TicketUserReply_admin', 'admin', '', 'User has replied on the ticket with the subject ({subject})', '{subject}', 1, '', '{\"english\":\"When user has replied on the ticket.\"}'),
    (3, 'ClientLogin_admin', 'admin', '', 'Client with the name- ({firstname} {lastname} made entrance to the site.', '{firstname},{lastname}', 1, '', '{\"english\":\"When client login.\"}'),
    (4, 'TicketAdminReply', 'client', '', 'Dear {firstname} {lastname}, ({ticketsubject}) has been responded by admin.', '{firstname}, {lastname}, {ticketsubject}', 1, '', '{\"english\":\"After Reply By Admin\"}'),
    (5, 'ClientAdd_admin', 'admin', '', 'New customer has been added to the website.', '', 1, '', '{\"english\":\"When client is added.\"}'),
    (6, 'AfterModuleChangePassword', 'client', '', 'Hi {firstname} {lastname}, password for the {domain} has been changed successfully. Here are the details- Username: {username} Password: {password}', '{firstname}, {lastname}, {domain}, {username}, {password}', 1, '', '{\"english\":\"After module password changed\"}'),
    (7, 'TicketClose', 'client', '', 'Hello {firstname} {lastname}, The ticket with the ticket number ({ticketno}) has been successfully closed. In case of any issue, kindly contact us.', '{firstname}, {lastname}, {ticketno}', 1, '', '{\"english\":\"Ticket Closure\"}'),
    (8, 'DomainRenewalNotice', 'client', '', 'Hi {firstname} {lastname}, your domain- {domain} will expire in {x} days i.e. on {expirydate} . Kindly visit site  to renew it. Thank You!', '{firstname}, {lastname}, {domain},{expirydate},{x}', 1, '15', '{\"english\":\"Domain Renewal Notice before {x} days.\"}'),
    (9, 'AfterRegistrarRegistration_admin', 'admin', '', 'New domain named {domain} have been registered.', '{domain}', 1, '', '{\"english\":\"When domain registered.\"}'),
    (10, 'AfterRegistrarRegistrationFailed', 'client', '', 'Hi {firstname} {lastname}, Your domain name could not be registered.', '{firstname},{lastname},{domain}', 1, '', '{\"english\":\"Domain Registration Failure\"}'),
    (11, 'AcceptOrder_SMS', 'client', '', 'Dear {firstname} {lastname}, Your order associated with the ID {orderid} has been approved.', '{firstname},{lastname},{orderid}', 1, '', '{\"english\":\"Post Order Acceptance\"}'),
    (12, 'InvoicePaymentReminder_secondoverdue', 'client', '', 'Hi {firstname} {lastname}, the payment for date {duedate}, associated with your is due. Kindly make the payment at the earliest to enjoy the services.', '{firstname}, {lastname}, {duedate}', 1, '', '{\"english\":\"Invoice payment reminder for second overdue\"}'),
    (13, 'ClientChangePassword', 'client', '', 'Hi {firstname} {lastname}, password has been changed successfully.', '{firstname},{lastname}', 1, '', '{\"english\":\"After client change password\"}'),
    (14, 'AfterRegistrarRenewal', 'client', '', 'Dear {firstname} {lastname}, Your domain {domain} is successfully renewed.', '{firstname},{lastname},{domain}', 1, '', '{\"english\":\"After domain renewal\"}'),
    (15, 'AfterRegistrarRegistrationFailed_admin', 'admin', '', 'An error occurred while recording the domain {domain}', '{domain}', 1, '', '{\"english\":\"When client login.\"}'),
    (16, 'InvoiceCreated', 'client', '', 'Hello {firstname} {lastname}, Your invoice with id {invoiceid} has been generated. Total amount is  {total}. The last day of payment is {duedate}. Kindly pay your bill before due date to use services without interruption', '{firstname}, {lastname}, {duedate}, {total}, {invoiceid}', 1, '', '{\"english\":\"After Invoice Creation\"}'),
    (17, 'ClientAdd', 'client', '', 'Hi {firstname} {lastname}, Thank you for registering with us. The details of your account are- Email: {email} Password: {password}', '{firstname},{lastname},{email},{password}', 1, '', '{\"english\":\"After Client Registration\"}'),
    (18, 'AfterRegistrarRenewalFailed_admin', 'admin', '', 'An error occurred while updating the domain {domain}', '{domain}', 1, '', '{\"english\":\"When domain registration failed.\"}'),
    (19, 'AfterModuleSuspend', 'client', '', 'Hi {firstname} {lastname}, The service for your account  associated with the domain ({domain}) has been paused. Kindly contact us for more details.', '{firstname},{lastname},{domain}', 1, '', '{\"english\":\"After Module Suspension\"}'),
    (20, 'AdminLogin_admin', 'admin', '', 'A user with the username {username} has entered the admin panel.', '{username}', 1, '', 'null'),
    (21, 'InvoicePaid', 'client', '', 'Dear {firstname} {lastname}, payment for due date, {duedate} is done! Thank you.', '{firstname}, {lastname}, {duedate},{invoiceid}', 1, '', '{\"english\":\"Post Payment\"}'),
    (22, 'AfterRegistrarRegistration', 'client', '', 'Hi {firstname} {lastname}, Entries in the name field for the domain name ({domain}) have been successfully made.', '{firstname},{lastname},{domain}', 1, '', '{\"english\":\"After Domain Registration\"}'),
    (23, 'TicketOpen_admin', 'admin', '', 'A new ticket with the subject ({subject}) has been created.', '{subject}', 1, '', '{\"english\":\"When new ticket is created.\"}'),
    (24, 'InvoicePaymentReminder_Firstoverdue', 'client', '', 'Hi {firstname} {lastname}, the payment associated with your account with date {duedate} is not done yet. Kindly make the payment at the earliest to enjoy the services.', '{firstname}, {lastname}, {duedate}', 1, '', '{\"english\":\"Invoice payment reminder for first overdue\"}'),
    (25, 'AfterModuleUnsuspend', 'client', '', ' Hello! The services for the domain ({domain}) have now been made active.', '{firstname},{lastname},{domain}', 1, '', '{\"english\":\"After module unsuspend\"}'),
    (26, 'AfterRegistrarRenewal_admin', 'admin', '', 'The domain name {domain} has been renewed.', '{domain}', 1, '', '{\"english\":\"When domain is renewed.\"}'),
    (27, 'AfterModuleCreate_Hosting', 'client', '', 'Hello! The services for the domain ({domain}) have now been made active . The login details for the accounts are- Username:{username} Password: {password}', '{firstname}, {lastname}, {domain}, {username}, {password}', 1, '', '{\"english\":\"Post Service Activation\"}'),
    (28, 'InvoicePaymentReminder_thirdoverdue', 'client', '', 'Hi {firstname} {lastname}, the payment for date {duedate}, associated with your is due. Kindly make the payment at the earliest to enjoy the services.', '{firstname}, {lastname}, {duedate}', 1, '', '{\"english\":\"Invoice payment reminder for third overdue\"}'),
    (29, 'AfterModuleChangePackage', 'client', '', 'Hello {firstname} {lastname}, The product/service package for your domain {domain} is changed. Kindly contact us for more details', '{firstname},{lastname},{domain}', 1, '', '{\"english\":\"Following Module Package Change\"}');
    ";
    mysql_query($query);
    require_once("api.php");
    require_once("lib/zepsonsms.class.php");
    $api = new zepsonsms();
    $api->checkLists();

    return array('status'=>'success','description'=>'ZepsonSMS WHMCS Addon successfully activated');
}

function zepsonsms_deactivate() {

    $query = "DROP TABLE `mod_zepsonsms_templates`";
	mysql_query($query);
    $query = "DROP TABLE `mod_zepsonsms_settings`";
    mysql_query($query);
    $query = "DROP TABLE `mod_zepsonsms_messages`";
    mysql_query($query);
	//DROP Table for OTP
    $query = "DROP TABLE `mod_zepsonsms_otp`";
    mysql_query($query);

    return array('status'=>'success','description'=>'ZepsonSMS Addon successfully deactivated');
}

function Newsletters_upgrade($vars) {
    $version = $vars['version'];

    switch($version){
        case "1":
        break;
    }

    $api = new zepsonsms();
    $api->checkLists();
}




function zepsonsms_output($vars){
	$modulelink = $vars['modulelink'];
	$version = $vars['version'];
	$LANG = $vars['_lang'];
	putenv("TZ=Asia/Colombo");
    $api = new zepsonsms();
    $tab = $_GET['tab'];
    echo '<div id="newsletters_sms_system">

    <style>
    .contentarea{
        background: #f5f5f5 !important;
    }
    #clienttabs *{
        margin: inherit;
        padding: inherit;
        border: inherit;
        color: inherit;
        background: inherit;
        background-color: inherit;
    }


    #clienttabs{position: relative; z-index: 99;}
     #clienttabs ul li {
        display: inline-block;
        margin-right: 3px;
        border: 1px solid #ddd;
        border-bottom:0px;
        padding: 12px;
        margin-bottom: -1px;
     }
     #clienttabs ul a {
     border: 0px;;
     }
     #clienttabs ul {
        float:left;
        margin-bottom:0px;
     }
     #clienttabs{
        float:left;
     }


    </style>



    <div id="clienttabs">
        <ul>
            <li class="' . (($tab == "settings" || (@$_GET['type'] == "" && $tab == ""))?"tabselected":"tab") . '"><a href="addonmodules.php?module=zepsonsms&tab=settings">'.$LANG['settings'].'</a></li>
            <li class="' . ((@$_GET['type'] == "client")?"tabselected":"tab") . '"><a href="addonmodules.php?module=zepsonsms&tab=templates&type=client">'.$LANG['clientsmstemplates'].'</a></li>
            <li class="' . ((@$_GET['type'] == "admin")?"tabselected":"tab") . '"><a href="addonmodules.php?module=zepsonsms&tab=templates&type=admin">'.$LANG['adminsmstemplates'].'</a></li>
            <li class="' . (($tab == "sendbulk")?"tabselected":"tab") . '"><a href="addonmodules.php?module=zepsonsms&tab=sendbulk">'.$LANG['sendsms'].'</a></li>
            <li class="' . (($tab == "messages")?"tabselected":"tab") . '"><a href="addonmodules.php?module=zepsonsms&amp;tab=messages">'.$LANG['messages'].'</a></li>
            <li class="' . (($tab == "c")?"tabselected":"tab") . '"><a href="addonmodules.php?module=zepsonsms&amp;tab=support">'.$LANG['support'].'</a></li>
        </ul>
    </div>
    <div style="clear:both;"></div>
    ';

    if (!isset($tab) || $tab == "settings")
    {
        /* UPDATE SETTINGS */
        if (isset($_POST['params'])) {
            $update = array(
                "api_key" => $_POST['api_key'],
                "api_token" => $_POST['api_token'],
                "sender_id" => $_POST['sender_id'],
                'wantsmsfield' => $_POST['wantsmsfield'],
                'gsmnumberfield' => $_POST['gsmnumberfield'],
                'dateformat' => $_POST['dateformat']
            );
            update_query("mod_zepsonsms_settings", $update, "");
        }
        /* UPDATE SETTINGS */

        $settings = $api->apiSettings();
        $api_key=$settings['api_key'];
        $api_token=$settings['api_token'];
        $sender_id=$settings['sender_id'];

//Start User Authorization Check
        $checker=new zepsonsmsAPI();
        $checker->setUser($api_key,$api_token);
        $checker->CheckBalance();


        if($checker=='balance'){
        echo '
 <style>
.card {
  box-shadow: 0 4px 8px 0 rgba(0,0,0,0.2);
  transition: 0.3s;
  width: 100%;
}


.container {
  padding: 2px 16px;
  display: flex;
  justify-content: center;
  flex-direction: row;
}
</style>
<br><br>
        <div class="card">
         <div class="container">
        <form action="" method="post" id="form">
        <input type="hidden" name="action" value="save" />
            <div class="internalDiv">
            <span id="responsemsg"></span>
			<input type="hidden" name="params" value="0"/>

                            <td class="fieldlabel" width="30%">'.$LANG['apikey'].'</td>
                            <div class="input-group">
                            <input type="text" name="api_key" class="form-control" size="40" value="' . $settings['api_key'] . '">
                            </div>



                            <input type="hidden" name="api_token" class="form-control" size="40" value="NOVALUE">


                            <td class="fieldlabel" width="30%">'.$LANG['senderid'].'</td>
                            <div class="input-group">
                            <input type="text" name="sender_id" class="form-control" size="40" value="' . $settings['sender_id'] . '">
                            </div>
                            <td class="fieldlabel" width="30%">'.$LANG['dateformat'].'</td>
                            <div class="input-group">
                            <input type="text" name="dateformat" class="form-control" size="40" value="' . $settings['dateformat'] . '">  </div> e.g:  %d.%m.%y (27.01.2014)

                            </div>
            <div class="btn-container">
                <input type="submit" value="'.$LANG['save'].'" class="btn btn-primary" />
            </div>
        </form>
        </div>
        </div>
        ';

        }
else{
    //Please setup your api akey
        echo '
        <style>
.card {
  box-shadow: 0 4px 8px 0 rgba(0,0,0,0.2);
  transition: 0.3s;
  width: 100%;
}


.container {
  padding: 2px 16px;
  display: flex;
  justify-content: center;
  flex-direction: row;
}
</style>
<br><br>
        <div class="card">
         <div class="container">
        <form action="" method="post" id="form">
        <input type="hidden" name="action" value="save" />
            <div class="internalDiv">
            <span id="responsemsg"></span>
			<input type="hidden" name="params" value="0"/>

                            <td class="fieldlabel" width="30%">'.$LANG['apikey'].'</td>
                            <div class="input-group">
                            <input type="text" name="api_key" class="form-control" size="40" value="' . $settings['api_key'] . '">
                            </div>


                            <input type="hidden" name="api_token" class="form-control" size="40" value="NOVALUE">


                            <td class="fieldlabel" width="30%">'.$LANG['senderid'].'</td>
                            <div class="input-group">
                            <input type="text" name="sender_id" class="form-control" size="40" value="' . $settings['sender_id'] . '">
                            </div>
                            <td class="fieldlabel" width="30%">'.$LANG['dateformat'].'</td>
                            <div class="input-group">
                            <input type="text" name="dateformat" class="form-control" size="40" value="' . $settings['dateformat'] . '">  </div> e.g:  %d.%m.%y (27.01.2014)

                            </div>
            <div class="btn-container">
                <input type="submit" value="'.$LANG['save'].'" class="btn btn-primary" />
            </div>
        </form>
        </div>
        </div>
        ';
}

    }
    elseif ($tab == "templates")
    {
        if (isset($_POST['params'])) {
            $where = array("type" => array("sqltype" => "LIKE", "value" => $_GET['type']));
            $result = select_query("mod_zepsonsms_templates", "*", $where);
            while ($data = mysql_fetch_array($result)) {
                if ($_POST[$data['id'] . '_active'] == "on") {
                    $tmp_active = 1;
                } else {
                    $tmp_active = 0;
                }
                $update = array(
                    "template" => $_POST[$data['id'] . '_template'],
                    "active" => $tmp_active
                );

                if(isset($_POST[$data['id'] . '_extra'])){
                    $update['extra']= trim($_POST[$data['id'] . '_extra']);
                }
                if(isset($_POST[$data['id'] . '_admingsm'])){
                    $update['admingsm']= $_POST[$data['id'] . '_admingsm'];
                    $update['admingsm'] = str_replace(" ","",$update['admingsm']);
                }
                update_query("mod_zepsonsms_templates", $update, "id = " . $data['id']);
            }
        }

        echo '<form action="" method="post">
        <input type="hidden" name="action" value="save" />
        <input type="hidden" name="params" value="0"/>
            <div class="internalDiv">
                <table class="form" width="100%" border="0" cellspacing="2" cellpadding="3" style="margin:0px;border: 0px;">
                    <tbody>';
        $where = array("type" => array("sqltype" => "LIKE", "value" => $_GET['type']));
        $result = select_query("mod_zepsonsms_templates", "*", $where);

        while ($data = mysql_fetch_array($result)) {
            if ($data['active'] == 1) {
                $active = 'checked = "checked"';
            } else {
                $active = '';
            }
            $desc = json_decode($data['description']);
            if(isset($desc->$LANG['lang'])){
                $name = $desc->$LANG['lang'];
            }else{
                $name = $data['name'];
            }
            echo '
                <tr>
                    <td class="fieldlabel" width="30%">' . $name . '</td>
                    <td class="fieldarea">
                        <textarea cols="50" name="' . $data['id'] . '_template">' . $data['template'] . '</textarea>
                    </td>
                </tr>';
            echo '
            <tr>
                <td class="fieldlabel"  style="float:right;">'.$LANG['parameter'].'</td>
                <td>' . $data['variables'] . '</td>
            </tr>
            ';
            if(!empty($data['extra'])){
                echo '
                <tr>
                    <td class="fieldlabel" width="30%">'.$LANG['ekstra'].'</td>
                    <td class="fieldarea">
                        <input type="text" name="'.$data['id'].'_extra" value="'.$data['extra'].'">
                    </td>
                </tr>
                ';
            }
            if($_GET['type'] == "admin"){
                echo '
                <tr>
                    <td class="fieldlabel" width="30%">'.$LANG['admingsm'].'</td>
                    <td class="fieldarea">
                        <input type="text" class="extraField" name="'.$data['id'].'_admingsm" placeholder="Ex :  255654485755,255752771650" value="'.$data['admingsm'].'">
                    </td>
                </tr>
                ';
            }
            echo '
            <tr>
                <td class="fieldlabel" width="30%" style="float:right;">'.$LANG['active'].'</td>
                <td><input type="checkbox" value="on" name="' . $data['id'] . '_active" ' . $active . '></td>
            </tr>
            ';




            echo '<tr>
                <td colspan="2"><hr></td>
            </tr>';
        }
        echo '
        </tbody>
                </table>

            </div>
            <div class="btn-container">
                <input type="submit" value="'.$LANG['save'].'" class="btn btn-primary" />
            </div>
            </form>
            ';

    }
    elseif ($tab == "messages")
    {
        if(!empty($_GET['deletesms'])){
            $smsid = (int) $_GET['deletesms'];
            $sql = "DELETE FROM mod_PHsms_messages WHERE id = '$smsid'";
            mysql_query($sql);
        }
        echo  '
        <!--<script src="http://ajax.aspnetcdn.com/ajax/jquery.dataTables/1.9.4/jquery.dataTables.min.js"></script>
        <link rel="stylesheet" href="http://ajax.aspnetcdn.com/ajax/jquery.dataTables/1.9.4/css/jquery.dataTables.css" type="text/css">
        <link rel="stylesheet" href="http://ajax.aspnetcdn.com/ajax/jquery.dataTables/1.9.4/css/jquery.dataTables_themeroller.css" type="text/css">
        <script type="text/javascript">
            $(document).ready(function(){
                $(".datatable").dataTable();
            });
        </script>-->

        <div class="internalDiv" style="padding:20px !important;">
        <table class="datatable" border="0" cellspacing="1" cellpadding="3" style="margin: 0px; border: 0px;">
        <thead>
            <tr>
                <th>#</th>
                <th>'.$LANG['client'].'</th>
                <th>'.$LANG['gsmnumber'].'</th>
                <th width="50%" >'.$LANG['message'].'</th>
                <th>'.$LANG['datetime'].'</th>
                <th width="40"></th>
            </tr>
        </thead>
        <tbody>
        ';

        // Getting pagination values.
        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = (isset($_GET['limit']) && $_GET['limit']<=50) ? (int)$_GET['limit'] : 10;
        $start  = ($page > 1) ? ($page*$limit)-$limit : 0;
        $order = isset($_GET['order']) ? $_GET['order'] : 'DESC';
        /* Getting messages order by date desc */
        $sql = "SELECT `m`.*,`user`.`firstname`,`user`.`lastname`
        FROM `mod_zepsonsms_messages` as `m`
        JOIN `tblclients` as `user` ON `m`.`user` = `user`.`id`
        ORDER BY `m`.`datetime` {$order} limit {$start},{$limit}";
        $result = mysql_query($sql);
        $i = 0;

        //Getting total records
        $total = "SELECT count(id) as toplam FROM `mod_zepsonsms_messages`";
        $sonuc = mysql_query($total);
        $sonuc = mysql_fetch_array($sonuc);
        $toplam = $sonuc['toplam'];

        //Page calculation
        $sayfa = ceil($toplam/$limit);

        while ($data = mysql_fetch_array($result)) {
            if($data['group_id'] && $data['status'] == ""){
                $status = $api->getReport($data['phid']);
                mysql_query("UPDATE mod_zepsonsms_messages SET status = '$status' WHERE id = ".$data['id']);
            }else{
                $status = $data['status'];
            }

            $i++;

            echo  '<tr>

            <td>'.$data['id'].'</td>
            <td><a href="clientssummary.php?userid='.$data['user'].'">'.$data['firstname'].' '.$data['lastname'].'</a></td>
            <td>'.$data['to'].'</td>
            <td>'.$data['text'].'</td>
            <td><center>'.$data['datetime'].'</center></td>
            <td><center><a href="addonmodules.php?module=zepsonsms&tab=messages&deletesms='.$data['id'].'" title="'.$LANG['delete'].'"><img src="images/delete.gif" width="16" height="16" border="0" alt="Delete"></a></center></td></tr>';
        }
        /* Getting messages order by date desc */

        echo '
        </tbody>
        </table>
        ';
        $list="";
        for($a=1;$a<=$sayfa;$a++)
        {
            $selected = ($page==$a) ? 'selected="selected"' : '';
            $list.="<option value='addonmodules.php?module=zepsonsms&tab=messages&page={$a}&limit={$limit}&order={$order}' {$selected}>{$a}</option>";
        }
        echo "<select  onchange=\"this.options[this.selectedIndex].value && (window.location = this.options[this.selectedIndex].value);\">{$list}</select></div>";

    }
    elseif($tab=="sendbulk")
    {
        $settings = $api->apiSettings();

        if(!empty($_POST['client'])){
            $userinf = explode("_",$_POST['client']);
            $userid = $userinf[0];
            $gsmnumber = $userinf[1];
            $country = $userinf[4];

            $replacefrom = array("{firstname}","{lastname}");
            $replaceto = array($userinf[2],$userinf[3]);
            $message = str_replace($replacefrom,$replaceto,$_POST['message']);

            ;

            $api->setCountryCode($api->getCodeBy($country));
            $api->setGsmnumber($gsmnumber);
            $api->setMessage($message);
            $api->setUserid($userid);

            $result = $api->send();
            if($result == false){
                $responseToShow =  $api->getErrors();
            }else{
                $responseToShow =  $LANG['smssent'].' '.$gsmnumber;
            }

            if($_POST["debug"] == "ON"){
                $debug = 1;
            }
        }
        $userSql = "SELECT `a`.`id`,`a`.`firstname`, `a`.`lastname`, `a`.`country`, `a`.`phonenumber` as `gsmnumber`
        FROM `tblclients` as `a` order by `a`.`firstname`";

        $clients = '';
        $result = mysql_query($userSql);
        while ($data = mysql_fetch_array($result)) {
            $clients .= '<option value="'.$data['id'].'_'.$data['gsmnumber'].'_'.$data['firstname'].'_'.$data['lastname'].'_'.$data['country'].'">'.$data['firstname'].' '.$data['lastname'].' (#'.$data['id'].')</option>';
        }

        echo '
        <script>
        jQuery.fn.filterByText = function(textbox, selectSingleMatch) {
          return this.each(function() {
            var select = this;
            var options = [];
            $(select).find("option").each(function() {
              options.push({value: $(this).val(), text: $(this).text()});
            });
            $(select).data("options", options);
            $(textbox).bind("change keyup", function() {
              var options = $(select).empty().scrollTop(0).data("options");
              var search = $.trim($(this).val());
              var regex = new RegExp(search,"gi");

              $.each(options, function(i) {
                var option = options[i];
                if(option.text.match(regex) !== null) {
                  $(select).append(
                     $("<option>").text(option.text).val(option.value)
                  );
                }
              });
              if (selectSingleMatch === true &&
                  $(select).children().length === 1) {
                $(select).children().get(0).selected = true;
              }
            });
          });
        };
        $(function() {
          $("#clientdrop").filterByText($("#textbox"), true);
        });
        </script>';



        echo '<form action="" method="post">
        <input type="hidden" name="action" value="save" />
            <div class="internalDiv" >'.$responseToShow.'
                <table class="form" width="100%" border="0" cellspacing="2" cellpadding="3" style="margin:0px;border: 0px;">
                    <tbody>
                        <tr>
                            <td class="fieldlabel" width="30%">'.$LANG['client'].'</td>
                            <td class="fieldarea">
                                <input id="textbox" type="text" placeholder="Type client name" style="width:498px;padding:5px"><br>
                                <select name="client" class="sel" multiple id="clientdrop" style="padding:5px">
                                    <option value="">'.$LANG['selectclient'].'</option>
                                    ' . $clients . '
                                </select>
                            </td>
                        </tr>
                        <tr>
                            <td class="fieldlabel" width="30%">'.$LANG['message'].'</td>
                            <td class="fieldarea">
                               <textarea cols="70" rows="5" name="message" style="width:498px;padding:5px"></textarea>
                            </td>
                        </tr>
                        <tr>
                            <td class="fieldlabel" width="30%">Parameters :</td>
                            <td class="fieldarea">
                                {firstname},{lastname}
                            </td>
                        </tr>
                    </tbody>
                </table>

            </div>
            <div class="btn-container">
                <input type="submit" value="'.$LANG['save'].'" class="btn btn-primary" />
            </div>
        </form>';


    }
    elseif($tab == "support"){

        echo '<div class="internalDiv" style="padding:20px !important;">';
        echo $LANG['cmodulesversion'];
        echo $LANG['latestmodules'];
        echo $LANG['phoneus'];
        echo $LANG['emailus'];
        echo $LANG['website'];
        echo $LANG['clientportal'];
        echo $LANG['smsportal'];

        echo '</div>';


    }

    $credit =  $api->getBalance();
    if($credit['balance']){
        echo '
            <div style="text-align: left;background-color: whiteSmoke;margin: 0px;padding: 5px; border: 1px solid #ddd;">
            <b>'.$LANG['balance'].':</b>
            </div>';
    }
	echo $LANG['lisans'];
    echo '</div>';
}
