$(document).on('click','#btnLookupSelect',function() {
	$('#lookupdialog').modal('hide');
	lookupselect();
});
		
$(document).on('click','#btnLookupCancel',function() {
	$('#lookupdialog').modal('hide');
	lookupclear();
});

$(document).on('click','#btnLookup',function(){
	var data = "search="+$("#txtlookup").val();
	
	var pstr = $('#lookupparam').val();
	var params = JSON.parse(pstr);
	
	var para = params['paramfield'];
	if (para !== undefined && para !== null) {
		$.each(para, function(key, value) {
			data += "&"+key+"="+value;
		});
	}
	
	var city = $("[id$='_city']").val();
	if (city !== undefined && city !==null) data += "&incity="+city;
	
	var getUrl = window.location;
	var baseUrl = getUrl.protocol + '//' + getUrl.host + '/' + getUrl.pathname.split('/')[1] + '/index.php/lookup';
	
	var link = baseUrl + "/" + params['lookuptype'] + 'ex2';
	var ofstr = params['otherfield'];
	$.ajax({
		type: 'GET',
		url: link,
		data: data,
		dataType: 'html',
		success: function(data) {
			$('#fieldvalue').empty();
			$("#lookup-list").html(data);
		},
		error: function(data) { // if error occured
			alert("Error occured.please try again");
		}
	});
});

function opendialog(lookupType, codeField, valueField, multiflag, others, params) {
	var code = $("input[id*='"+codeField+"']").attr("id");
	var value = $("input[id*='"+valueField+"']").attr("id");
	var title = $("label[for='"+value+"']").text();
	var type = (lookupType==='*') ? '' : lookupType;
	
	var param = {
		'codefield': code,
		'valuefield': value,
		'otherfield': others,
		'paramfield': params,
		'lookuptype': type,
		'multiple': multiflag
	}
	
	var j = JSON.stringify(param);
    $('#lookupparam').val(j);
	if (!multiflag) $('#lookup-label').attr('style','display: none');
	$('#lookupdialog').find('.modal-title').text(title);
	$('#lookupdialog').modal('show');
}

function lookupselect() {
	var codeval = "";
	var valueval = "";

	var pstr = $('#lookupparam').val();
	var params = JSON.parse(pstr);
	
	if (params.multiple) {
	} else {
		codeval = $("input:checked[name='lstlookup']").val();
		valueval = $("input[name='lstlookup'][value='"+codeval+"']").parent().text();
		if (codeval) {
			if (params.codefield) $('#'+params.codefield).val(codeval);
			$('#'+params.valuefield).val(valueval);
			$.each(params.otherfield, function(key,value) {
				var fldId = 'otherfld_'+codeval+'_'+key;
				var fldVal = $('#'+fldId).val();
				$('#'+value).val(fldVal);
			});
		}
	}

/*	
	each(function(i, selected) {
		codeval = ((codeval=="") ? codeval : codeval+"~") + $(selected).val();
		valueval = ((valueval=="") ? valueval : valueval+" ") + $(selected).text();
	});
*/	
	lookupclear();
}

function lookupclear() {
	$('#lookupparam').val('');
//	$('#lookupcodefield').val('');
//	$('#lookupvaluefield').val('');
	$("#txtlookup").val('');
	$("#lookup-list").empty();
	$('#fieldvalue').empty();
//	$("#lstlookup").removeAttr('multiple');
	$("#lookup-label").removeAttr('style');
}
