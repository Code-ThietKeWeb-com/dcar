<!-- BEGIN: edit -->
{data.err}
<div class="boxForm">
	<form action="{data.link_action}" method="post" enctype="multipart/form-data" name="myForm" id="myForm" class="validate" >
		<div class="container-fluid">
			<div class="row-title"><div class="f-title">Thông tin</div></div>


			<div class="row"  >
				<div  class="col-md-6 col-xs-12">

					<table width="100%"  border="0"  cellspacing="1" cellpadding="1" class="admintable">

						<tr >
							<td class="row1" width="130"  nowrap="">{LANG.title}  </td>
							<td class="row0">
								<input name="title" type="text" id="title" size="50" maxlength="250"  class="form-control"  value="{data.title}" />
							</td>
						</tr>
						<tr >
							<td class="row1"  nowrap="">Link : </td>
							<td class="row0">
								<input type="text" name="l_link"  id="l_link"    class="form-control"  value="{data.l_link}" />
							</td>
						</tr>

					</table>

				</div>
				<div  class="col-md-6 col-xs-12">
					<table width="100%"  border="0"  cellspacing="1" cellpadding="1" class="admintable">

						<tr >
							<td class="row1" width="130"  nowrap="">{LANG.position}  <span class="font_err">*</span></td>
							<td class="row0"><select name="pos" id="pos" class="form-control"   >{data.list_pos}</select></td>
						</tr>

						<tr >
							<td class="row1"  >{LANG.type}: </td>
							<td  align="left" class="row0"><select name="type_ad" id="type_ad" class="form-control"   >{data.list_type_ad}</select></td>
						</tr>

					</table>


				</div>
			</div>
		</div>



		<div class="container-fluid" style="margin-top: 15px">
			<div class="row-title"><div class="f-title">Thông tin banner - logo</div></div>


			<div id="ext_type0" class="ext_type">
				<div class="div-img" style="text-align: center; margin-bottom: 15px;">{data.html_img}</div>
				<table width="100%"  border="0"  cellspacing="1" cellpadding="1" class="admintable">
					<tr >
						<td width="130" nowrap="" class="row1">{LANG.logo}  :&nbsp;</td>
						<td align="left" class="row0">
							<div class="input-group">
								<input type="text"  name="picture" id="picture" class="form-control"   value="{data.picture}">
								<div class="input-group-btn"><button type="button" class="button btnBrowseMedia" value="Browse server" data-obj="picture" data-mod="weblink" data-folder="weblink" data-type="image" ><span class="img"><i class="fa fa-image"></i> Chọn hình</span></button></div>
							</div>

						</td>
					</tr>
					<!--<tr >
						<td  nowrap="" class="row1">Mô tả :&nbsp;</td>
						<td align="left" class="row0">
							<textarea name="description" id="description" class="ckeditor">{data.description}</textarea>
						</td>
					</tr>-->
				</table>


			</div>

			<div id="ext_type1" class="ext_type">
				<table width="100%"  border="0"  cellspacing="1" cellpadding="1" class="admintable">
					<tr >
						<td width="130" nowrap="" class="row1">Nội dung :&nbsp;</td>
						<td align="left" class="row0">
							<textarea name="content" id="content" class="ckeditor">{data.content}</textarea>
						</td>
					</tr>
				</table>

			</div>
			<div id="ext_type2" class="ext_type">
				<table width="100%"  border="0"  cellspacing="1" cellpadding="1" class="admintable">
					<tr >
						<td width="130" nowrap="" class="row1">Nội dung Script:&nbsp;</td>
						<td align="left" class="row0">
							<textarea name="script" id="script" rows="5" cols="50" style="width:100%" >{data.script}</textarea>
						</td>
					</tr>
				</table>

			</div>

		</div>

		<div class="container-fluid" style="margin-top: 15px">
			<div class="row-title"><div class="f-title">Thông tin tùy chọn</div></div>

			<div class="row"  >
				<div  class="col-md-6 col-xs-12">

					<table width="100%"  border="0"  cellspacing="1" cellpadding="1" class="admintable">


						<tr >
							<td class="row1" width="130"  nowrap="">{LANG.width}  </td>
							<td class="row0">
								<input name="width" type="text" id="width"  size="10" maxlength="4"   value="{data.width}"  {data.readonly}  /> pixcel
							</td>
						</tr>
						<tr>
							<td class="row1" >{LANG.height}:&nbsp;</td>
							<td align="left" class="row0"><input name="height" type="text" id="height" size="3" maxlength="4" value="{data.height}">&nbsp;pixcel <span class="font_err">{LANG.note_height}</span></td>
						</tr>
						<tr >
							<td class="row1" nowrap >{LANG.target} : </td>
							<td align="left" class="row0">{data.list_target}</td>
						</tr>
						<tr >

							<td class="row1">{LANG.date_post} : </td>
							<td align="left" class="row0" ><input name="date_add" id="date_add" type="text"  size="20" maxlength="250" class="dates" value="{data.date_add}" />&nbsp;<span class="font_err" >(dd/mm/YYYY)</span></td>
						</tr>
						<tr >
							<td class="row1" >{LANG.date_expire}: </td>
							<td align="left" class="row0" ><input name="date_expire" id="date_expire"  type="text"  size="20" class="dates" maxlength="250" value="{data.date_expire}" />&nbsp;<span class="font_err" >(dd/mm/YYYY)</span></td>
						</tr>


					</table>

				</div>
				<div  class="col-md-6 col-xs-12">
					<table width="100%"  border="0"  cellspacing="1" cellpadding="1" class="admintable">

						<tr >
							<td class="row1" width="130" nowrap>{LANG.module_show}:</td>
							<td align="left" class="row0">{data.list_module_show}</td>
						</tr>
						<tr >
							<td class="row1" nowrap>{LANG.display}: </td>
							<td align="left" class="row0">{data.list_display}</td>
						</tr>

					</table>



					</table>
				</div>
			</div>


		</div>



		<div class="form-footer">
			<div class="div-button">
				<input type="hidden" name="csrf_token" id="csrf_token" value="{data.csrf_token}" />
				<input type="hidden" name="do_submit" id="do_submit" value="1" />
				<button type="submit" name="btnSubmit" id="btnSubmit" value="Submit" class="btn btn-primary"><span>Submit</span></button>
				<button type="reset" name="btnReset" id="btnReset" value="Reset" class="btn btn-default"><span>Cancel</span></button>
			</div>
		</div>

	</form>
</div>




<script type='text/javascript'>
  function show_type(tid)
  {
    $(".ext_type").hide();
    $("#ext_type"+tid).show();
  }

  $( 'textarea.ckeditor').each( function() {
    CKEDITOR.replace( $(this).attr('id') ,
      {
        language : 'vi',
        allowedContent: true,
        toolbar : 'Normal',
        autoParagraph :false,
        filebrowserBrowseUrl : ROOT+'admin/?mod=media&act=popup_media&module=&folder=File/Image&type=file',
        filebrowserImageBrowseUrl : ROOT+'admin/?mod=media&act=popup_media&stype=editor&module=&folder=File/Image&type=image',
        filebrowserFlashBrowseUrl : ROOT+'admin/?mod=media&act=popup_media&module=&folder=File/Image&type=flash',
        height : '250px',
        width : '100%'
      });
  });


  $(document).ready(function() {


    $('#myForm').validate({
      rules: {


      },
      messages: {

      }

    });

    show_type($("#type_ad").val());

    $("#type_ad").change(function(){
      show_type($(this).val());
	});

  });


  {data.js_preview}
</script>
<!-- END: edit -->

<!-- BEGIN: manage -->

<div class="box-fillter">
	<div class="well well-sm fillter">
		<form action="{data.link_fsearch}" method="post" name="fSearch" class="form-inline md4">
			<div class="input-group"  >
				<label class="small ng-binding">{LANG.position} </label>
				<select name="pos" id="pos" class="form-control" onchange="submit();" >{data.list_pos}</select>
			</div>
			<div class="input-group"  >
				<label class="small ng-binding">Modules </label>
				<select name="module" id="module" class="form-control">{data.list_module}</select>
			</div>
			<div class="input-group"  >
				<label class="small ng-binding">{LANG.search} </label>

				<div class="s-item">
					<div  class="item col-5">{data.list_search}</div>
					<div  class="item col-7"><input name="keyword" value="{data.keyword}" size="20" type="text" class="form-control" style="width: 100%" /></div>
					<div class="clear"></div>
				</div>

			</div>
			<div class="searchbtn">
				<button type="submit" class="btn btn-primary ng-binding" name="btnGo" value="Search"  ><i class="fa fa-search"></i> Search</button>
			</div>
		</form>
		<div class="div-totals">
			Tổng cộng : <b class="font_err">{data.totals}</b>
		</div>
	</div>

</div>

{data.err}
{data.table_list}
<br />
<table width="100%"  border="0" align="center" cellspacing="1" cellpadding="1" class="bg_tab">
  <tr>
    <td  height="25">{data.nav}</td>
  </tr>
</table>
<br />
<!-- END: manage -->