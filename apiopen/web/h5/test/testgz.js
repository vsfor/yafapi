//... test for self gz and ungz
var sz = {
	a:"b",
	b:"c",
	d:[0,0,1,3],
	e:"收货",
	f:123.2,
	g:{
		a:"地址哈哈"
	}
};
var gzStr =  gzcompress(sz);
console.log(gzStr);

var gunzStr = gzuncompress(gzStr);
console.log(gunzStr);

console.log(JSON.stringify(sz) == JSON.stringify(gunzStr));
//.... test for ungz phpzipped string
var phpgz = 'eJw9jdENgCAMRKfpJ4QUavVTJK5hpOIG7G8LiUnvcrn0tcAZEG8VxN2cjr4QB8DsNAOe5mM6N0m2glpj9fJD4h/A4FVt8JI2zRapEr/uglh+0OrUUIk8n608D0Xg8gHkuSAg';
console.log(gzuncompress(phpgz));

var jsgz = 'eJw9jWEOwjAIRk/zfq6ZdB3tz7HiNYxOvf8RVjRZwvsSAg9QQ+Q5IG+RvtIKNiM2IXfKHuWKGXWJDdlHvtJxOUd6I3MafEK3G7WNBi9snabf6UHul4ovWI2R2O/dStX/sYz2E5gdHgA=';
console.log(gzuncompress(jsgz));

// console.log(JSON.stringify(gzuncompress(phpgz)) ); console.log( JSON.stringify(gzuncompress(jsgz)));
console.log(JSON.stringify(gzuncompress(phpgz)) == JSON.stringify(gzuncompress(jsgz)));

var jv = {
	'a' : '数+-/\\组',
	'b.c' : 'c.d . e汉 字f-_=',
	'中+文' : 3
};
console.log(gzcompress(jv));
