<?php
 if ( ! defined('BASEPATH')) exit('No direct script access allowed');
class Varios_library
{
	private $ci;
	private $client_type;
	
	public function extraer_string($str, $left, $right)
	{
		$str = substr(stristr($str, $left), strlen($left));
		$leftLen = strlen(stristr($str, $right));
		$leftLen = $leftLen ? -($leftLen) : strlen($str);
		$str = substr($str, 0, $leftLen);
		return $str;
	}
	
	public function xml2array($contents, $get_attributes=1, $priority = 'tag') 
	{
    	if(!$contents) return array();

    	if(!function_exists('xml_parser_create')) {
        	return array();
    	}

	    $parser = xml_parser_create('');
	    xml_parser_set_option($parser, XML_OPTION_TARGET_ENCODING, "UTF-8"); 
	    xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
	    xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
	    xml_parse_into_struct($parser, trim($contents), $xml_values);
	    xml_parser_free($parser);

    	if(!$xml_values) return;

	    $xml_array = array();
	    $parents = array();
	    $opened_tags = array();
	    $arr = array();
	
	    $current = &$xml_array;

	    $repeated_tag_index = array();
    	foreach($xml_values as $data) 
    	{
        	unset($attributes,$value);

	        extract($data);

	        $result = array();
	        $attributes_data = array();
        
        	if(isset($value)) 
        	{
            	if($priority == 'tag') $result = $value;
            	else $result['value'] = $value;
        	}

	        if(isset($attributes) and $get_attributes) 
	        {
	            foreach($attributes as $attr => $val) 
	            {
	                if($priority == 'tag') $attributes_data[$attr] = $val;
	                else $result['attr'][$attr] = $val;
	            }
	        }

        	if($type == "open") 
        	{
            	$parent[$level-1] = &$current;
            	if(!is_array($current) or (!in_array($tag, array_keys($current)))) 
            	{
                	$current[$tag] = $result;
                	if($attributes_data) $current[$tag. '_attr'] = $attributes_data;
                	$repeated_tag_index[$tag.'_'.$level] = 1;

                	$current = &$current[$tag];

            	} 
            	else 
            	{ 
                	if(isset($current[$tag][0])) 
                	{
                    	$current[$tag][$repeated_tag_index[$tag.'_'.$level]] = $result;
                    	$repeated_tag_index[$tag.'_'.$level]++;
                	} 
                	else 
                	{
                    	$current[$tag] = array($current[$tag],$result);
                   		$repeated_tag_index[$tag.'_'.$level] = 2;
                    
                    	if(isset($current[$tag.'_attr'])) 
                    	{ 
                        	$current[$tag]['0_attr'] = $current[$tag.'_attr'];
                        	unset($current[$tag.'_attr']);
                    	}
                	}
                	$last_item_index = $repeated_tag_index[$tag.'_'.$level]-1;
                	$current = &$current[$tag][$last_item_index];
            	}
        	} 
        	elseif($type == "complete") 
        	{
            	if(!isset($current[$tag])) 
            	{
                	$current[$tag] = $result;
                	$repeated_tag_index[$tag.'_'.$level] = 1;
                	if($priority == 'tag' and $attributes_data) $current[$tag. '_attr'] = $attributes_data;
            	} 
            	else 
            	{ 
                	if(isset($current[$tag][0]) and is_array($current[$tag])) 
                	{
                    	$current[$tag][$repeated_tag_index[$tag.'_'.$level]] = $result;
                    
                    	if($priority == 'tag' and $get_attributes and $attributes_data) 
                    	{
                        	$current[$tag][$repeated_tag_index[$tag.'_'.$level] . '_attr'] = $attributes_data;
                    	}
                    	$repeated_tag_index[$tag.'_'.$level]++;

                	} 
                	else 
                	{ 
                    	$current[$tag] = array($current[$tag],$result);
                    	$repeated_tag_index[$tag.'_'.$level] = 1;
                    	if($priority == 'tag' and $get_attributes) 
                    	{
                        	if(isset($current[$tag.'_attr'])) 
                        	{
                            	$current[$tag]['0_attr'] = $current[$tag.'_attr'];
                            	unset($current[$tag.'_attr']);
                        	}                        
                        	if($attributes_data) 
                        	{
                            	$current[$tag][$repeated_tag_index[$tag.'_'.$level] . '_attr'] = $attributes_data;
                        	}
                    	}
                    	$repeated_tag_index[$tag.'_'.$level]++; 
                	}
            	}
        	} 
        	elseif($type == 'close') 
        	{ 
            	$current = &$parent[$level-1];
        	}
    	}	
    	return($xml_array); 
	}
	
	public function amigar_cadena($cadena)
	{
		$cadena = str_replace("á","a",$cadena);
		$cadena = str_replace("Á","a",$cadena);
		$cadena = str_replace("é","e",$cadena);
		$cadena = str_replace("É","e",$cadena);
		$cadena = str_replace("í","i",$cadena);
		$cadena = str_replace("Í","i",$cadena);
		$cadena = str_replace("ó","o",$cadena);
		$cadena = str_replace("Ó","o",$cadena);
		$cadena = str_replace("ú","u",$cadena);
		$cadena = str_replace("Ú","u",$cadena);
		$cadena = str_replace("ü","u",$cadena);
		$cadena = str_replace("Ñ","N",$cadena);
		$cadena = str_replace("ñ","n",$cadena);
		$cadena = str_replace(",","",$cadena);
		$cadena = str_replace("´","",$cadena);
		$cadena = str_replace(";","",$cadena);
		$cadena = str_replace(":","",$cadena);
		$cadena = str_replace(".","",$cadena);
		$cadena = str_replace("+","mas",$cadena);
		$cadena = str_replace("@","",$cadena);
		$cadena = str_replace("&","y",$cadena);
		$cadena = str_replace("$","",$cadena);
		$cadena = str_replace("%","por-ciento",$cadena);
		$cadena = str_replace("?","",$cadena);
		$cadena = str_replace("¿","",$cadena);
		$cadena = str_replace("¡","",$cadena);
		$cadena = str_replace("!","",$cadena);
		$cadena = str_replace("°","",$cadena);
		$cadena = str_replace("#","",$cadena);
		$cadena = str_replace("(","",$cadena);
		$cadena = str_replace(")","",$cadena);
		$cadena = str_replace("'","",$cadena);
		$cadena = str_replace('"','',$cadena);
		$cadena = str_replace("/","-",$cadena);
		$cadena = str_replace("´","",$cadena);
		$cadena = str_replace(" ","-",$cadena);
		$cadena = str_replace("-un-","-",$cadena);
		$cadena = str_replace("-una-","-",$cadena);
		$cadena = str_replace("-el-","-",$cadena);
		$cadena = str_replace("-la-","-",$cadena);
		$cadena = str_replace("-a-","-",$cadena);
		$cadena = str_replace("-con-","-",$cadena);
		$cadena = str_replace("-y-","-",$cadena);
		$cadena = str_replace("-en-","-",$cadena);
		$cadena = str_replace("-los-","-",$cadena);
		$cadena = str_replace("-las-","-",$cadena);
		$cadena = str_replace("-de-","-",$cadena);
		$cadena = str_replace("-que-","-",$cadena);
		$cadena = str_replace("--","-",$cadena);
		$cadena = str_replace("---","-",$cadena);
		$cadena = str_replace("----","-",$cadena);
		
		return $cadena;
	}
	
	public function xml2assoc($xml) 
	{
    	$assoc = null;
      	while($xml->read())
      	{
        	switch ($xml->nodeType) 
        	{
          		case XMLReader::END_ELEMENT: return $assoc;
          		case XMLReader::ELEMENT:
            		$assoc[$xml->name] = array('value' => $xml->isEmptyElement ? '' : $this->xml2assoc($xml));
            		if($xml->hasAttributes){
              			$el =& $assoc[$xml->name][count($assoc[$xml->name]) - 1];
              			while($xml->moveToNextAttribute()) $el['attributes'][$xml->name] = $xml->value;
            		}
            	break;
          		case XMLReader::TEXT:
          		case XMLReader::CDATA: $assoc .= $xml->value;
        	}
      }
      return $assoc;
	}
	
	public function xmlArray($contents, $get_attributes=1, $priority = 'tag') 
	{
    	if(!$contents) return array();

    	if(!function_exists('xml_parser_create')) 
    	{
        	return array();
    	}

    	$parser = xml_parser_create('');
    	xml_parser_set_option($parser, XML_OPTION_TARGET_ENCODING, "UTF-8");
    	xml_parser_set_option($parser, XML_OPTION_CASE_FOLDING, 0);
    	xml_parser_set_option($parser, XML_OPTION_SKIP_WHITE, 1);
    	xml_parse_into_struct($parser, trim($contents), $xml_values);
    	xml_parser_free($parser);

    	if(!$xml_values) return;

	    //Initializations
    	$xml_array = array();
    	$parents = array();
    	$opened_tags = array();
    	$arr = array();

	    $current = &$xml_array; 

	    //Go through the tags.
    	$repeated_tag_index = array();
    	//Multiple tags with same name will be turned into an array
    	foreach($xml_values as $data) 
    	{
    		//Remove existing values, or there will be trouble
        	unset($attributes,$value);

	        //This command will extract these variables into the foreach scope
        	extract($data);

    	    $result = array();
        	$attributes_data = array();
        
        	if(isset($value)) 	
        	{
            	if($priority == 'tag')
            	{ 
            		$result = $value;
            	}
            	else
            	{
            		//Put the value in a assoc array if we are in the 'Attribute' mode 
            		$result['value'] = $value;
            	} 
        	}

	        //Set the attributes too.
    	    if(isset($attributes) and $get_attributes) 
    	    {
            	foreach($attributes as $attr => $val) 
            	{
                	if($priority == 'tag')
                	{ 
                		$attributes_data[$attr] = $val;
                	}
                	else 
                	{
                		//Set all the attributes in a array called 'attr'
                		$result['attr'][$attr] = $val; 
                	}
            	}
        	}

	        //See tag status and do the needed.
    	    if($type == "open") 
    	    {
            	$parent[$level-1] = &$current;
            	if(!is_array($current) or (!in_array($tag, array_keys($current)))) 
            	{ 
            		//Insert New tag
                	$current[$tag][0] = $result;
                	if($attributes_data) $current[$tag. '_attr'] = $attributes_data;
                	$repeated_tag_index[$tag.'_'.$level] = 1;

                	$current = &$current[$tag][0];

            	}
            	else 
            	{ 
            		//There was another element with the same tag name

                	if(isset($current[$tag][0])) 
                	{
                		//If there is a 0th element it is already an array
                    	$current[$tag][$repeated_tag_index[$tag.'_'.$level]] = $result;
                    	$repeated_tag_index[$tag.'_'.$level]++;
                	} 
                	else 
                	{
                		//This section will make the value an array if multiple tags with the same name appear together
                    	$current[$tag] = array($current[$tag],$result);//This will combine the existing item and the new item together to make an array
                    	$repeated_tag_index[$tag.'_'.$level] = 2;
                    
                    	if(isset($current[$tag.'_attr'])) 
                    	{ 
                    		//The attribute of the last(0th) tag must be moved as well
                        	$current[$tag]['0_attr'] = $current[$tag.'_attr'];
                        	unset($current[$tag.'_attr']);
                    	}

                	}
                	$last_item_index = $repeated_tag_index[$tag.'_'.$level]-1;
                	$current = &$current[$tag][$last_item_index];
            	}

        	} 
        	elseif($type == "complete") 
        	{ 
        		//Tags that ends in 1 line '<tag />'
            	//See if the key is already taken.
            	if(!isset($current[$tag])) 
            	{ 
            		//New Key
                	$current[$tag] = $result;
                	$repeated_tag_index[$tag.'_'.$level] = 1;
                	if($priority == 'tag' and $attributes_data) $current[$tag. '_attr'] = $attributes_data;

            	} 
            	else 
            	{ 
            		//If taken, put all things inside a list(array)
                	if(isset($current[$tag][0]) and is_array($current[$tag])) 
                	{
                		//If it is already an array...

                    	// ...push the new element into that array.
                    	$current[$tag][$repeated_tag_index[$tag.'_'.$level]] = $result;
                    
                    	if($priority == 'tag' and $get_attributes and $attributes_data) {
                        	$current[$tag][$repeated_tag_index[$tag.'_'.$level] . '_attr'] = $attributes_data;
                    	}
                    	$repeated_tag_index[$tag.'_'.$level]++;

                	} 
                	else 
                	{ 
                		//If it is not an array...
                    	$current[$tag] = array($current[$tag],$result); //...Make it an array using using the existing value and the new value
                    	$repeated_tag_index[$tag.'_'.$level] = 1;
                    	if($priority == 'tag' and $get_attributes) 
                    	{
                        	if(isset($current[$tag.'_attr'])) 
                        	{ 
                        		//The attribute of the last(0th) tag must be moved as well
                            
                            	$current[$tag]['0_attr'] = $current[$tag.'_attr'];
                            	unset($current[$tag.'_attr']);
                        	}
                        
                        	if($attributes_data) 
                        	{
                            	$current[$tag][$repeated_tag_index[$tag.'_'.$level] . '_attr'] = $attributes_data;
                        	}
                    	}
                    	$repeated_tag_index[$tag.'_'.$level]++; //0 and 1 index is already taken
                	}
            	}

        	} 
        	elseif($type == 'close') 
        	{ 
        		//End of tag '</tag>'
            	$current = &$parent[$level-1];
        	}
    	}
    
    	return($xml_array); 
	}
	
	public function limpiarurl($frase)
	{
		$url = str_replace("á","a",$frase);
		$url = str_replace("é","e",$url);
		$url = str_replace("í","i",$url);
		$url = str_replace("ó","o",$url);
		$url = str_replace("ú","u",$url);
		$url = str_replace("ü","u",$url);
		$url = str_replace("Ñ","N",$url);
		$url = str_replace("ñ","n",$url);
		$url = str_replace(",","",$url);
		$url = str_replace(";","",$url);
		$url = str_replace(":","",$url);
		$url = str_replace("%","por-ciento",$url);
		$url = str_replace(".","",$url);
		$url = str_replace("@","",$url);
		$url = str_replace("?","",$url);
		$url = str_replace("¿","",$url);
		$url = str_replace("!","",$url);
		$url = str_replace("¡","",$url);
		$url = str_replace("'","",$url);
		$url = str_replace('"','',$url);
		$url = str_replace("(","",$url);
		$url = str_replace(")","",$url);
		$url = str_replace(" ","-",$url);
		
		return $url;
	}
}
?>