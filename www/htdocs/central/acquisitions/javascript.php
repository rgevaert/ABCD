<script src=../dataentry/js/lr_trim.js></script>
<!-- calendar stylesheet -->
  <link rel="stylesheet" type="text/css" media="all" href="../dataentry/calendar/calendar-win2k-cold-1.css" title="win2k-cold-1" />
  <!-- main calendar program -->
  <script type="text/javascript" src="../dataentry/calendar/calendar.js"></script>
  <!-- language for the calendar -->
  <script type="text/javascript" src="../dataentry/calendar/lang/calendar-en.js"></script>
  <!-- the following script defines the Calendar.setup helper function, which makes
       adding a calendar a matter of 1 or 2 lines of code. -->
  <script type="text/javascript" src="../dataentry/calendar/calendar-setup.js"></script>

<script>



function Show(Db,Key){	msgwin=window.open("../dataentry/show.php?base="+Db+"&Expresion="+Key,"","width=600, height=600, resizable, scrollbars")
	msgwin.focus}
function DateToIso(From,To){
	d=From.split('/')
	<?php echo "dateformat=\"$config_date_format\"\n" ?>
	if (dateformat="DD/MM/YY"){
		iso=d[2]+d[1]+d[0]
	}else{
		iso=d[2]+d[0]+d[1]
	}
	To.value=iso
}

function ChangeSeq(ix,prefix){

	msgwin=window.open("","CHANGE","width=400, height=200, scrollbars=yes")
	msgwin.document.writeln("<b><?php echo $msgstr["database"]."</b>: ". $arrHttp["base"]?>")
	msgwin.document.writeln("<form name=cn method=post action=../dataentry/changeseq.php>")
	msgwin.document.writeln("<input type=hidden name=base value=<?php echo $arrHttp["base"]?>>")
	msgwin.document.writeln("<input type=hidden name=prefix value="+prefix+">")
	msgwin.document.writeln("<input type=hidden name=tag value="+ix+">")
	msgwin.document.writeln("<?php echo $msgstr['new'].' '.$msgstr['cn']?> <input type=text size=20 name=cn value=''>")
	msgwin.document.writeln("<input type=submit value=<?php echo $msgstr['entrar']?>>")
	msgwin.document.writeln("</form>")
	msgwin.document.close()
	msgwin.focus()
}

function EnviarForma(){
	enviar=Validar()
	if (enviar=="N") return
	Msg=""
	Separador=""
	var Repetibles=new Array();
    FormarValorCapturado()
	if (ValorCapturado+Val_text==""){
			alert("<?php echo $msgstr['specvalue']?>")
			return
	}
	document.forma1.submit()
}
function AbrirIndiceAlfabetico(xI,Prefijo,Subc,Separa,db,cipar,tag,postings,Repetible,Formato){
	Ctrl_activo=xI
	if (db=="")  db="<?php echo $arrHttp["base"]?>"
	lang="<?php echo $_SESSION["lang"]?>"
    document.forma1.Indice.value=xI
    Separa="&delimitador="+Separa
    Prefijo=Separa+"&tagfst="+tag+"&prefijo="+Prefijo
    ancho=200
    myleft=screen.width-480
	url_indice="../dataentry/capturaclaves.php?opcion=autoridades&base="+db+"&cipar="+cipar+"&Tag="+tag+Prefijo+"&postings="+postings+"&lang="+lang+"&repetible="+Repetible+"&Formato="+Formato
	msgwin=window.open(url_indice,"Indice","width=480, height=500,  scrollbars, status, resizable location=no, left="+myleft)
	msgwin.focus()
	return
}

function FormarValorCapturado(){
	j=document.forma1.elements.length-1
	ValorCapturado=""
	VC=new Array()
	Val_text=""
	for (i=0;i<=j;i++){
		campo=document.forma1.elements[i]
		nombre=campo.name
		id=campo.id
		if (campo.value!=""){
			if (nombre.substr(0,3)=="tag"){
				if (campo.type=="text" || campo.type=="textarea"){
					Val_text+=campo.value
				}else{
					switch (campo.type){
						case "checkbox":
							if (campo.checked){
								if (VC[nombre]=="undefined"){
									VC[nombre]="S";
									ValorCapturado= nombre+"_"+campo.value+"\n";
								}else{
									ValorCapturado=ValorCapturado+"\n"+nombre+"_"+campo.value
								}
								campo.checked=false
							}
							break
						case "select":
						case "select-one":
							break;
						case "select-multiple":
							Ctrl=eval("document.forma1."+nombre)
							for (ixsel=0;ixsel<Ctrl.length;ixsel++)
								if (Ctrl.options[ixsel].selected){
									if (VC[nombre]=="undefined"){
										VC[nombre]="S";
										ValorCapturado=nombre+"_" +Ctrl.options[ixsel].value;
									}else{
										ValorCapturado=ValorCapturado+"\n"+nombre+"_"+Ctrl.options[ixsel].value;
								}
							}
							break
					}
				}
			}
			if (campo.id=="O" && campo.value==""){
				if(Msg=="") {
					Separador="<?php echo $msgstr['specvalue']?>: "
				}else{
					Separador=", "
				}
				Msg=Msg+Separador+NomList[i]
				enviar=false
			}
		}else{
			if (nombre.substr(0,3)=="eti") 	campo.value=""
		}
	}
	document.forma1.check_select.value=ValorCapturado
}

</script>
