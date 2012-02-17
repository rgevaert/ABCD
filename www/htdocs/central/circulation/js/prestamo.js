	Ntpa=0
	Tpre=-1
	ppu=Array()

	ListaMfn=""
	np=0
	nv=0
	document.onkeypress =

 		function (evt) { 			alert("entro")
   			var c = document.layers ? evt.which
           		: document.all ? event.keyCode
           		: evt.keyCode;
   			return true;
 		}
		var nav4 = window.Event ? true : false;




// quick browser tests
	var ns4 = (document.layers) ? true : false;
	var ie4 = (document.all && !document.getElementById) ? true : false;
	var ie5 = (document.all && document.getElementById) ? true : false;
	var ns6 = (!document.all && document.getElementById) ? true : false;

	function show(sw,obj) {
	// show/hide the divisions
		if (sw && (ie4 || ie5) ) document.all[obj].style.visibility = 'visible';
		if (!sw && (ie4 || ie5) ) document.all[obj].style.visibility = 'hidden';
		if (sw && ns4) document.layers[obj].visibility = 'visible';
		if (!sw && ns4) document.layers[obj].visibility = 'hidden';
	}


	function Fecha2Iso(fecha){
		f=fecha.substr(6,4)+fecha.substr(3,2)+fecha.substr(0,2)
		return f
	}

	function Procesar(Opcion){
		ixpre=-1
		ListaMfn=""
		if (np==0){
			if (document.ecta.chkPr.checked) {
				ListaMfn=ppu[0][0]
				ixpre=0
				ixpsel=0
			}
		}else{			for (i=0;i<=np;i++){

				if (document.ecta.chkPr[i].checked){
					ixpsel=i
					ixpre=ixpre+1
					ListaMfn=ppu[i][0]
					break
				}
			}
		}
		if (ixpre==-1){
			alert("Debe seleccionar el préstamo a devolver o renovar")
		}else{
			if (Opcion=="devolver"){
				Datos="prestamo_devolver.php?Opcion="+Opcion+"&usuario="+usuario
				Datos=Datos+"&base=trans&cipar=trans.par&userid=cipres"
				Datos=Datos+"&Mfn="+ListaMfn
				self.location=Datos
			}
			if (Opcion=="renovar"){
				if (ixpre>0) {
					alert("Solo puede seleccionar un préstamo para renovar")
					return
				}
				if (ppu[ixpsel][5]<0){
					alert("El préstamo está vencido. No se puede renovar")
					return
				}
	//			Datos="prestamo_presentar.php?Opcion="+Opcion+"&usuario="+usuario
	//			Datos=Datos+"&base=presta&cipar=cipres.par&userid="+userid
	//			Datos=Datos+"&transacciones="+ListaMfn
	//			self.location=Datos
			}
		}
	}

	function checkdate(input){
		var validformat=/^\d{2}\/\d{2}\/\d{4}$/ //Basic check for format validity
		var returnval=false
		if (!validformat.test(input))
			alert("Formato de fecha inválido")
		else{ //Detailed check for valid date ranges
			var monthfield=input.split("/")[1]
			var dayfield=input.split("/")[0]
			var yearfield=input.split("/")[2]
			var dayobj = new Date(yearfield, monthfield-1, dayfield)
			if ((dayobj.getMonth()+1!=monthfield)||(dayobj.getDate()!=dayfield)||(dayobj.getFullYear()!=yearfield))
				alert("Fecha inválida")
			else
				returnval=true
		}
//		if (returnval==false) input.select()
		return returnval
	}

	Date.prototype.add = function (sInterval, iNum){
		var dTemp = this;
		if (!sInterval || iNum == 0) return dTemp;
		switch (sInterval.toLowerCase()){
			case "ms":
				dTemp.setMilliseconds(dTemp.getMilliseconds() + iNum);
				break;
			case "s":
				dTemp.setSeconds(dTemp.getSeconds() + iNum);
				break;
			case "min":
				dTemp.setMinutes(dTemp.getMinutes() + iNum);
				break;
			case "h":
				dTemp.setHours(dTemp.getHours() + iNum);
				break;
			case "d":
				dTemp.setDate(dTemp.getDate() + iNum);
				break;
			case "m":
				dTemp.setMonth(dTemp.getMonth() + iNum);
				break;
			case "a":
				dTemp.setFullYear(dTemp.getFullYear() + iNum);
				break;
		}
		return dTemp;
	}

	function Vencimiento(){
	//	document.Prestar.fd.disabled=true
		fp=document.Prestar.fp.value
		val=checkdate(fp)

		x = new Date (fp.substr(6),fp.substr(3,2),fp.substr(0,2))
		y = new Date (fechaDia.substr(6),fechaDia.substr(3,2),fechaDia.substr(0,2))

		if (x>y){
//			alert("Fecha de préstamo mayor que la fecha del dia")
			document.Prestar.fp.value=fechaDia
			return false
		}
		if (val==false) return false
		if (document.Prestar.tp.selectedIndex==-1) {
			alert("Debe seleccionar el tipo de Préstamo")
			return false
		}

		fd=document.Prestar.fd.value
		if (fd!=""){
			val=checkdate(fd)
			if (val==false) return false
		}
		Plazo=document.Prestar.tp.options(document.Prestar.tp.selectedIndex).text

		i=Plazo.indexOf("[")
		j=Plazo.indexOf("]")
		Plazo=Plazo.substr(i+1,j)
		i=Plazo.indexOf(" ")
		Lapso=Plazo.substr(0,i)

		Unidad=Plazo.substr(i+1,1)
		var monthfield=fp.split("/")[1]-1
		var dayfield=fp.split("/")[0]
		var yearfield=fp.split("/")[2]
		var Fprestamo = new Date(yearfield, monthfield, dayfield)
		Fechap=Fprestamo
		Devol=Fechap.add(Unidad,parseInt(Lapso)-1); //
		Year=Devol.getYear()
		Mes=Devol.getMonth()+1

		if (Number(Mes)<10) Mes="0"+Mes
		Dia=Devol.getDate()
		if (Number(Dia)<10) Dia="0"+Dia
		fd=Dia+"/"+Mes+"/"+Year
		document.Prestar.fd.value=fd
		var Fprestamo = new Date(yearfield, monthfield, dayfield)
		var difference = Devol-Fprestamo;   //unit is milliseconds
		Datedif = Math.round(difference/1000/60/60/24); //now unit is days
		df=0
		for (i=0;i<Datedif-1;i++){
			var Fprestamo = new Date(yearfield, monthfield, dayfield)
			xfecha=Fprestamo.add("d",i); //
			Lmes=xfecha.getMonth()+1
			Ldia=xfecha.getDate()
			if (Feriados[Lmes,Ldia]=="F")
				df=df+1
			else{
				ndia=xfecha.getDay()+1
				if (Ntb.indexOf(String(ndia))!=-1) df=df+1
			}
		}

		Devol=Devol.add("d",df)
		Year=Devol.getYear()
		Mes=Devol.getMonth()+1
		if (Number(Mes)<10) Mes="0"+Mes
		Dia=Devol.getDate()
		if (Number(Dia)<10) Dia="0"+Dia
		fd=Dia+"/"+Mes+"/"+Year
		document.Prestar.fd.value=fd
	}

function EnviarPrestamo(){
		a=Vencimiento()
		if (a==false){
			alert("Fecha de préstamo o devolución inválida")
			return
		}
		if (document.Prestar.inven.value=="" && document.Prestar.signa.value=="" || document.Prestar.fp.value=="" || document.Prestar.fd.value==""){
			alert("Debe completar los datos del préstamo")
			return
		}
		ep=""
		if (document.Prestar.ejemplar.length>0){
			for (i=0;i<document.Prestar.ejemplar.length;i++){
				if (document.forms[0].ejemplar[i].checked){
					ep= document.forms[0].ejemplar[i].value
					break
				}
			}
		}else{
			if (document.forms[0].ejemplar.checked){
				ep= document.forms[0].ejemplar.value
			}
		}

		if (ep==""){
			alert("Debe seleccionar el ejemplar objeto del préstamo")
			return
		}
		e=ep.split('|')
		document.Prestar.centro.value=e[0]
		document.Prestar.inventario.value=e[1]
		document.Prestar.ejemp.value=e[2]
		document.Prestar.fpreiso.value=Fecha2Iso(document.Prestar.fp.value)
		document.Prestar.fdeviso.value=Fecha2Iso(document.Prestar.fd.value)
		document.Prestar.usuario.value=usuario
		document.Prestar.submit()
	}

	function CancelarPrestamo(){
		loc="prestamo_presentar.php?Opcion=prestamousuario&base=user&cipar=cipres.par&usuario="+usuario+"&userid="+userid+"&Expresion="+userid
		self.location.href=loc
	}

	function BuscarLibro(Opcion){
		inven_01=document.Prestar.inven.value
		signa=document.Prestar.signa.value
		base="marc"
		cipar="cipres.par"
        expresion=usuario
		switch (Opcion){
			case "C":	//buscar en el catálogo
         		if (document.Prestar.libro.value=="") 	{
					msgwin=window.open("prestamo_buscar.php?desde=prestamo&Opcion=formab&base="+base+"&cipar="+cipar,"Buscar","status=yes,resizable=yes,toolbar=no,menu=yes,scrollbars=yes,width=630,height=400,top=0,left=350")
       				msgwin.focus()
				}
				break
			case "S":	//Buscar por signatura
				if (document.Prestar.signa.value==""){
					alert("Debe suministrar la signatura topográfica")
					break
				}
				expresion="!Z"+document.Prestar.signa.value
				msgwin=window.open("buscar.php?desde=prestamo&Opcion=buscar&prestamo=S&Formato=p&prologo=pp&base="+base+"&cipar="+cipar+"&Expresion="+expresion,"Buscar","status=yes,resizable=yes,toolbar=no,menu=yes,scrollbars=yes,width=630,height=400,top=0,left=350")
       			msgwin.focus()
				break
			case "I":	//Buscar por número de inventario

			//	msgwin=window.open(url,"")
				break
		}
	}

	function BuscarCatalogo(){
		ix=top.menu.Prestamo.bd.selectedIndex
		base=top.menu.Prestamo.bd.options[ix].value
		if (base==""){
			alert("Debe seleccionar la base de datos bibliográfica")
			return
		}
		loc="buscar.php?Opcion=formab&prestamo=S&Formato=p&prologo=pp&base="+base+"&cipar="+base+".par"
		msgwin=window.open(loc,"Catalogo","resizable=yes,scrollbars, status=yes, toolbar=no,menu=no,width=650,height=400")
		msgwin.focus()
	}
