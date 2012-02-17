var wPreview = null;

function preview ( prevAction )
{
	wPreview = window.open( "../php/index.php", "preview", "top=0,left=0,height=530,width=785,menubar=no,location=no,resizable=yes,scrollbars=yes,status=yes" );
	wPreview.focus();
}

function attachW ( href )
{
	window.open( href, "attach", "top=20,left=20,height=540,width=785,menubar=no,location=no,resizable=yes,scrollbars=yes,status=yes" );
}


function fnow( href )
{
	window.open( href, "attach", "top=150,left=250,height=500,width=600,menubar=no,location=no,resizable=yes,scrollbars=yes,status=yes" );
}