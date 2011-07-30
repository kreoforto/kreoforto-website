<?php

class PHPMailer {
    
    private $ln                = "\r\n";
    private $types             = array("PLAIN_TEXT" => "text/plain", "HTML" => "text/html");
    private $type              = "text/plain";
    private $charset           = "utf-8";
    private $transfer_encoding = 8; 
    private $rfc_format        = "%s <%s>";
    
    private $to       = "";
    private $from     = "";
    private $subject  = "";
    private $msg      = "";
    private $cc       = "";
    private $bcc      = "";
    private $reply_to = "";


    public function setContentType($type) {
        
        if(array_key_exists($type, $this->types)) {
            $this->type = $this->types[$type];
        }
        else {
            throw new InvalidArgumentException;
        }
    }
    
    public function setCharset($charset) {
        $this->charset = $charset;
    }
    
    public function setTransferEncoding($bits) {
        $this->transfer_encoding = $bits;
    }
    
    public function noCR($cr = true) {
        (boolean)$cr ? $this->ln = "\n" : $this->ln = "\r\n";
    }
    
    public function setSubject($subject) {
        $this->subject = $subject;
    }
    
    public function setMessage($message) {
        $this->message = $message;
    }
    
    public function setReplyAddress($address) {
        $this->reply_to = $address;
    }
    
    public function setSender($sender_address, $sender_name = null) {
        $sender_name !== null ? $this->from = sprintf($this->rfc_format, $sender_name, $sender_address) : $this->from = $sender_address;
        ini_set('sendmail_from', $sender_address);
    }
    
    public function setRecipient($recipient_address, $recipient_name = null) {
        $this->to = $this->makeRecipientString($recipient_address, $recipient_name);
    }
    
    public function sendMail() {
        return mail($this->to, $this->subject, $this->message, $this->makeHeaders());
    }
    
    private function makeHeaders() {
        
        $headers = "";
        
        // set content headers
        $headers  = "Content-type: ".$this->type."; charset=".$this->charset.$this->ln;
        $headers .= "Content-Transfer-Encoding: ".$this->transfer_encoding."bit".$this->ln;
        if($this->type == "text/html") { $headers .= 'MIME-Version: 1.0'.$this->ln; }
        
        // set address headers
        $headers .= "From: ".$this->from.$this->ln;
        if(!empty($this->cc)) { $headers .= "Cc: ".$this->cc.$this->ln; }
        if(!empty($this->bcc)) { $headers .= "Bcc: ".$this->bcc.$this->ln; }
        if(!empty($this->reply_to)) { $headers .= "Reply-To: ".$this->reply_to.$this->ln; }
        
        // set mailer header
        $headers .= "X-Mailer: PHP/" . phpversion();
        
        return $headers;
    }
    
    public function setCC($recipient_address, $recipient_name = null) {
        $this->cc = $this->makeRecipientString($recipient_address, $recipient_name);
    }
    
    public function setBCC($recipient_address, $recipient_name = null) {
        $this->bcc = $this->makeRecipientString($recipient_address, $recipient_name);
    }
    
    private function makeRecipientString($recipient_address, $recipient_name = null) {
        $address = (array)$recipient_address;
        $recipient_name !== null ? $name = (array)$recipient_name : $name = null;
        
        $recipient_string = "";
        
        $i = 0;
        while($i < count($address) - 1) {
            
            if($name !== null) {
                $recipient_string .= sprintf($this->rfc_format, $name[$i], $address[$i]).",";
            }
            else {
                $recipient_string .= $address[$i].",";
            }
            
            $i++;
        }
        
        if($name !== null) {
            $recipient_string .= sprintf($this->rfc_format, $name[$i], $address[$i]);
        }
        else {
            $recipient_string .= $address[$i];
        }
        
        return $recipient_string;
    }
}

?>