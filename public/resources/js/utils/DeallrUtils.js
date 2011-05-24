DeallrUtil = {};

DeallrUtil.replaceTokens = function(template, tokens) {
	var data = template;
	$.each( tokens, function(key, val) {
		data = data.replace('{'+key+'}',val);
	});
	return data;
};

DeallrUtil.stringSortCallback = function(a,b) {
	var str1 = a.name.toLowerCase();
	var str2 = b.name.toLowerCase();
	var ret_value = str1 == str2 ? 0 : ( str1 > str2 ? 1 : -1 );
	return ret_value;
};