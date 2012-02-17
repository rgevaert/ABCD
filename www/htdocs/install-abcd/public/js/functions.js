/**
* Script validation form
*
* @author:			Ana Katia Camilo <katia.camilo@bireme.org>
* @author:			Bruno Neofiti <bruno.neofiti@bireme.org>
* @author:			Domingos Teruel <domingos.teruel@bireme.org>
* @since        	2008-09-09
* @copyright:      (c) 2008 BIREME/PAHO/WHO - PFI
*
* The contents of this file are subject to the Mozilla Public License
* Version 1.1 (the "License"); you may not use this file except in
* compliance with the License. You may obtain a copy of the License at
* http://www.mozilla.org/MPL/
*
* Software distributed under the License is distributed on an "AS IS"
* basis, WITHOUT WARRANTY OF ANY KIND, either express or implied. See the
* License for the specific language governing rights and limitations
* under the License. 
******************************************************************************/


/*
 * @param1 recebe mensagem de confirmacao 
 * @param2 recebe mensagem de erro 
 */
function confirmInstall(msg1, msg2){
	var answer=confirm(msg1);
	if (answer==true){
		doit('formData');
	}else{
		alert(msg2);
	}
}

/*
 * @param recebe o nome de uma div HTML 
 * @return se style.display for none vira block,
 * senao se for block vira none
 */
function showHideDiv(divId){
	obj = document.getElementById(divId);
	v = obj.style.display; 

	if (v == 'none' || v == '' ) {
		disp = 'block';
	}else {
		disp = 'none';
	}
	
	obj.style.display = disp;
}

function doit(fId) {
	eForm = document.getElementById(fId);
	eForm.submit();
}

function cancelAction(dest)
{
	window.location.href = dest;
}  

function EmDesenvolvimento(){
 alert ("Em Desenvolvimento!");
}

/*
 * Funcao feita pelo DGI para resolver problema com as imagens no IE6
 * @param {Object} myImage
 */
function fixPNG(myImage){ // correctly handle PNG transparency in Win IE 5.5 or higher.
	 var imgID = (myImage.id) ? "id='" + myImage.id + "' " : ""
	 var imgClass = (myImage.className) ? "class='" + myImage.className + "' " : ""
	 var imgTitle = (myImage.title) ? "title='" + myImage.title + "' " : "title='" + myImage.alt + "' "
	 var imgStyle = "display:inline-block;" + myImage.style.cssText
	 var strNewHTML = "<span " + imgID + imgClass + imgTitle
	 strNewHTML += " style=\"" + "width:" + myImage.width + "px; height:" + myImage.height + "px;" + imgStyle + ";"
	 strNewHTML += "filter:progid:DXImageTransform.Microsoft.AlphaImageLoader"
	 strNewHTML += "(src=\'" + myImage.src + "\', sizingMethod='scale');\"></span>"
	 myImage.outerHTML = strNewHTML
}
