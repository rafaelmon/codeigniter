<?php
class Captcha_model extends CI_Model
{
	//regstra captcha
	public function registra($data)
        {
            $query = $this->db->insert_string('ci_captcha', $data);
            $this->db->query($query);
        }
	public function valida($data)
        {
            // First, delete old captchas
            $expiration = (int)(time()-3600); // 1 hr
//            $this->db->where('captcha_time <',$expiration);
//            $this->db->delete('ci_captcha');
            $this->db->query("DELETE FROM ci_captcha WHERE captcha_time < ".$expiration);		

            // Then see if a captcha exists:
            $sql = "SELECT COUNT(*) AS count FROM ci_captcha WHERE word = ? AND ip_address = ? AND captcha_time >= ?";
            
            $word   = $data['word'];
            $ip     = $data['ip'];
            
            $binds = array($word, $ip, $expiration);
            $query = $this->db->query($sql, $binds);
            $row = $query->row();
            if ($row->count == 0)
                    return false;
            else
                    return true;
        }
	
}
?>