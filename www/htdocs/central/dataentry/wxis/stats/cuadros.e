'xargs'/
'cipar='v5015/
'db='v5001/
'prolog=,'/
select s(v9001)
	case 'buscar':
		'bool='mpu,v3000,mpl/,
        'h1=1'/
		'pft=','mpu,v'v3030,'#','mpl'/
	case 'presentar':
		'bool='mpu,v3000,mpl/,
        'h1=1'/
		'pft=@'v3030'.pft'/
	elsecase
		if v9002='Pais' then 'posting=1' fi/
		if v9002='Rango'  then,
			'pft=v2011"$$$",v2010*2,"$$$"v2010/'/ 
		else,
			'pft=v2011"$$$",ref(val(v3^m),v40),"$$$"v2010/'/ 
		fi,
		'k1='v4000/
		'k2='v4000.2'ZZZ'/
		'count=1000'/
endsel,

