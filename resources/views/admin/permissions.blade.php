@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="col-sm-12 col-xl-12">
        <div class="card">
            <form method="post" enctype="multipart/form-data" class="form-horizental" id="submit-form">
                <div class="card-header"><i class="fa fa-align-justify"></i> {{ $data['page_title'] }}</div>
                <div class="card-body">
                    <fieldset class="mt-3">
                        <legend>Assign Permissions: <span class="text-info">{{ !empty($data['tbl_record']) ? $data['tbl_record']->title : 'Role' }}</span></legend>
                        <div class="row mt-2">
                        @foreach($data['menus'] as $menu)
                          <div class="form-group col-sm-4" id="{{ 'acc-'.$menu->id }}">
                                <div class="form-check form-check-inline mr-1">
                                  <input class="form-check-input {{ 'chk-'.$menu->parent_id }}" {{ (!empty($data['menu_ids']) && in_array($menu->id,$data['menu_ids'])) ? 'checked' : '' }} name="chk[]" value="{{ $menu->id }}" data-id="{{ $menu->parent_id }}" id="{{ 'chk-'.$menu->id }}" type="checkbox">
                                </div>
                                <label class="my-label" for="{{ 'chk-'.$menu->id }}">{{ trim($menu->parent) != '' ? $menu->parent.' >> '.$menu->title : $menu->title }}</label>
                          </div>
                        @endforeach			
                        </div>
                    </fieldset>
                </div>
                <div class="card-footer">
                    <input type="button" name="submit-btn" id="submit-btn" value="Save" style="min-width:25%" class="btn btn-primary mt-3">
                    <input type="hidden" name="user_group_id" id="user_group_id" value="{{ $data['user_group_id'] }}">
                    @csrf
                    <div class="form-group col-sm-12 pull-left no-pad text-left">
                        <p class="mt-3" id="msgs-submit-form"></p>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<style type="text/css">
	.form-control {
		border-radius: 0 !important;
		box-shadow: none;
		border-color: #d2d6de;
		font-size: 13px;
	}
	label {
		font-size: 14px;
		display: inline-block;
		margin-bottom: 0;
		font-weight:400;
	}
	.mr-1, .mx-1 {
		margin-right: .25rem!important;
	}
	.form-check-inline {
		display: -ms-inline-flexbox;
		display: inline-flex;
		-ms-flex-align: center;
		align-items: center;
		padding-left: 0;
		margin-right: .75rem;
	}
	.form-check-inline .form-check-input{
		width:20px;
		height:20px;
	}
	.form-group {
		display: flex;
    	align-items: center;
	}
</style>
@php 
	$user_group_id = $data['user_group_id']
@endphp
<script type="text/javascript">
	$(document).ready(function () {
		
		$('#submit-btn').click(function(e) {
			$('.form-check-input').attr('disabled', false);
			submitForm_to('{{ admin_url(). "/permissions/".encrypt($user_group_id) }}','{{ admin_url(). "/permissions/".encrypt($user_group_id) }}','submit-form');
		});
		
		$('.form-check-input').change(function(e) {
			//console.log($(this).data('id'));
			var parent_id = $(this).data('id');
			var this_id = $(this).val();
			if($(this).prop("checked")){
				//console.log('a');
				$('#chk-'+parent_id).prop('checked', true);
				$('#chk-'+parent_id).attr('disabled', true);
			}else{
				var t = false;
				$(".chk-"+parent_id).each(function(index, value) {
				  
				  if($('#'+this.id).prop("checked")){
					  t = true;
				  }
				});
				
				//console.log('#chk-'+parent_id, t);
				if(t){
					$('#chk-'+parent_id).prop('checked', t);
					$('#chk-'+parent_id).attr('disabled', t);
				}else{
					//$('#chk-'+parent_id).prop('checked', t);
					$('#chk-'+parent_id).attr('disabled', t);
				}
			}
			//
			$('#chk-'+parent_id).trigger('change');
		});
		
		$('.form-check-input').trigger('change');
	});
</script>
@endsection