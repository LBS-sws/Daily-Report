<?php
class Script {
	public static function genLookupSelect() {
		$mesg = Yii::t('dialog','No Record Found');
		$str = <<<EOF
$('#btnLookupSelect').on('click',function() {
	$('#lookupdialog').modal('hide');
	lookupselect();
});
		
$('#btnLookupCancel').on('click',function() {
	$('#lookupdialog').modal('hide');
	lookupclear();
});

function lookupselect() {
	var codeval = "";
	var valueval = "";
	$("#lstlookup option:selected").each(function(i, selected) {
		codeval = ((codeval=="") ? codeval : codeval+"~") + $(selected).val();
		valueval = ((valueval=="") ? valueval : valueval+" ") + $(selected).text();
	});
	var ofstr = $('#lookupotherfield').val();

	if (codeval && valueval!='$mesg') {
		var codefield = $('#lookupcodefield').val();
		var valuefield = $('#lookupvaluefield').val();
		if (codefield!='') $('#'+codefield).val(codeval);
		$('#'+valuefield).val(valueval);
		
		var others = (ofstr!='') ? ofstr.split("/") : new Array();
		if (others.length > 0) {
			$.each(others, function(idx, item) {
				var field = item.split(",");
				if (field.length > 0) {
					var fldId = 'otherfld_'+codeval+'_'+field[0];
					var fldVal = $('#'+fldId).val();
					$('#'+field[1]).val(fldVal);
				}
			});
		}
	}
	
	lookupclear();
}

function lookupclear() {
	$('#lookuptype').val('');
	$('#lookupcodefield').val('');
	$('#lookupvaluefield').val('');
	$("#txtlookup").val('');
	$("#lstlookup").empty();
	$('#fieldvalue').empty();
	$("#lstlookup").removeAttr('multiple');
	$("#lookup-label").removeAttr('style');
}
EOF;
		return $str;
	}

	public static function genLookupSelectText() {
		$mesg = Yii::t('dialog','No Record Found');
		$str = <<<EOF
$('#btnLookupSelect').on('click',function() {
	$('#lookupdialog').modal('hide');
	lookupselectText();
});
		
$('#btnLookupCancel').on('click',function() {
	$('#lookupdialog').modal('hide');
	lookupclearText();
});

function lookupselectText() {
	var codeval = "";
	var valueval = "";
	$("#lstlookup option:selected").each(function(i, selected) {
		codeval = ((codeval=="") ? codeval : codeval+"~") + $(selected).val();
		valueval = ((valueval=="") ? valueval : valueval+" ") + $(selected).text();
	});
	if(valueval==''){
	    valueval='全部';
	}
	var ofstr = $('#lookupotherfield').val();

	if (valueval!='$mesg') {
		var codefield = $('#lookupcodefield').val();
		var valuefield = $('#lookupvaluefield').val();
		if (codefield!='') $('#'+codefield).val(codeval);
		$('#'+valuefield).val(valueval);
		
		var others = (ofstr!='') ? ofstr.split("/") : new Array();
		if (others.length > 0) {
			$.each(others, function(idx, item) {
				var field = item.split(",");
				if (field.length > 0) {
					var fldId = 'otherfld_'+codeval+'_'+field[0];
					var fldVal = $('#'+fldId).val();
					$('#'+field[1]).val(fldVal);
				}
			});
		}
	}
	
	lookupclearText();
}

function lookupclearText() {
	$('#lookuptype').val('');
	$('#lookupcodefield').val('');
	$('#lookupvaluefield').val('');
	$("#txtlookup").val('');
	$("#lstlookup").empty();
	$('#fieldvalue').empty();
	$("#lstlookup").removeAttr('multiple');
	$("#lookup-label").removeAttr('style');
}
EOF;
		return $str;
	}
 
	public static function genLookupButton($btnName, $lookupType, $codeField, $valueField, $multiselect=false) {
		$multiflag = $multiselect ? 'true' : 'false';
		$str = <<<EOF
$('#$btnName').on('click',function() {
	var code = $("input[id*='$codeField']").attr("id");
	var value = $("input[id*='$valueField']").attr("id");
	var title = $("label[for='"+value+"']").text();
	$('#lookuptype').val('$lookupType');
	$('#lookupcodefield').val(code);
	$('#lookupvaluefield').val(value);
	if ($multiflag) $('#lstlookup').attr('multiple','multiple');
	if (!($multiflag)) $('#lookup-label').attr('style','display: none');
	$('#lookupdialog').find('.modal-title').text(title);
//	$('#lookupdialog').dialog('option','title',title);
	$('#lookupdialog').modal('show');
});
EOF;
		return $str;
	}
 
	public static function genLookupButtonEx($btnName, $lookupType, $codeField, $valueField, $otherFields=array(), $multiselect=false, $paramFields=array()) {
		$others = '';
		if (!empty($otherFields)) {
			foreach ($otherFields as $key=>$field) {
				$others .= ($others=='' ? '' : '/').$key.','.$field;
			}
		}
		$params = '';
		if (!empty($paramFields)) {
			foreach ($paramFields as $key=>$field) {
				$params .= ($params=='' ? '' : '/').$key.','.$field;
			}
		}
		$multiflag = $multiselect ? 'true' : 'false';
		$lookuptypeStmt = ($lookupType!=='*') ? "$('#lookuptype').val('$lookupType');" : '';

		if(key_exists("maxCount",$paramFields)){
            $lookuptypeStmt.="$('#lstlookup').attr('maxCount','{$paramFields['maxCount']}');";
        }else{
            $lookuptypeStmt.="$('#lstlookup').removeAttr('maxCount');";
        }
		$str = <<<EOF
$('#$btnName').on('click',function() {
	var code = $("input[id*='$codeField']").attr("id");
	var value = $("input[id*='$valueField']").attr("id");
	var title = $("label[for='"+value+"']").text();
	$lookuptypeStmt
	$('#lookupcodefield').val(code);
	$('#lookupvaluefield').val(value);
	$('#lookupotherfield').val('$others');
	$('#lookupparamfield').val('$params');
	if ($multiflag){
	    $('#lstlookup').attr('multiple','multiple');
	    $('#lookup-label').attr('style','display: block');
	}else{
	    $('#lstlookup').removeAttr('multiple');
	    $('#lookup-label').attr('style','display: none');
	}
//	$('#lookupdialog').dialog('option','title',title);
	$('#lookupdialog').find('.modal-title').text(title);
	$('#lookupdialog').modal('show');
});
EOF;
		return $str;
	}

	public static function genLookupButtonText($btnName, $lookupType, $codeField, $valueField, $otherFields=array(), $multiselect=false, $paramFields=array()) {
		$others = '';
		if (!empty($otherFields)) {
			foreach ($otherFields as $key=>$field) {
				$others .= ($others=='' ? '' : '/').$key.','.$field;
			}
		}
		$params = '';
		if (!empty($paramFields)) {
			foreach ($paramFields as $key=>$field) {
				$params .= ($params=='' ? '' : '/').$key.','.$field;
			}
		}
		$multiflag = $multiselect ? 'true' : 'false';
		$lookuptypeStmt = ($lookupType!=='*') ? "$('#lookuptype').val('$lookupType');" : '';

		if(key_exists("maxCount",$paramFields)){
            $lookuptypeStmt.="$('#lstlookup').attr('maxCount','{$paramFields['maxCount']}');";
        }else{
            $lookuptypeStmt.="$('#lstlookup').removeAttr('maxCount');";
        }
		$str = <<<EOF
$('#$btnName').on('click',function() {
	var code = $("input[id*='$codeField']").attr("id");
	var value = $("*[id*='$valueField']").attr("id");
	var title = $("label[for='"+value+"']").text();
	$lookuptypeStmt
	$('#lookupcodefield').val(code);
	$('#lookupvaluefield').val(value);
	$('#lookupotherfield').val('$others');
	$('#lookupparamfield').val('$params');
	if ($multiflag){
	    $('#lstlookup').attr('multiple','multiple');
	    $('#lookup-label').attr('style','display: block');
	}else{
	    $('#lstlookup').removeAttr('multiple');
	    $('#lookup-label').attr('style','display: none');
	}
//	$('#lookupdialog').dialog('option','title',title);
	$('#lookupdialog').find('.modal-title').text(title);
	$('#lookupdialog').modal('show');
});
EOF;
		return $str;
	}
/*
	public static function genLookupButtonEx($btnName, $lookupType, $codeField, $valueField, $otherFields=array(), $multiselect=false) {
		$others = '';
		if (!empty($otherFields)) {
			foreach ($otherFields as $key=>$field) {
				$others .= ($others=='' ? '' : '/').$key.','.$field;
			}
		}
		$multiflag = $multiselect ? 'true' : 'false';
		
		$str = <<<EOF
$('#$btnName').on('click',function() {
	var code = $("input[id*='$codeField']").attr("id");
	var value = $("input[id*='$valueField']").attr("id");
	var title = $("label[for='"+value+"']").text();
	$('#lookuptype').val('$lookupType');
	$('#lookupcodefield').val(code);
	$('#lookupvaluefield').val(value);
	$('#lookupotherfield').val('$others');
	if ($multiflag) $('#lstlookup').attr('multiple','multiple');
	if (!($multiflag)) $('#lookup-label').attr('style','display: none');
//	$('#lookupdialog').dialog('option','title',title);
	$('#lookupdialog').find('.modal-title').text(title);
	$('#lookupdialog').modal('show');
});
EOF;
		return $str;
	}
*/

	public static function genLookupSearch() {
		$mesg = Yii::t('dialog','No Record Found');
		$link = Yii::app()->createAbsoluteUrl("lookup");
		$str = <<<EOF
$('#btnLookup').on('click',function(){
	var data = "search="+$("#txtlookup").val();
	var link = "$link"+"/"+$("#lookuptype").val();
	$.ajax({
		type: 'GET',
		url: link,
		data: data,
		success: function(data) {
			jQuery("#lookup-list").html(data);
			var count = $("#lstlookup").children().length;
			if (count<=0) $("#lstlookup").append("<option value='-1'>$mesg</option>");
		},
		error: function(data) { // if error occured
			alert("Error occured.please try again");
		},
		dataType:'html'
	});
});
EOF;
		return $str;
	}

	public static function genLookupSearchEx() {
		$mesg = Yii::t('dialog','No Record Found');
		$link = Yii::app()->createAbsoluteUrl("lookup");
		$str = <<<EOF
$('#btnLookup').on('click',function(){
	var data = "search="+$("#txtlookup").val();
	
	var pstr = $('#lookupparamfield').val();
	var params = (pstr!='') ? pstr.split("/") : new Array();
	if (params.length > 0) {
		$.each(params, function(idx, item) {
			var field = item.split(",");
			if (field.length > 0) {
				var fldid = '#'+field[1];
				var fldval = $(fldid).val();
				console.log('id:'+fldid+',value:'+fldval);
				if (fldval !== undefined && fldval !==null) data += "&"+field[0]+"="+fldval;
			}
		});
	}
	
	var city = $("[id$='_city']").val();
	if (city !== undefined && city !==null) data += "&incity="+city;
	
	var link = "$link"+"/"+$("#lookuptype").val()+'ex';
	var ofstr = $('#lookupotherfield').val();
	$.ajax({
		type: 'GET',
		url: link,
		data: data,
		dataType: 'json',
		success: function(data) {
			$('#fieldvalue').empty();
			$("#lstlookup").empty();

			var others = (ofstr!='') ? ofstr.split("/") : new Array();
			
			$.each(data, function(index, element) {
				$("#lstlookup").append("<option value='"+element.id+"'>"+element.value+"</option>");
				if (others.length > 0) {
					$.each(others, function(idx, item) {
						var field = item.split(",");
						if (field.length > 0) {
							var hidden = $('<input/>',{type:'hidden',id:'otherfld_'+element.id+'_'+field[0], value:element[field[0]]});
							hidden.appendTo('#fieldvalue');
						}
					});
				}
			});
			
			var count = $("#lstlookup").children().length;
			if (count<=0) $("#lstlookup").append("<option value='-1'>$mesg</option>");
		},
		error: function(data) { // if error occured
			alert("Error occured.please try again");
		}
	});
});
EOF;
		return $str;
	}

/*	
	public static function genLookupSearchEx() {
		$mesg = Yii::t('dialog','No Record Found');
		$link = Yii::app()->createAbsoluteUrl("lookup");
		$str = <<<EOF
$('#btnLookup').on('click',function(){
	var data = "search="+$("#txtlookup").val();
	var link = "$link"+"/"+$("#lookuptype").val()+'ex';
	var ofstr = $('#lookupotherfield').val();
	$.ajax({
		type: 'GET',
		url: link,
		data: data,
		dataType: 'json',
		success: function(data) {
			$('#fieldvalue').empty();
			$("#lstlookup").empty();

			var others = (ofstr!='') ? ofstr.split("/") : new Array();
			
			$.each(data, function(index, element) {
				$("#lstlookup").append("<option value='"+element.id+"'>"+element.value+"</option>");
				if (others.length > 0) {
					$.each(others, function(idx, item) {
						var field = item.split(",");
						if (field.length > 0) {
							var hidden = $('<input/>',{type:'hidden',id:'otherfld_'+element.id+'_'+field[0], value:element[field[0]]});
							hidden.appendTo('#fieldvalue');
						}
					});
				}
			});
			
			var count = $("#lstlookup").children().length;
			if (count<=0) $("#lstlookup").append("<option value='-1'>$mesg</option>");
		},
		error: function(data) { // if error occured
			alert("Error occured.please try again");
		}
	});
});
EOF;
		return $str;
	}
*/

	public static function genReadonlyField() {
		$str = <<<EOF
$('[readonly]').addClass('readonly');
EOF;
		return $str;
	}

	public static function genTableRowClick() {
		$str = <<<EOF
$('.clickable-row').click(function() {
	window.document.location = $(this).data('href');
});
EOF;
		return $str;
	}

    public static function genDatePicker($fields) {
        $str = "";
        $language = Yii::app()->language;
        foreach ($fields as $field) {
            $str .= "$('#$field').datepicker({autoclose: true,language: '$language', format: 'yyyy/mm/dd'});";
        }
        return $str;
    }

	public static function genDeleteData($link) {
		$str = "
$('#btnDeleteData').on('click',function() {
	$('#removedialog').modal('hide');
	deletedata();
});

function deletedata() {
	var elm=$('#btnDelete');
	jQuery.yii.submitForm(elm,'$link',{});
}
		";
		return $str;
	}
	
	public static function genFileDownload($model, $formname, $doctype) {
		$doc = new DocMan($doctype,0,get_class($model));
		$ctrlname = Yii::app()->controller->id;
		$dwlink = Yii::app()->createAbsoluteUrl($ctrlname."/filedownload");
		$dlfuncid = $doc->downloadFunctionName;
		$str = "
function $dlfuncid(mid, did, fid) {
	href = '$dwlink?mastId='+mid+'&docId='+did+'&fileId='+fid+'&doctype=$doctype';
	window.open(href);
}
		";
		Yii::app()->clientScript->registerScript('downloadfile1'.$doctype,$str,CClientScript::POS_HEAD);
	}
	
	public static function genFileUpload($model, $formname, $doctype) {
		$doc = new DocMan($doctype,$model->id,get_class($model));

		$msg = Yii::t('dialog','Are you sure to delete record?');
		$ctrlname = Yii::app()->controller->id;
		$rmlink = Yii::app()->createAbsoluteUrl($ctrlname."/fileremove",array('doctype'=>$doctype));
		$dwlink = Yii::app()->createAbsoluteUrl($ctrlname."/filedownload");
		$rmfldid = get_class($model).'_removeFileId_'.strtolower($doctype);
		$tblid = $doc->tableName;
		$rmfuncid = $doc->removeFunctionName;
		$dlfuncid = $doc->downloadFunctionName;
		$btnid = $doc->uploadButtonName;
		$typeid = strtolower($doctype);
		$modelname = get_class($model);
		
		$str = "
function $rmfuncid(id) {
	if (confirm('$msg')) {
		document.getElementById('$rmfldid').value = id;
		var form = document.getElementById('$formname');
		var formdata = new FormData(form);
		$.ajax({
			type: 'POST',
			url: '$rmlink',
			data: formdata,
			mimeType: 'multipart/form-data',
			contentType: false,
			processData: false,
			success: function(data) {
				if (data!='NIL') {
					$('#$tblid').find('tbody').empty().append(data);
					attmno = '$modelname'+'_no_of_attm_'+'$typeid';
					counter = $('#'+attmno).val();
					var d = $('#doc$typeid');
					if (counter==undefined || counter==0) {
						d.removeClass();
						d.html('');
					} else {
						d.removeClass().addClass('label').addClass('label-info');
						d.html(counter);
					}
				}
			},
			error: function(data) { // if error occured
				alert('Error occured.please try again');
			}
		});	
	}
}

function $dlfuncid(mid, did, fid) {
	href = '$dwlink?mastId='+mid+'&docId='+did+'&fileId='+fid+'&doctype=$doctype';
	window.open(href);
}
		";
		Yii::app()->clientScript->registerScript('removefile1'.$doctype,$str,CClientScript::POS_HEAD);

		$link = Yii::app()->createAbsoluteUrl($ctrlname."/fileupload",array('doctype'=>$doctype));
		$str = "
$('#$btnid').on('click', function() {
	var form = document.getElementById('$formname');
	var formdata = new FormData(form);
	$.ajax({
		type: 'POST',
		url: '$link',
		data: formdata,
		mimeType: 'multipart/form-data',
		contentType: false,
		processData: false,
		success: function(data) {
			if (data!='NIL') {
				$('#$tblid').find('tbody').empty().append(data);
				$('input:file').MultiFile('reset')
				attmno = '$modelname'+'_no_of_attm_'+'$typeid';
				counter = $('#'+attmno).val();
				var d = $('#doc$typeid');
				if (counter==undefined || counter==0) {
					d.removeClass();
					d.html('');
				} else {
					d.removeClass().addClass('label').addClass('label-info');
					d.html(counter);
				}
			}
		},
		error: function(data) { // if error occured
			alert('Error occured.please try again');
		}
	});
});
		";
		Yii::app()->clientScript->registerScript('fileUpload'.$doctype,$str,CClientScript::POS_READY);

        self::showImgFun();
	}

	public static function genFileUploadList($model, $formname, $doctype) {
		$doc = new DocMan($doctype,$model->id,get_class($model));

		$msg = Yii::t('dialog','Are you sure to delete record?');
		$ctrlname = Yii::app()->controller->id;
		$rmlink = Yii::app()->createAbsoluteUrl($ctrlname."/fileremove",array('doctype'=>$doctype));
		$dwlink = Yii::app()->createAbsoluteUrl($ctrlname."/filedownload");
		$dwList = Yii::app()->createAbsoluteUrl($ctrlname."/fileList");
		$rmfldid = get_class($model).'_removeFileId_'.strtolower($doctype);
		$tblid = $doc->tableName;
		$rmfuncid = $doc->removeFunctionName;
		$dlfuncid = $doc->downloadFunctionName;
		$btnid = $doc->uploadButtonName;
		$typeid = strtolower($doctype);
		$modelname = get_class($model);

		$str = "
function $rmfuncid(id) {
	if (confirm('$msg')) {
		document.getElementById('$rmfldid').value = id;
		var form = document.getElementById('$formname');
		var formdata = new FormData(form);
		$.ajax({
			type: 'POST',
			url: '$rmlink',
			data: formdata,
			mimeType: 'multipart/form-data',
			contentType: false,
			processData: false,
			success: function(data) {
				if (data!='NIL') {
					$('#$tblid').find('tbody').html(data);
                    var id = $('#{$modelname}_id').val();
                    var num = $('#$tblid').find('tbody').eq(0).children('tr').length;
                    num--;
                    num = num<0?0:num;
                    $('.click-doc[data-id=\"'+id+'\"]').data('num',num);
                    $('.click-doc[data-id=\"'+id+'\"]>.badge').text(num);
				}
			},
			error: function(data) { // if error occured
				alert('Error occured.please try again');
			}
		});	
	}
}

function $dlfuncid(mid, did, fid) {
	href = '$dwlink?mastId='+mid+'&docId='+did+'&fileId='+fid+'&doctype=$doctype';
	window.open(href);
}
		";
		Yii::app()->clientScript->registerScript('removefile1'.$doctype,$str,CClientScript::POS_HEAD);

		$link = Yii::app()->createAbsoluteUrl($ctrlname."/fileupload",array('doctype'=>$doctype));
		$str = "
$('#$btnid').on('click', function() {
	var form = document.getElementById('$formname');
	var formdata = new FormData(form);
	$.ajax({
		type: 'POST',
		url: '$link',
		data: formdata,
		mimeType: 'multipart/form-data',
		contentType: false,
		processData: false,
		success: function(data) {
			if (data!='NIL') {
				$('#$tblid').find('tbody').html(data);
				$('input:file').MultiFile('reset');
				var id = $('#{$modelname}_id').val();
				var num = $('#$tblid').find('tbody').eq(0).children('tr').length;
				num--;
				num = num<0?0:num;
				$('.click-doc[data-id=\"'+id+'\"]').data('num',num);
				$('.click-doc[data-id=\"'+id+'\"]>.badge').text(num);
			}
		},
		error: function(data) { // if error occured
			alert('Error occured.please try again');
		}
	});
});
		";
		Yii::app()->clientScript->registerScript('fileUpload'.$doctype,$str,CClientScript::POS_READY);


		$link = Yii::app()->createAbsoluteUrl($ctrlname."/fileList",array('doctype'=>$doctype));
		$str = "
		function getFileListAll() {
            var form = document.getElementById('$formname');
            var formdata = new FormData(form);
            $.ajax({
                type: 'POST',
                url: '$link',
                data: formdata,
                mimeType: 'multipart/form-data',
                contentType: false,
                processData: false,
                success: function(data) {
                    if (data!='NIL') {
                        $('#$tblid').find('tbody').html(data);
                    }
                },
                error: function(data) { // if error occured
                    alert('Error occured.please try again');
                }
            });
        }
		";
		Yii::app()->clientScript->registerScript('fileList'.$doctype,$str,CClientScript::POS_READY);

        self::showImgFun();
	}

    public static function showImgFun(){
        Yii::app()->clientScript->registerCssFile(Yii::app()->baseUrl . "/css/viewer.css");//图片阅读
        Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . "/js/viewer.js", CClientScript::POS_END);//图片阅读
        $js = "
            $('body').on('click','.viewer-canvas',function(){
                $('body').removeClass('viewer-open');
                $(this).parent('.viewer-container').remove();
            });
            $('body').on('click','.viewer-canvas *',function(e){
                e.stopPropagation();
            });
            $('body').on('click','.search_box_img',function(){
                var clickText = $(this).text();
                if($('#viewer-ul').length>0){
                    $('#viewer-ul').remove();
                }
                var list = $('<ul id=\"viewer-ul\" class=\"hide\"></ul>');
                $(this).parents('table:first').find('img').each(function(){
                    var title = $(this).parents('td.search_box_img').eq(0).text();
                    var li = $('<li></li>');
                    var img = $('<img>');
                    img.attr({ src:$(this).attr('src'),alt:title });
                    if(title == clickText){
                        img.addClass('click_viewer_img');
                    }
                    li.html(img);
                    list.append(li);
                });
                $('body').append(list);
                list.viewer({ url: 'src'});
                list.find('.click_viewer_img').trigger('click');
            });
	    ";
        Yii::app()->clientScript->registerScript('showImgFun',$js,CClientScript::POS_READY);
    }
}
?>