$(document).ready(function(e) {
	$('button[type="reset"]').click(function(e) {
		$('div.form-group.has-error .alert.alert-danger').hide().html('');
		$('div.form-group').removeClass('has-error');
	});
	
	$('body').on('keypress', 'input[type=number][maxlength]', function(event){
		var key = event.keyCode || event.charCode;
		var charcodestring = String.fromCharCode(event.which);
		var txtVal = $(this).val();
		var maxlength = $(this).attr('maxlength');
		var regex = new RegExp('^[0-9]+$');
		// 8 = backspace 46 = Del 13 = Enter 39 = Left 37 = right Tab = 9
		if( key == 8 || key == 46 || key == 13 || key == 37 || key == 39 || key == 9 ){
			return true;
		}
		// maxlength allready reached
		if(txtVal.length == maxlength){
			event.preventDefault();
			return false;
		}
		// pressed key have to be a number
		if( !regex.test(charcodestring) ){
			event.preventDefault();
			return false;
		}
		return true;
	});
});

function checkValue($this)
{
	var regex = new RegExp("^[0-9]+$");
	if (regex.test($this.val())) 
	{
		return true;
	}
	return false;
}
	
function deleteRecord(id,func)
{
	var retVal = confirm("Do you want to continue ?");
	if( retVal == true )
	{
		$.ajax({
			type: "POST",   
			url: content_url+func+'/'+id,
			dataType: 'json',
			success: function(html)
			{
				if($.trim(html) == '1')
				{
					location.reload();
				}
				else
				{
					alert('Error Occurred, Try again later.');
				}
			}
		});
		return false;
	}
}

function utoa(str) 
{
	return window.btoa(window.btoa(window.btoa(window.btoa(window.btoa(window.btoa(window.btoa(unescape(encodeURIComponent(str)))))))));
}	

function reset_offset(val)
{
	offset = 0;
	$('#load-more').fadeOut(150);
}
/**************************************************************************************************************************/
function ChangeStatus(tblname,row_name,id,val)
{
	var retVal = true;//confirm("Do you want to continue ?");
	if( retVal == true )
	{
		$.ajax({
			type: "POST",   
			url: content_url+'ChangeStatus/'+tblname+'/'+row_name+'/'+id+'/'+val,
			dataType: 'json',
			success: function(html)
			{
				location.reload();
			}
		});
		return false;
	}
}

function filesize_ext()
{
	$('.signature_info').html('');
	var file_name	= $("#signature").val();	
	var ext 		= file_name.split('.').pop();
	ext				= $.trim(ext);
	if(ext == 'png' || ext == 'PNG' || ext == 'jpg' || ext == 'JPG' || ext == 'jpeg' || ext == 'JPEG')
	{
		var _size		= showFileSize('signature');
		if(_size > 1048576)
		{
			if($('.signature_info').hasClass('alert'))
			{
				$('.signature_info').removeClass('alert').removeClass('mt-1');
			}
			$('.signature_info').html('<span class="alert alert-danger pull-left mt-1">File size should not exceed 1 MB.</span>').fadeIn(150);
			$("#signature").val('');
		}
	}
	else
	{
		if($('.signature_info').hasClass('alert'))
		{
			$('.signature_info').removeClass('alert').removeClass('mt-1');
		}
		$('.signature_info').html('<span class="alert alert-danger pull-left mt-1">Files with .'+ext+' extensions are not allowed.</span>').fadeIn(150);
		$("#signature").val('');
	}
	console.log(_size);
}

function showFileSize(id) 
{
	var input, file;

	if (!window.FileReader) 
	{
		return "The file API isn't supported on this browser yet.";
	}

	input = document.getElementById(id);
	
	if (!input) 
	{
		return "Um, couldn't find the fileinput element.";
	}
	else if (!input.files) 
	{
		return "This browser doesn't seem to support the `files` property of file inputs.";
	}
	else if (!input.files[0]) 
	{
		return "Please select a file before clicking 'Load'";
	}
	else 
	{
		file = input.files[0];
		return file.size;
	}
}

/**************************************************************************************************************************/
function submitForm_to(post_to,locate_to,form_id)
{
	if(form_id == 'add_product')
	{
		$('textarea.ckeditor').each(function () {
			var $textarea = $(this);
			$textarea.val(CKEDITOR.instances[$textarea.attr('name')].getData());
		});
	}
	
	$('#msgs-'+form_id).html('<span class="alert alert-success">Processing Information...</span>').fadeIn(150);
	$('.alert.alert-danger').removeClass('alert').removeClass('alert-danger').html('').fadeIn(150);
	$('#'+form_id+' #submit').prop('disabled',true);
	$('#'+form_id).ajaxForm({
		type: "POST",   
		url: post_to,
		dataType: 'json',
		success: function(data)
		{
			//console.log(data);
			$('#'+form_id+' #submit').prop('disabled',false);
			
			if (!data.success) 
			{
				if(data.is_login)
				{
					window.location = data.redirect;
				}
				
				if(data.message){
					$('#msgs-'+form_id).html('<span class="alert alert-danger">'+data.message+'</span>');
				}else{
					$('#msgs-'+form_id).html('<span class="alert alert-danger">Error Occurred, Please enter all required information.</span>');
				}
				for(var i = 0; i < data.errors.length; i++)
				{
					if($.trim(data.errors[i].error) != '')
					{
						if(data.errors[i].error)
						{
							if(data.errors[i].field)
							{
								$('#'+data.errors[i].field).parent('div.form-group').addClass('has-error');
								$('.'+data.errors[i].field+'_info').addClass('mt-1').addClass('pull-left').addClass('alert').addClass('alert-danger').html(data.errors[i].error).fadeIn(150);
							}
							else
							{
								$('#msgs-'+form_id).html('<span class="alert alert-danger">'+data.errors[i].error+'</span>').fadeIn(150);
							}
						}
						else
						{
							$('#'+data.errors[i].field).parent('div').removeClass('has-error');
							$('.'+data.errors[i].field+'_info').addClass('mt-1').addClass('pull-left').addClass('alert').addClass('alert-danger').html('').fadeIn(150);
						}
					}
				}
				setTimeout(function(){ $('#msgs-'+form_id).html(''); }, 5000);					
			} 
			else 
			{
				if(data.message)
				{
					if(form_id == 'bulk-user')
					{
						$('#roleid').val('');
						$('#file').val('');
						$('#msgs-'+form_id).html('<span class="alert alert-success">'+data.message +'</span>').fadeIn(150).delay(5000).fadeOut(150);
					}
					else if(form_id == 'search-experts')
					{
						$('#page_no').val(data.page_no);
						$('#expert-lists').html(data.html);
						$('#expert-details').html(data.expert_details);
					}
					else
					{
						$('#msgs-'+form_id).html('<span class="alert alert-success">'+data.message +'</span>').fadeIn(150).delay(5000).fadeOut(150);
					}
				}
				
				if(locate_to != '')
				{
					if(data.cropped)
					{
						window.location = data.crop_url;
					}
					else if(data.redirect_url)
					{
						window.location = data.redirect_url;
					}
					else
					{
						window.location = locate_to;
					}
				}
				else if(form_id != 'search-experts'){
					$('input[type="text"], input[type="file"], textarea').val('');
				}
			}
		},
		error: function (request, status, error) {
			//alert(request.responseText);
			//console.log('request',request,'status',status, 'error',error);
			$('#msgs-'+form_id).html('<span class="alert alert-danger">Server Error: '+request.status+' ('+error+')</span>');
			$('#'+form_id+' #submit').prop('disabled',false);
		}
	}).submit();
	return false;
}
/**************************************************************************************************************************/

function bulk_upload(post_to,locate_to,form_id) 
{
	$('textarea.ckeditor').each(function () {
	   var $textarea = $(this);
	   $textarea.val(CKEDITOR.instances[$textarea.attr('name')].getData());
	});
	
	var bar = $('.bar');		
	$('#'+form_id).ajaxForm({
		type: "POST",   
		url: post_to,
		dataType: 'json',
		beforeSend: function(xhr) {
			_xhr = xhr;
			//console.log(_xhr);
			var percentVal = 0;
			if(percentVal > 100)
			{
				percentVal  = 100;
			}
			else if(percentVal < 0)
			{
				percentVal  = 0;
			}
			bar.css('width', percentVal+'%');
			$('#msgs-'+form_id).html('<span class="alert alert-success">Processing, Please wait...</span>').fadeIn(150);
			//console.log(percentVal);
		},
		uploadProgress: function(event, position, total, percentComplete) {
			var percentVal = percentComplete;
			if(percentVal > 100)
			{
				percentVal  = 100;
			}
			else if(percentVal < 0)
			{
				percentVal  = 0;
			}
			//console.log(percentVal);
			bar.css('width', percentVal+'%');
			bar.html(percentVal + '% Processed...');
			
			if(percentVal == 100 || percentVal == '100')
			{
				_xhr = null;
				$('#msgs-'+form_id).html('<span class="alert alert-success bar">100%, Please wait while processing completes.</span>').fadeIn(150);				
			}
			else
			{
				$('#msgs-'+form_id).html('<span class="alert alert-success bar">'+percentVal+'% Processed...</span>').fadeIn(150);					
			}
		},
		complete: function(xhr) {
			_xhr = null;
			
			if(xhr.responseText)
			{
				var data	= JSON.parse(xhr.responseText);
				//console.log(xhr);
				if(data.success)
				{
					$('#msgs-'+form_id).html('<span class="alert alert-success">'+data.message +'</span>').fadeIn(150).delay(5000).fadeOut(150);
					window.location = locate_to;
				}
				else
				{		
					$('#msgs-'+form_id).html('<span class="alert alert-danger">Error, Please provide all required fields.</span>');
					//console.log(data.errors.length);
					for(var i = 0; i < data.errors.length; i++)
					{
						if($.trim(data.errors[i].error) != '')
						{
							if(data.errors[i].error)
							{
								if(data.errors[i].field)
								{
									$('#'+data.errors[i].field).parent('div.form-group').addClass('has-error');
									$('.'+data.errors[i].field+'_info').addClass('mt-1').addClass('pull-left').addClass('alert').addClass('alert-danger').html(data.errors[i].error).fadeIn(150);
								}
								else
								{
									$('#msgs-'+form_id).html('<span class="alert alert-danger">'+data.errors[i].error+'</span>').fadeIn(150);
								}
							}
							else
							{
								$('#'+data.errors[i].field).parent('div').removeClass('has-error');
								$('.'+data.errors[i].field+'_info').addClass('mt-1').addClass('pull-left').addClass('alert').addClass('alert-danger').html('').fadeIn(150);
							}
						}
					}
					setTimeout(function(){ $('#msgs-'+form_id).html(''); }, 5000);
				}
			}
		},
		error: function (data) {
			if(data.statusText == 'abort')
			{
				$('#msgs-'+form_id).html('<span class="alert alert-danger">Request Aborted.</span>').fadeIn(150).delay(5000).fadeOut(150);
			}
			else
			{
				$('#msgs-'+form_id).html('<span class="alert alert-danger">Error occured while processing.</span>').fadeIn(150).delay(5000).fadeOut(150);
			}
		}
	}).submit();
	return false;
}

/**************************************************************************************************************************/
						//'txt','number','phone','Phone No.'
function validate(input_type, sub_type, tag_id, tag_title,min_len, max_len, data_type)
{
	var val 	= $.trim($('#'+tag_id).val());
	var msg		= '';
	console.log(data_type);
	if(input_type == 'dd')
	{
		if(val == '')
		{
			msg	= 'Please select '+tag_title;
		}
	}
	else if(input_type == 'cb')
	{
		if(val == '')
		{
			msg	= 'Please select '+tag_title;
		}
	}
	else
	{
		if(val == '')
		{
			if(tag_id == 'cpassword')
			{
				msg	= 'Please '+tag_title;
			}
			else
			{
				msg	= 'Please enter '+tag_title;
			}
			
		}
		else if(sub_type == 'email' && !(validateEmail(val)))
		{
			msg	= 'Please enter a valid '+tag_title;
		}
		else if(sub_type == 'number' && !(isNumber(val)))
		{
			msg	= 'Only numeric values are allowed in '+tag_title;
		}
		else if(val.length > max_len && max_len != '')
		{
			msg	= 'Only '+max_len+' characters are allowed for '+tag_title;
		}
		else if(val.length < min_len && min_len != '')
		{
			msg	= tag_title+' must be at least '+min_len+' characters in length';
		}
		else if(data_type == 'date' && !(isValidDate(val)))
		{
			msg	= 'Please enter a valid '+tag_title;
		}
		else if(data_type == 'alpha' && !(isAlpha(val)))
		{
			msg	= 'Only alphabets are allowed in '+tag_title;
		}
		else if(sub_type == 'float' && !(isFloat(val)))
		{
			msg	= 'Only numeric are allowed in '+tag_title;
		}
		else if(data_type == 'alphanumeric' && !(isAlphaNumeric(val)))
		{
			msg	= 'Only alphabets & numbers are allowed in '+tag_title;
		}
		else if(data_type == 'notonlyspecialchar' && !(isOnlySpecialCharacters(val)))		
		{
			msg	= 'Only special characters are not allowed in '+tag_title;
		}
		else if(tag_id == 'cpassword' && val != $.trim($('#password').val()))
		{
			msg	= 'Confirm Password doesn\'t match';
		}
	}
	
	if(msg != '')
	{
		$('#'+tag_id).parent('div').addClass('has-error');
		$('.'+tag_id+'_info').html(msg+'.').addClass('mt-1').addClass('pull-left').addClass('alert').addClass('alert-danger').fadeIn(150);
		//setTimeout(function(){ $('#'+tag_id).parent('div.form-group').removeClass('has-error'); }, 5000);
	}
	else
	{
		$('.'+tag_id+'_info').html('').removeClass('alert').removeClass('alert-danger').fadeIn(150);
		$('#'+tag_id).parent('div').removeClass('has-error');
	}
}

function validate_length(tag_id, tag_title,min_len, max_len)
{
	var val 	= $.trim($('#'+tag_id).val());
	var msg		= '';
	
	if(val != '')
	{
		if(val.length > max_len && max_len != '')
		{
			msg	= 'Only '+max_len+' characters are allowed for '+tag_title;
		}
		else if(val.length < min_len && min_len != '')
		{
			msg	= tag_title+' must be at least '+min_len+' characters in length';
		}			
	}
	
	if(msg != '')
	{
		$('#'+tag_id).parent('div').addClass('has-error');
		$('.'+tag_id+'_info').html(msg+'.').addClass('mt-1').addClass('pull-left').addClass('alert').addClass('alert-danger').fadeIn(150);
		//setTimeout(function(){ $('#'+tag_id).parent('div.form-group').removeClass('has-error'); }, 5000);
	}
	else
	{
		$('.'+tag_id+'_info').html('').removeClass('alert').addClass('alert-danger').fadeIn(150);
		$('#'+tag_id).parent('div').removeClass('has-error');
	}
}

function validate_email(tag_id, tag_title)
{
	var val 	= $.trim($('#'+tag_id).val());
	var msg		= '';
	
	if(val != '')
	{
		if(!(validateEmail(val)))
		{
			msg	= 'Please enter a valid '+tag_title;
		}
	}
	
	if(msg != '')
	{
		$('#'+tag_id).parent('div').addClass('has-error');
		$('.'+tag_id+'_info').html(msg+'.').addClass('mt-1').addClass('pull-left').addClass('alert').addClass('alert-danger').fadeIn(150);
		//setTimeout(function(){ $('#'+tag_id).parent('div.form-group').removeClass('has-error'); }, 5000);
	}
	else
	{
		$('.'+tag_id+'_info').html('').removeClass('alert').addClass('alert-danger').fadeIn(150);
		$('#'+tag_id).parent('div').removeClass('has-error');
	}
}

/**************************************************************************************************************************/
function validateEmail(x) 
{
	var atpos = x.indexOf("@");
	var dotpos = x.lastIndexOf(".");
	if (atpos < 1 || dotpos < atpos+2 || dotpos+2 >= x.length) 
	{
		return false;
	}
	else
	{
		return true;
	}
}

/**************************************************************************************************************************/
function isNumber(val) 
{
	var regex = new RegExp("^[0-9]+$");
	if (regex.test(val)) 
	{
		return true;
	}
	return false;
}

/**************************************************************************************************************************/
function isFloat(val) 
{
	var regex = new RegExp("^[0-9.]+$");
	if (regex.test(val)) 
	{
		return true;
	}
	return false;
}

/**************************************************************************************************************************/
function isAlphaNumeric(val) 
{
	var regex = new RegExp("^[a-zA-Z0-9 ]+$");
	if (regex.test(val)) 
	{
		return true;
	}
	return false;
}

/**************************************************************************************************************************/
function isAlpha(val) 
{
	var regex = new RegExp("^[a-zA-Z ]+$");
	if (regex.test(val)) 
	{
		return true;
	}
	return false;
}

/**************************************************************************************************************************/
function isOnlySpecialCharacters(val) 
{
	//var regex = new RegExp("^[a-zA-Z ]+$");
	if (/[a-z 0-9]/.test(val.toLowerCase())) 
	{
		return true;
	}
	return false;
}

/**************************************************************************************************************************/
function isValidDate(dateString)
{
	// First check for the pattern
	var regex_date = /^\d{4}\-\d{1,2}\-\d{1,2}$/;

	if(!regex_date.test(dateString))
	{
		return false;
	}

	// Parse the date parts to integers
	var parts   = dateString.split("-");
	var day     = parseInt(parts[2], 10);
	var month   = parseInt(parts[1], 10);
	var year    = parseInt(parts[0], 10);

	// Check the ranges of month and year
	if(year < 1000 || year > 3000 || month == 0 || month > 12)
	{
		return false;
	}

	var monthLength = [ 31, 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31 ];

	// Adjust for leap years
	if(year % 400 == 0 || (year % 100 != 0 && year % 4 == 0))
	{
		monthLength[1] = 29;
	}

	// Check the range of the day
	return day > 0 && day <= monthLength[month - 1];
}

/**************************************************************************************************************************/
function searchusers()
{
	var val = $.trim($('#search_user').val());
	if(val == '')return false;
	window.location = site_url+'userlisting/'+val;
}

function search_users(event)
{
	if(event.keyCode == 13)
	{
		searchusers();
	}
}
/**************************************************************************************************************************/
// create password live validation
var hasAlpha 		= false;
var hasNumber		= false;
var hasCapital		= false;
var hasValidLength 	= false;
function validatePasswords(form_id, password_field)
{
	$(form_id+' '+password_field).keyup(function() {
		var pswd 			= $(this).val();
		//validate letter
		//console.log(pswd);
		if (pswd.match(/[A-z]/) ) 
		{
			$(form_id+' #letter').removeClass('invalid').addClass('valid');
			hasAlpha 		= true;
		} 
		else 
		{
			$(form_id+' #letter').removeClass('valid').addClass('invalid');
			hasAlpha 		= false;
		}
		//validate capital letter
		
		if ( pswd.match(/[A-Z]/) ) 
		{
			hasCapital		= true;
			$(form_id+' #capital').removeClass('invalid').addClass('valid');
		} 
		else 
		{
			hasCapital		= false;
			$(form_id+' #capital').removeClass('valid').addClass('invalid');
		}
		
		//validate number
		if ( pswd.match(/\d/) ) 
		{
			hasNumber		= true;
			$(form_id+' #number').removeClass('invalid').addClass('valid');
		} 
		else 
		{
			hasNumber		= false;
			$(form_id+' #number').removeClass('valid').addClass('invalid');
		}
		//validate the length
		if(pswd.length < 8 ) 
		{
			hasValidLength = false;
			$(form_id+' #length').removeClass('valid').addClass('invalid');
		} 
		else 
		{
			hasValidLength = true;
			$(form_id+' #length').removeClass('invalid').addClass('valid');
		}		
	
		if(hasValidLength == true && hasAlpha == true && hasNumber == true && hasCapital == true)
		{
			$(form_id+' #pswdbtn').attr('disabled',false);
		}
		else
		{
			$(form_id+' #pswdbtn').attr('disabled',true);
		}			   
	});
	
	$(form_id+' '+password_field).focus(function(){
		$(form_id+' .pass_hint').css('display','');		 
	});
	
	$(form_id+' '+password_field).blur(function() {
		if(hasValidLength == true && hasAlpha == true && hasNumber == true && hasCapital == true)
		{
			$(form_id+' .pass_hint').css('display','none');
		}		   
	});
}
/*********************************************************************************************************************/