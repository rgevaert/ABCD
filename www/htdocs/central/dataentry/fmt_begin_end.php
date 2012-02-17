<?php

$pref=strtolower($arrHttp["wk_tipom_1"]);
if (isset($arrHttp["wk_tipom_2"]) and $arrHttp["wk_tipom_2"]!=""){	$pref.="_".strtolower($arrHttp["wk_tipom_2"]);}
$pref.="_".$arrHttp["base"];
$archivo=$db_path.$arrHttp["base"]."/def/".$_SESSION["lang"]."/$pref".$ext;
if (!file_exists($archivo)) $archivo=$db_path.$arrHttp["base"]."/def/".$lang_db."/$pref".$ext;
if (!file_exists($archivo)) $archivo=$db_path.$arrHttp["base"]."/def/".$_SESSION["lang"]."/".$arrHttp["base"].$ext;
if (!file_exists($archivo)) $archivo=$db_path.$arrHttp["base"]."/def/".$lang_db."/".$arrHttp["base"].$ext;
if (file_exists($archivo)){
	$fp = file($archivo);
	$fp_str=implode('$%|%$',$fp);
	$fp=explode('###',$fp_str);
	$ix_fatal=-1;
	foreach($fp as $value){		$value=str_replace('$%|%$',' ',$value);
		$value=trim($value);
		if ($value!="") {
			$ix=strpos($value,':');
			if ($ix===false){
			}else{
				$tag_val=substr($value,0,$ix);
				$value=substr($value,$ix+1);
				$v=explode('$$|$$',$value);
				if (substr(trim($v[0]),0,1)=="@"){
					$pft_file=$db_path.$arrHttp["base"]."/pfts/".$_SESSION["lang"]."/".trim(substr($v[0],1));
					if (!file_exists($pft_file)) $pft_file=$db_path.$arrHttp["base"]."/pfts/".$lang_db."/".trim(substr($v[0],1));
					$v[0]="@".$pft_file;
					$rec_validation.= "'$tag_val: ',".$v[0]." ,, mpl '$$$$',";
				}else{
					$rec_validation.= "'$tag_val: ',".$v[0].",mpl '$$$$',";
				}
			}
		}
	}

	$formato=urlencode($rec_validation);
	$query="";
	switch ($ext){		case ".beg":
			switch ($arrHttp["Mfn"]){				case "New":
					$ValorCapturado=$ValorCapturado.urlencode("\n33330");
					$query = "&base=".$arrHttp["base"] ."&cipar=$db_path"."par/".$arrHttp["base"].".par&Pft=".$formato."&ValorCapturado=".$ValorCapturado."&";
					$IsisScript=$xWxis."z3950_cnv.xis";
					break;
				default:
					$ValorCapturado.="\n3333".$arrHttp["Mfn"];
					$query = "&base=".$arrHttp["base"] ."&cipar=$db_path"."par/".$arrHttp["base"].".par&Pft=".$formato."&ValorCapturado=".$ValorCapturado;
					$IsisScript=$xWxis."z3950_cnv.xis";			}
			break;
		case ".end":
			$end_code="S";   // CREATE A DUMMY RECORD AND APLY THE VALIDATION FORMAT
			ActualizarRegistro();
			unset($end_code);
			if ($arrHttp["Mfn"]=="New")
			   	$ValorCapturado.="\n33330";
			else
				$ValorCapturado.="\n3333".$arrHttp["Mfn"];
			$query = "&base=".$arrHttp["base"] ."&cipar=$db_path"."par/".$arrHttp["base"].".par&Pft=".$formato."&ValorCapturado=".$ValorCapturado;
			$IsisScript=$xWxis."z3950_cnv.xis";
			break;
	}
	if ($query!=""){
		include("../common/wxis_llamar.php");
		$recval_pft="";
		$res=implode("\n",$contenido);
		$linea=explode('$$$$',$res);
		$ix_fatal=-1;
		foreach ($linea as $v_value){
			if ($v_value!=""){
				$v_ix=strpos($v_value,':');
				$v_tag=trim(substr($v_value,0,$v_ix));
				$v_res=substr($v_value,$v_ix+2);
				if (trim($v_res)!=""){					if ($ext==".beg"){
						if (!isset($valortag[$v_tag]) or $valortag[$v_tag]==""){
							$valortag[$v_tag]=$v_res;
                        }
					}
					if ($ext==".end"){						if (!isset($variables["tag".$v_tag]) or trim($variables["tag".$v_tag])=="")
							$variables["tag".$v_tag]=$v_res;					}
				}
		    }
		}

	}
}
?>