<?php
/* WHMCS SMS Addon with GNU/GPL Licence
 * Zepson SMS - https://portal.zepsonsms.co.tz/
 * Zepson SMS Portal - https://zepsonsms.co.tz/
 * Version 1.0
 *
 *
 * Licence: GPLv3 (http://www.gnu.org/licenses/gpl-3.0.txt)
 * */
class zepsonsmsAPI {
    private $user_token; // USER API TOKEN
    private $user_key; //USER API KEY
    private $sender_id="ZEPSONSMS"; //USER SENDER KEY AND DEFAULT WebSMS
    private $country_code="255";//Default Country Code Bangladesh //880 with out +
    protected $url='https://portal.zepsonsms.co.tz/api/v3/sms/send?';// ALWAYS USE THIS LINK TO CALL API SERVICE

    public $msgType="plain";// Message type plain/voice/unicode/flash/music/mms/whatsapp
    public $route=0;// Your Routing Path Default 0
    public $file=false;// File URL for voice or whatsapp. Default not set
    public $scheduledate=false;//Date and Time to send message (YYYY-MM-DD HH:mm:ss) Default not use
    public $duration=false;//Duration of your voice message in seconds (required for voice)
    public $language=false;//Language of voice message (required for text-to-speach)

    /**
     * To Find your api details please log and go into https://zepsonsms.co.tz | https://www.portal.zepsonsms.co.tz
     */
    /**
     * Call to site
     */
    private function Call($params,$api_key){
        if($params){
            $params = str_replace(" ", '%20', $params);
            $curl_handle = curl_init();

            curl_setopt_array($curl_handle, array(
            CURLOPT_URL => $this->url.$params,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer '.$api_key
            ),
            ));

            $buffer = curl_exec($curl_handle);

            curl_close($curl_handle);

            if($buffer){
                return $buffer;
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

    /**
     * Set user Credentials
     * @return boolen
     */
    public function setUser($key,$token){
        if($key && $token){
            $this->user_key=$key;
            $this->user_token=$token;
            return true;
        }else{
            return false;
        }
    }

    /**
     * Set Sender ID
     * @return boolen
     */
    public function setSenderID($sender_id){
        if($sender_id){
            $this->sender_id=$sender_id;
            return true;
        }else{
            return false;
        }
    }

    /**
     * Set Default Routing
     * @return boolen
     */
    public function RouteNumber($number){
        if($number){
            $explode=str_split($number);
            if($explode[0]=="+"){
                unset($explode[0]);
                $number=implode("",$explode);
            }else{
                if($explode[0]==0){
                    unset($explode[0]);
                    $number=implode("",$explode);
                }
                $number=$this->country_code.$number;
            }
            return $number;
        }else{
            return false;
        }
    }

    /**
     * Check avalible credit balance
     * @return array
     */
    public function CheckBalance($json=FALSE){
        $param='action=check-balance&api_key='.$this->user_key.'&apitoken='.$this->user_token;
        if($result=$this->Call($param,$this->user_key)){
            if($json===FALSE){
                $c=json_decode($result,true);
                if($c['balance'] !="error"){
                    return false;
                }else{
                    return $c;
                }
            }else{
                return $result;
            }
        }else{
            return false;
        }
    }

    /**
     * Check SMS status
     * group_id = The group_id returned by send sms request
     * @return array
     */
    public function CheckStatus($group_id,$json=FALSE){
        if($group_id){
            $param="groupstatus&apikey=".$this->user_key."&apitoken=".$this->user_token."&groupid=".$group_id;
            if($res=$this->Call($param,$this->user_key)){
                if($json===FALSE){
                    $c=json_decode($res);//You can also use direct json by call json as true
                    if($c['status']=="error"){
                        return false;
                    }else{
                        return $c;
                    }
                }else{
                    return $res;
                }
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

    /**
     * Send Message
     * @return boolen
     */
    public function SendMessage($Mobile,$TEXT,$json=FALSE){
        $TEXT=urlencode($TEXT);
        if($this->sender_id !="" && $this->user_key !="" && $this->user_token !=""){
            if($Mobile){
                if($TEXT){
                   // $Mobile=$this->RouteNumber($Mobile); // Never used for whmcs  because this function already have
                    $param='&sender_id='.$this->sender_id.'&recipient='.$Mobile.'&type='.$this->msgType;
                    if($this->route != 0) $param.='&route='.$this->route;
                    if($this->msgType=="plain" || $this->msgType=="unicode"){
                        //SMS
                       $param.='&message='.$TEXT;
                    }elseif($this->msgType=="voice" || $this->msgType=="mms"){
                        //Voice And MMS
                        if($this->file){
                            $param.='&text='.$TEXT.'&file='.$this->file;
                            if($this->msgType=="voice" && $this->duration !=false){
                                $param.='&duration='.$this->duration;
                            }
                        }else{
                            return false;
                        }
                    }elseif($this->msgType=="whatsapp"){
                        //WhatsAPP
                        $param.='&text='.$TEXT;
                        if($this->file){
                            $param.='&file='.$this->file;
                        }
                    }elseif($this->msgType=="flash"){
                        //Flash
                        $param.='&text='.$TEXT;
                        if($this->file){
                            $param.='&file='.$this->file;
                        }
                    }
                    if($this->scheduledate!=false){
                        $param.='&schedule_time='.$this->scheduledate;
                    }
                    if($this->language!=false){
                        $param.='&language='.$this->language;
                    }
                    if($res=$this->Call($param,$this->user_key)){
                        if($json !=FALSE){
                            return $res;
                        }else{
                            $c=json_decode($res);
                            return $c;
                        }
                    }
                }else{
                    return false;
                }
            }else{
                return false;
            }
        }else{
            return false;
        }
    }

}
?>
