<?php
class Gr_reportes extends CI_Model
{
    public function actualizar($a,$m)
    {
        $p=$a.$m;
        switch ($p)
        {
            case 20143:$this->db->query("call sp_insert_meta_gr_1(2014,3)");break;
            case 20144:$this->db->query("call sp_insert_meta_gr_1(2014,4)");break;
            case 20145:$this->db->query("call sp_insert_meta_gr_1(2014,5)");break;
            case 20146:$this->db->query("call sp_insert_meta_gr_1(2014,6)");break;
            case 20147:$this->db->query("call sp_insert_meta_gr_1(2014,7)");break;
            case 20148:$this->db->query("call sp_insert_meta_gr_1(2014,8)");break;
            case 20149:$this->db->query("call sp_insert_meta_gr_1(2014,9)");break;
            case 201410:$this->db->query("call sp_insert_meta_gr_1(2014,10)");break;
            case 201411:$this->db->query("call sp_insert_meta_gr_1(2014,11)");break;
	    case 201412:$this->db->query("call sp_insert_meta_gr_1(2014,12)");break;
	    case 20151:$this->db->query("call sp_insert_meta_gr_1(2015,1)");break;
	    case 20152:$this->db->query("call sp_insert_meta_gr_1(2015,2)");break;
	    case 20153:$this->db->query("call sp_insert_meta_gr_1(2015,3)");break;
	    case 20154:$this->db->query("call sp_insert_meta_gr_1(2015,4)");break;
	    case 20155:$this->db->query("call sp_insert_meta_gr_1(2015,5)");break;
	    case 20156:$this->db->query("call sp_insert_meta_gr_1(2015,6)");break;
	    case 20157:$this->db->query("call sp_insert_meta_gr_1(2015,7)");break;
	    case 20158:$this->db->query("call sp_insert_meta_gr_1(2015,8)");break;
	    case 20159:$this->db->query("call sp_insert_meta_gr_1(2015,9)");break;
	    case 201510:$this->db->query("call sp_insert_meta_gr_1(2015,10)");break;
	    case 201511:$this->db->query("call sp_insert_meta_gr_1(2015,11)");break;
	    case 201512:$this->db->query("call sp_insert_meta_gr_1(2015,12)");break;
	    case 20161:$this->db->query("call sp_insert_meta_gr_1(2016,1)");break;
	    case 20162:$this->db->query("call sp_insert_meta_gr_1(2016,2)");break;
	    case 20163:$this->db->query("call sp_insert_meta_gr_1(2016,3)");break;
	    case 20164:$this->db->query("call sp_insert_meta_gr_1(2016,4)");break;
	    case 20165:$this->db->query("call sp_insert_meta_gr_1(2016,5)");break;
	    case 20166:$this->db->query("call sp_insert_meta_gr_1(2016,6)");break;
	    case 20167:$this->db->query("call sp_insert_meta_gr_1(2016,7)");break;
	    case 20168:$this->db->query("call sp_insert_meta_gr_1(2016,8)");break;
	    case 20169:$this->db->query("call sp_insert_meta_gr_1(2016,9)");break;
	    case 201610:$this->db->query("call sp_insert_meta_gr_1(2016,10)");break;
	    case 201611:$this->db->query("call sp_insert_meta_gr_1(2016,11)");break;
	    case 201612:$this->db->query("call sp_insert_meta_gr_1(2016,12)");break;
	    case 20171:$this->db->query("call sp_insert_meta_gr_1(2017,1)");break;
	    case 20172:$this->db->query("call sp_insert_meta_gr_1(2017,2)");break;
	    case 20173:$this->db->query("call sp_insert_meta_gr_1(2017,3)");break;
	    case 20174:$this->db->query("call sp_insert_meta_gr_1(2017,4)");break;
	    case 20175:$this->db->query("call sp_insert_meta_gr_1(2017,5)");break;
	    case 20176:$this->db->query("call sp_insert_meta_gr_1(2017,6)");break;
	    case 20177:$this->db->query("call sp_insert_meta_gr_1(2017,7)");break;
	    case 20178:$this->db->query("call sp_insert_meta_gr_1(2017,8)");break;
	    case 20179:$this->db->query("call sp_insert_meta_gr_1(2017,9)");break;
	    case 201710:$this->db->query("call sp_insert_meta_gr_1(2017,10)");break;
	    case 201711:$this->db->query("call sp_insert_meta_gr_1(2017,11)");break;
	    case 201712:$this->db->query("call sp_insert_meta_gr_1(2017,12)");break;
            default:echo 0;break;
        }
    }
}	
?>