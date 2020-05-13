<?php
class Valida_model extends CI_Model
{
	//formato de fecha dd/mm/aaaa
	public function validaFecha($date)
	{
		$fecha = explode("/",$date);
		$day = $fecha[0];
		$mes = $fecha[1];
		$anio = $fecha[2];
		if (strlen($date) == 10 and count($fecha) == 3)
		{
			if ($day <= 31)
			{
				if ($mes == "01" or $mes == "03" or $mes == "05" or $mes == "07" or $mes == "08" or $mes == "10" or $mes == "12")
				{
					return true;
				}
				else
				{
					if (($mes == "04" or $mes == "06" or $mes == "09" or $mes == "11") and $day <= "30")
					{
						return true;
					}
					else
					{
						if ($mes == "02")
						{
							if (fmod($anio,4) == 0 and $day <= 29)
								return true;
							else
								if ($day <= 28)
									return true;
								else
									return false;
						}
						else
							return false;
					}
				}
			}
			else
				return false;
		}
		else
			return false;
	}
	
	//Formato de fecha aaaa/mm/dd
	public function validaFechaInversa($date)
	{
		$fecha = explode("-",$date);
		if (strlen($date) == 10 and count($fecha) == 3)
		{
			$day = $fecha[2];
			$mes = $fecha[1];
			$anio = $fecha[0];
			if ($day <= 31)
			{
				if ($mes == "01" or $mes == "03" or $mes == "05" or $mes == "07" or $mes == "08" or $mes == "10" or $mes == "12")
				{
					return true;
				}
				else
				{
					if (($mes == "04" or $mes == "06" or $mes == "09" or $mes == "11") and $day <= "30")
					{
						return true;
					}
					else
					{
						if ($mes == "02")
						{
							if (fmod($anio,4) == 0 and $day <= 29)
								return true;
							else
								if ($day <= 28)
									return true;
								else
									return false;
						}
						else
							return false;
					}
				}
			}
			else
				return false;
		}
		else
			return false;
	}
	
	/*Transforma la fecha de dd-mm-aaa a aaaa-mm-dd*/
	public function transformaFecha($fecha)
	{
		$fecha = explode("/",$fecha);
		if (count($fecha)>0)
		{
			$dia = $fecha[0];
			$mes = $fecha[1];
			$anio = $fecha[2];
			if (strlen($dia)<2) $dia= "0".$dia;
			if (strlen($mes)<2) $mes= "0".$mes;
			//return $anio."-".$mes."-".$dia;	
			return $anio."/".$mes."/".$dia;
		}
		else
		{
			$fecha = explode("-",$fecha);
			$dia = $fecha[0];
			$mes = $fecha[1];
			$anio = $fecha[2];
			if (strlen($dia)<2) $dia= "0".$dia;
			if (strlen($mes)<2) $mes= "0".$mes;
			return $anio."-".$mes."-".$dia;
		}
	}
	
	/*Transforma la fecha de aaaa-mm-dd a dd-mm-aaaa*/
	public function transFecha($fecha)
	{
		$fecha = explode("-",$fecha);
		if (count($fecha)>0)
		{
			$dia = $fecha[2];
			$mes = $fecha[1];
			$anio = $fecha[0];
			return $dia."-".$mes."-".$anio;	
		}
		else
			return false;
	}
	
	public function validaHora($hora)
	{
		if (strlen($hora)==8)
		{
			$hora = substr($hora,0,5);
		}
		
		if (strlen($hora) == 5 or strlen($hora) == 4)
		{
			$horas = explode(":",$hora);
			$hh = $horas[0];
			$mm = $horas[1];
			if (strlen($mm) == 2)
			{
				if ($hh >= 0 and $hh <24)
				{
					if ($mm >= 0 and $mm < 60)
					{
						return true;
					}
					else
						return false;
				}
				else
					return false;
			}
			else
				return false;
		}
		else
			return false;
	}
}
?>