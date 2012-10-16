<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); 

/**
* Base class for web service calls to share methods
*/
class BaseWsRequest {
    
    private $CI	= null;
    
    public function __construct() {
        $this->CI=&get_instance();
    }
    
    public function isValidLogin($xml) {
        if (!$this->CI) { $this->CI=&get_instance(); }
        $this->CI->load->model('user_model');
        
        // Check for a valid login
        if (isset($xml->auth->user) && isset($xml->auth->pass)) {
            // Check to see if they're a valid user
            if (!$this->CI->user_model->validate((string)$xml->auth->user, (string)$xml->auth->pass, true)) {
                // Invalid login! fail!
                return false;
            } else { return true; }
        }
    }
    
    /**
    * Check our public key, usually used for the Ajax calls on the site
    * to ensure there's no abuse
    */ 
    public function checkPublicKey() {
        if (!$this->CI) { $this->CI=&get_instance(); }
        
        //if it is public, check our "key" they sent along to prevent abuse
        $get_vars = explode('&', $_SERVER['QUERY_STRING']);
        if ($get_vars) {
            foreach ($get_vars as $k=>$v) { 
                $x=explode('=', $v); 
                if (count($x) > 1) {
                    $_GET[$x[0]]=$x[1]; 
                } else {
                    return false;
                }
            }
            
            $this->CI->load->helper('reqkey');
            $reqk=$_GET['reqk'];
            $seck=$_GET['seck'];
            $key_check = checkReqKey($seck, $reqk);
            if ($key_check) {
                return true;
            }
        }
        return false;
    }
    
    public function throwError($msg) {
        return array('output'=>'json','data'=>array(
                'items'=>array('msg'=>$msg))
        );
    }
    
    /**
     * Shortut to send a json response message
     *
     * @param string $message Message
     */
    public function sendJsonMessage($message) {
        return array('output'=>'json','data'=>array('items'=>array('msg'=>$message)));
    }
    
}
