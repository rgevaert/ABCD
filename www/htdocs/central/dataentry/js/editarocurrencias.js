

  var valoresCampo=new Array(200)    /* para colocar las ocurrencias del campo */
  var SubCampos=new Array(23)        /* para colocar el desglose de los subcampos del campo */
  var TagCampo=window.opener.document.forma1.TagActivo.value
  var lista_sc=Array()
  document.forma1.tagcampo.value=window.opener.document.forma1.valor.value  /* Fdt del campo */
//  alert(document.forma1.tagcampo.value)
  document.forma1.occur.value=window.opener.document.forma1.occur.value  /* no. de ocurrencias del campo */
  document.forma1.ep.value=window.opener.document.forma1.ep.value    /* indice en proceso */
  document.forma1.NoVar.value=window.opener.document.forma1.NoVar.value  /*indice al input type=text en proceso*/
  Contenido=window.opener.document.forma1.conte.value   /*Contenido del campo*/
  SubC=window.opener.document.forma1.SubC.value   /*Desglose de los subcampos del campo (valores tomados del archivo FDT*/
  Repetible=window.opener.document.forma1.Repetible.value
  Formato=window.opener.document.forma1.Formato_ex.value   //Formato para extracci�n de las listas de autoridades
// se elimina la primera l�nea que que ella contiene la descripci�n total del campo

	var tag=""
	var X_tag=""
  	X_tag="document.forma1."+TagCampo

	document.forma1.base.value=window.opener.document.forma2.base.value
	document.forma1.cipar.value=window.opener.document.forma2.cipar.value
    base=document.forma1.base.value
  	cipar=document.forma1.cipar.value
	ixpos=SubC.indexOf("\n")
	SubC=SubC.substr(ixpos+1)
  Valores=window.opener.document.forma1.conte.value
  Occ=window.opener.document.forma1.occur.value
  if (Occ==0) {Occ=1}
  i=window.opener.document.forma1.ep.value

   SubCampos = SubC.split("\n")
   nSC=SubCampos.length
/* se obtiene el arreglo de las ocurrencias del campo */

   valoresCampo = Contenido.split("\n")

/* Se crea el Select para las ocurrencias del campo */
   Titulo=window.opener.NombreC
 //  document.writeln(Titulo)

   Tx=Titulo.split('|')
   Tx_Rep=Tx[4] // para ver si el campo es repetible
   document.write("<table width=700 cellpadding=0 cellspacing=0>")
   document.write("<td class=mmed0 colspan=2><a href=javascript:Ayuda("+Tx[1]+")><img src=img/question.gif border=0></a> <b><font size=2>"+Tx[2]+"("+Tx[1]+")</b></td> ")
   Occ=valoresCampo.length

   if (Occ>5){kOc=5} else {kOc=Occ}
   document.write('<tr><td valign=top width=80 >')
   if (Tx_Rep==1){
   		if (Repetible=="R") document.write("<a href=javascript:AgregarOcurrencia()><img src=img/add.gif border=0 Alt='agregar ocurrencia'></a> &nbsp;")
   		document.write("<a href=javascript:EliminarOcurrencia()><img src=img/delete_occ.gif border=0 Alt='eliminar ocurrencia seleccionada'></a>")
  	 	if (Repetible=="R") document.write("<a href=javascript:SubirOcurrencia('lista')><img src=img/up.gif border=0></a>")
   		if (Repetible=="R") document.write("<a href=javascript:BajarOcurrencia('lista')><img src=img/down.gif border=0></a> &nbsp;")
   }
   document.writeln("</td>")
   document.write("<td><select name=lista style='width:550px;background-color=#F2F6F8;'  size="+kOc+" onClick=\"javascript:TerminoSeleccionado();\">")
   for (j=0; j<=Occ-1; j++) {
       document.write("   <option  ")
       if (j==0) {document.write(" selected ")}
		cc=""
		if (Trim(valoresCampo[j])!="")
       document.write(" value=\""+valoresCampo[j]+"\">"+valoresCampo[j])
   }
   document.writeln('</select></td></table><br>')

/* Tabla para colocar los subcampos de la ocurrencia */





// Esta linea se agrega para obligar que el objeto "tag" siempre sea un arreglo a�n cuando sea un solo subcampo
	document.writeln("<input type=hidden name=tag"+tag+" value=''>")

function returnObjById( id )
{
    if (document.getElementById)
        var returnVar = document.getElementById(id);
    else if (document.all)
        var returnVar = document.all[id];
    else if (document.layers)
        var returnVar = document.layers[id];
    return returnVar;
}

function Redraw(xsalida,newSc,add_name){
// si no hay indicadores pero el campo los debe tener se abren las dos casillas en blanco
	inctr=""
	if (xsalida==""){		for (i=0;i<Tx[5].length;i++){
			sc=Tx[5].substr(i,1)
			xsalida+="^"+sc
		}
	}else{
		if (Tx[5].substr(0,1)==1){
			ixpos=xsalida.indexOf("^")
			if (ixpos!=2)
				indicadores="  "

			else
				indicadores=xsalida.substr(0,ixpos)			xsalida=xsalida.substr(ixpos)
			xsalida="^1"+indicadores.substr(0,1)+"^2"+indicadores.substr(1,1)+xsalida
		}
	}
	campos=xsalida.split('^')
	M=-1
	strsubc=""
	html="<table border=0 cellspacing=1 class=\"listTable\">"
	inicio=""
	Desc_sc=Array()
// se obtiene el nombre de los subcampos para el select del ADD
	for (i=0;i<Tx[5].length;i++){
		xd=SubCampos[i].split('|')
		if ((xd[5]==1 || xd[5]==2) && i<2){
            ix="I"+xd[5]          //ADD THE LETTER I TO THE INDICATOR CODE FOR NOT CONFUSING WITH SUBFIELD 2
            Desc_sc[ix]=xd[2]		}else{			Desc_sc[xd[5]]=xd[2]
		}
	}
    sc_ant=""
    len=campos.length
   	for (i=1;i<len;i++){        new_subc=campos[i].substr(0,1)
        pick=""
        for (j=0;j<Tx[5].length;j++){        	sc=SubCampos[j].split('|')
        	tipoe=sc[7]
        	Ind=""
        	if (i<3 && (sc[5]==1 || sc[5]==2))
        		Ind="I"
        	if (sc[5]==new_subc){
        		ind_pick=Ind+sc[5]
        		pick=PickList[ind_pick]
        		break        	}        }
        valor=""

        if (campos[i].length>1) valor=campos[i].substr(1)
        M++
		html+="<tr onmouseover=\"this.className = 'rowOver';\" onmouseout=\"this.className = '';\">\n"
        html+="<td nowrap valign=top >"+new_subc
        C_Sc=new_subc
        strsubc+=new_subc
       	lista_sc[M]="t"+C_Sc+"_"+M
        ixsc=Tx[5].indexOf(new_subc)

        if (i>2 || C_Sc!=1 && C_Sc!=2){
        	if (C_Sc!="-"){
        		if (sc_ant!="-"){
        			html+="<a href=javascript:SubirSubc('t"+C_Sc+"_"+M+"')><img src=img/up.gif border=0></a>"
                   	html+="<a href=javascript:BajarSubc('t"+C_Sc+"_"+M+"')><img src=img/down.gif border=0></a>"
    			}else{
    				sc_ant=""
    				html+="<a href=javascript:BajarSubc('t"+C_Sc+"_"+M+"')><img src=img/down.gif border=0></a>"
    			}
    			ixsc=Tx[5].indexOf(new_subc,2)
    		}else{    			sc_ant="-"    		}
      	}
        if (sc[12]!=""){
	   		prefijo=sc[12]
           	iSc=sc[5]
           	Formato=sc[13]
           	db_link=sc[11]
           	if (db_link=="") db_link=base
           	cipar_link=db_link+".par"
	   		link=" &nbsp;<a href='javascript:AbrirIndiceAlfabetico(\"t"+C_Sc+"_"+M+"\",\""+prefijo+"\",\""+iSc+"\",\"S\""+",\""+db_link+"\""+",\""+cipar_link+"\""+",\""+TagCampo+"\""+",\""+Formato+"\")'><img src=img/setsearch.gif border=0 align=center></a><font color=red>"
		}else{                                                                                                                                                                                  		link=""
		}
		if (link!="") html+="<font color=darkblue>"+link+"</a>"
	   	html+="</td>"
	   	link=""
	   	//GET THE SUBFIELD NAME
       	ixnamec=Tx[5].indexOf(C_Sc)
       	if ((C_Sc==1 || C_Sc==2) && i<3){       		NombreSc=Desc_sc["I"+C_Sc]        //GET THE NAME OF THE INDICATOR       	}else{
       		NombreSc=Desc_sc[C_Sc]            //GET THE NAME OF THE SUBFIELD
       	}        html+="<td  valign=top>"+NombreSc+"</a></td>"
       	html+="<td class=td nowrap>"
       	xsize="70"
       	if (i<3 && (C_Sc==1 || C_Sc==2)){       		xsize="1 maxlength=1"       	}
       	if (pick!=""){
       		html+=" <select name="+TagCampo+" id=t"+C_Sc+"_"+M+"><option value=' '> </option>\n"
       		opt=pick.split('$$$$')
       		selected=""
       		for (var ixopt in opt){       			if (Trim(opt[ixopt])!=""){	       			o=opt[ixopt].split('|')
	       			if (Trim(valor)==Trim(o[0]))  selected= " selected"
	       			if (o[5]!="-")
	             		html+="<option value='"+o[0]+"' "+selected+">"+o[1]+"</option>\n"
	             	selected=""
	           	}
			}
            html+="</select>\n";       	}else{
       		html+="<input type=text class=SubC  size="+xsize+" name="+TagCampo+" id=t"+C_Sc+"_"+M+" value='"+valor+"' >"       	}

       	if (i>2 || C_Sc!=1 && C_Sc!=2){	    	html+="<select name=agregar id=at"+C_Sc+"_"+M+" onChange=AgregarSubcampo(this,"+j+") style=width:100px>"
  			html+="<option value=''>add"
    		for (ia=0;ia<Tx[5].length;ia++){
     			sca=Tx[5].substr(ia,1)
       			if (ia>1 || (Tx[5].substr(0,1)!=1 && Tx[5].substr(0,1)!=2))
       				if (sca!="-")
           				html+="<option value="+Tx[5].substr(ia,1)+">"+Tx[5].substr(ia,1)+" "+Desc_sc[sca]
        	}
        	if (C_Sc!="-"){
	        	html+="</select> &nbsp;<a href=javascript:DeleteSubfield('t"+C_Sc+"_"+M+"')><img src=../dataentry/img/delete_occ.gif border=0 ></a>"
        	}
        }
	}

	html+="</table>"
	elem = document.getElementById("asubc");

	elem.innerHTML = html;
}

function BajarOcurrencia(id){
	ix=document.forma1.lista.selectedIndex
	if (ix==-1 || ix>=document.forma1.lista.options.length) return	ocurren=document.forma1.lista.options[ix+1].value
	txt_ocurren=document.forma1.lista.options[ix+1].text
	document.forma1.lista.options[ix+1].value=document.forma1.lista.options[ix].value
	document.forma1.lista.options[ix+1].text=document.forma1.lista.options[ix].text
	document.forma1.lista.options[ix].value=ocurren
	document.forma1.lista.options[ix].text=txt_ocurren
	document.forma1.lista.selectedIndex=ix+1
}

function SubirOcurrencia(id){
	ix=document.forma1.lista.selectedIndex
	if (ix==-1 || ix==0) return
	ocurren=document.forma1.lista.options[ix-1].value
	txt_ocurren=document.forma1.lista.options[ix-1].text
	document.forma1.lista.options[ix-1].value=document.forma1.lista.options[ix].value
	document.forma1.lista.options[ix-1].text=document.forma1.lista.options[ix].text
	document.forma1.lista.options[ix].value=ocurren
	document.forma1.lista.options[ix].text=txt_ocurren
	document.forma1.lista.selectedIndex=ix-1
}


function BajarSubc(Id){	valores=Array()
	for (i=0;i<lista_sc.length;i++){
		valores[i]=returnObjById(lista_sc[i]).value
		if (lista_sc[i]==Id){
			xpos=i
		}
	}
	if (xpos==lista_sc.length-1) return
	areemplazar=returnObjById(lista_sc[xpos+1]).value
	valores[xpos+1]=valores[xpos]
	valores[xpos]=areemplazar
	areemplazar=lista_sc[xpos+1]
	lista_sc[xpos+1]=lista_sc[xpos]
	lista_sc[xpos]=areemplazar
	xsalida=""

	for (i=0;i<lista_sc.length;i++){		subc=lista_sc[i].substr(1,1)
		if ((i==0 || i==1 ) & (subc==1 || subc==2)){			xsalida+=valores[i]		}else{
			xsalida+="^"+subc+valores[i]
		}
	}
	Redraw(xsalida,"","")
}

function SubirSubc(Id){
	valores=Array()
	for (i=0;i<lista_sc.length;i++){
		ctrl= returnObjById(lista_sc[i])
		valores[i]=returnObjById(lista_sc[i]).value
		if (lista_sc[i]==Id){
			xpos=i
		}
	}
	if (xpos==0) return
 	xxss=lista_sc[xpos-1]
 	if (xxss.substr(1,1)==2 || xxss.substr(1,1)==1 ) return
	areemplazar=returnObjById(lista_sc[xpos-1]).value
	valores[xpos-1]=valores[xpos]
	valores[xpos]=areemplazar
	areemplazar=lista_sc[xpos-1]
	lista_sc[xpos-1]=lista_sc[xpos]
	lista_sc[xpos]=areemplazar
	xsalida=""
	for (i=0;i<lista_sc.length;i++){
		subc=lista_sc[i].substr(1,1)
		if ((i==0 || i==1 ) & (subc==1 || subc==2)){
			xsalida+=valores[i]
		}else{
			xsalida+="^"+subc+valores[i]
		}
	}
	Redraw(xsalida,"","")
}

function AgregarSubcampo(subc,j){
	add_name=""
	ins=subc.id
//verify if the actual subfield is filled. If not, no subfield is added
	ins=ins.substr(1)
	ctrl= returnObjById(ins)
	if (Trim(ctrl.value)=="") {		subc.selectedIndex=0
  		ins=-1
  		return
	}
	salida=""
	ixsc=-1
    for (i=0;i<document.forma1.elements.length;i++){
      	tipo=document.forma1.elements[i].type
      	nombre=""
       	switch (tipo){
        	case "text":
        		ixsc++
        		nombre=document.forma1.elements[i].id
        		subc_act=nombre.substr(1,1)
        		valor=" "
        		valor=document.forma1.elements[i].value
        		if (ixsc>1){
        			salida+="^"+subc_act+valor
        		}else{
        			if ((ixsc==0 || ixsc==1) && (subc_act=="1" || subc_act=="2")){
        				salida+=valor
                    }else(
                    	salida+="^"+subc_act+valor
                    )
        		}
            	break
	      	case "select-one":
	       		nombre=document.forma1.elements[i].id
	       		if (nombre=="" || nombre.substr(0,1)!="t") break
	       		subc_act=nombre.substr(1,1)
	       		ixsc++
	       		valor=document.forma1.elements[i].options[document.forma1.elements[i].selectedIndex].value
	       		if (ixsc<2 && (subc_act==1 || subc_act==2)){
	       			if (Trim(valor)=="") valor=" "
	       				salida+=valor
	       		}else{
	       			if (Trim(valor)!=""){
						if (valor.indexOf('^')==-1)
							salida+="^"+subc_act+valor
						else
							salida+=valor
					}
	       		}
     			break
        	}
       		if (nombre==ins){            // se determina si el nuevo subcampo se va a insertar aqu�
       			new_subc=Trim(subc.options[subc.selectedIndex].value)
       			add_name=subc.options[subc.selectedIndex].text
       			salida+="^"+new_subc
       		}
       	}
       	Redraw(salida,new_subc,add_name)
        return
	}

function DeleteSubfield(subc){
	salida=""
	ixsc=-1
    for (i=0;i<document.forma1.elements.length;i++){
    	tipo=document.forma1.elements[i].type
   		nombre=""
       	switch (tipo){
        	case "text":
        		ixsc++
        		nombre=document.forma1.elements[i].id
        		subc_act=nombre.substr(1,1)
        		valor=" "
        		valor=document.forma1.elements[i].value
        		if (nombre!=subc){
	    			if (ixsc>1){
	    				salida+="^"+subc_act+valor
	    			}else{
	    				if ((ixsc==0 || ixsc==1) && (subc_act=="1" || subc_act=="2")){
	    					salida+=valor
	               		}else{	               			salida+="^"+subc_act+valor	               		}
	    			}
    			}
            	break
	      	case "select-one":
	      		case "select-one":
	       		nombre=document.forma1.elements[i].id
	       		if (nombre=="" || nombre.substr(0,1)!="t") break
	       		subc_act=nombre.substr(1,1)
	       		ixsc++
	       		valor=document.forma1.elements[i].options[document.forma1.elements[i].selectedIndex].value
	       		if (ixsc<2 && (subc_act==1 || subc_act==2)){
	       			if (Trim(valor)=="") valor=" "
	       				salida+=valor
	       		}else{
	       			if (Trim(valor)!=""){
						if (valor.indexOf('^')==-1)
							salida+="^"+subc_act+valor
						else
							salida+=valor
					}
	       		}
     			break
        }

    }
    Redraw(salida,"","")
    return
}


