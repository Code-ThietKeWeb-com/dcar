<!-- BEGIN: edit -->
<script language="javascript" >
  $(document).ready(function() {
    $('#myForm').validate({
      rules: {
        title: {
          required: true,
          minlength: 3
        }
      },
      messages: {

        title: {
          required: "{LANG.err_text_required}",
          minlength: "{LANG.err_length} 3 {LANG.char}"
        }
      }
    });
  });
</script>



<div class="boxForm">
	{data.err}


	<form action="{data.link_action}" method="post" enctype="multipart/form-data" name="myForm" id="myForm" class="validate" >
		<div class="container-fluid">

			<div class="panel with-nav-tabs panel-default ">
				<div class="panel-heading">

					<ul class="nav nav-tabs">
						<li class="active" ><a data-toggle="tab" href="#TabInfo">Thông tin</a></li>
						<li ><a data-toggle="tab" href="#TabContent"><b>{LANG.content_extra}</b></a></li>
					</ul>
				</div>
				<div class="panel-body">
					<div class="tab-content">

						<div id="TabInfo" class="tab-pane fade in active">

							<div class="row"  >


								<div  class="col-md-6 col-xs-12">

									<table width="100%"  border="0"  cellspacing="1" cellpadding="1" class="admintable">


										<tr class="form-required">
											<td class="row1" width="20%" >{LANG.title} : </td>
											<td  align="left" class="row0"><input name="title" id="title" type="text" size="70" maxlength="250" value="{data.title}" class="form-control"></td>
										</tr>

										<tr >
											<td class="row1" >{LANG.company} : </td>
											<td  align="left" class="row0"><input name="company" id="company" type="text" size="70" maxlength="250" value="{data.company}" class="form-control"></td>
										</tr>
										<tr >
											<td class="row1" >{LANG.address} : </td>
											<td  align="left" class="row0"><input name="address" id="address" type="text" size="70" maxlength="250" value="{data.address}" class="form-control"></td>
										</tr>
										<tr >
											<td class="row1" >{LANG.phone} : </td>
											<td  align="left" class="row0"><input name="phone" id="phone" type="text" size="70" maxlength="250" value="{data.phone}" class="form-control"></td>
										</tr>
										<tr >
											<td class="row1" >Fax : </td>
											<td  align="left" class="row0"><input name="fax" id="fax" type="text" size="70" maxlength="250" value="{data.fax}" class="form-control"></td>
										</tr>


									</table>

								</div>
								<div  class="col-md-6 col-xs-12">
									<table width="100%"  border="0"  cellspacing="1" cellpadding="1" class="admintable">


										<tr >
											<td class="row1" >Email: </td>
											<td  align="left" class="row0"><input name="email" id="email" type="text" size="70" maxlength="250" class="form-control" value="{data.email}"></td>
										</tr>
										<tr >
											<td class="row1" >Website: </td>
											<td  align="left" class="row0"><input name="website" id="website" type="text" size="70" maxlength="250" class="form-control" value="{data.website}"></td>
										</tr>


										<tr>
											<td class="row1">QR CODE: </td>
											<td class="row0">
												{data.qrcode}
											</td>
										</tr>

									</table>



								</div>
							</div>

						</div>


						<div id="TabContent" class="tab-pane fade ">
							{data.html_content}
						</div>


					</div>
				</div></div>


		</div>





		<div class="container-fluid" style="margin-top: 15px">
			<div class="row-title"><div class="f-title">Bản đồ </div></div>


			<div class="panel with-nav-tabs panel-default ">
				<div class="panel-heading">

					<ul class="nav nav-tabs nav-maps">
						<li class="active"><a data-toggle="tab" href="#Tab1"><label for="map_type1"><input type="radio" name="map_type" id="map_type1" value="1" {data.checked.1} />Google Maps</label></a></li>
						<li ><a data-toggle="tab" href="#Tab2"><label for="map_type2"><input type="radio" name="map_type" id="map_type2" value="2" {data.checked.2}/>Images Maps</label></a></li>
						<li><a data-toggle="tab" href="#Tab3"><label for="map_type3"><input type="radio" name="map_type" id="map_type3" value="3"  {data.checked.3} />Google Maps Embed</label></a></li>
						<li><a data-toggle="tab" href="#Tab0"><label for="map_type0"><input type="radio" name="map_type" id="map_type0" value="0"  {data.checked.0} />None</label></a></li>
					</ul>
				</div>
				<div class="panel-body">
					<div class="tab-content">
						<div id="Tab1" class="tab-pane fade in active">
							<div id="googleMaps">

								<script type="text/javascript" src="//maps.google.com/maps/api/js?key={CONF.GoogleMapsAPIKey}&language=vi"></script>
								<script language="javascript" >
                                  var defaultPosition = new google.maps.LatLng({data.map_lat},{data.map_lng});
                                  var information	= '{data.map_information}';
                                  var map_lng  = '{data.map_lat}';
                                  var map_lat = '{data.map_lng}';
								</script>
								<script language="javascript" src="{DIR_JS}/google/googlemap.js" ></script>




								<table width="100%" border="0" cellspacing="0" cellpadding="0" class="tbl-step-maps">
									<tr>
										<td><strong>Bước 1 :</strong> Điền thông tin địa chỉ của công ty vào ô địa chỉ => Click nút <strong>"Cập nhật"</strong>.<br>

											<table width="100%"  border="0" cellspacing="2" cellpadding="2" align="center">
												<tr>
													<td width="100" nowrap=""><strong>Nhập địa chỉ : </strong></td>
													<td>

														<div class="input-group"  style="width: 100%;">
															<input type="text" size="60" id="map_address" name="map_address"  value="{data.map_address}"  class="form-control"/>
															<div class="input-group-btn">
																<button type="button" class="btn btn-primary"  value="Cập nhật" onclick="onChangeAddress()" style="width: 100px; text-align: center" >Cập nhật</span></button>
															</div>
														</div>


													</td>
												</tr>
											</table>
										</td>
									</tr>
									<tr>
										<td>

											<strong>Bước 2 :</strong> Kéo nút định vị <img src="http://mt.googleapis.com/vt/icon/name=icons/spotlight/spotlight-poi.png&scale=1" align="absmiddle" width="16" /> đến đúng vị trí công ty trên bản đồ , điền nội dung cần hiển thị trong khung <strong>"Thông tin vị trí"</strong> => Click nút <strong>"Lưu vị trí"</strong>.<br>

											<div id="map" style="width:100%; height:500px;" ></div>
											<div id="tt"></div>
											<input type="hidden" id="map_lat" name="map_lat" value="{data.map_lat}" />
											<input type="hidden" id="map_lng" name="map_lng" value="{data.map_lng}" />
											<input type="hidden" id="map_information" name="map_information" value="{data.map_desc}" />

										</td>

									</tr>
								</table>
							</div>
						</div>
						<div id="Tab2" class="tab-pane fade ">
							<div id="uploadMaps">

								<div class="img-bg"> {data.img_maps} </div>
								<div class="input-group"  style="width: 100%;">
									<input type="text" name="map_picture" id="map_picture" value="{data.map_picture}" class="form-control"    style="width: 100%;"/>
									<div class="input-group-btn">
										<button type="button" class="button btnBrowseMedia" value="Browse server" data-obj="map_picture" data-mod="" data-folder="File/Image" data-type="image"><span class="img"><i class="fa fa-image"></i> Chọn hình</span></button>
									</div>
								</div>

							</div>
						</div>

						<div id="Tab3" class="tab-pane fade">

							<textarea name="map_embed" id="map_embed" style="width: 100%" rows="5" placeholder="" class="form-control">{data.map_embed}</textarea>

							<div class="note" style="padding-top: 5px;">
								Ví Dụ :
								<pre><code class="language-markup">&lt;iframe src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d3919.0830255012024!2d106.64173000000001!3d10.804953!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0xcfcaad1b644c9a7b!2zQ8O0bmcgVHkgVGhp4bq_dCBL4bq_IFdlYiBUUlVTVC52bg!5e0!3m2!1svi!2sus!4v1532922326108" width="100%" height="450" frameborder="0" style="border:0" allowfullscreen &gt;&lt;/iframe&gt;</code></pre>
							</div>
						</div>
						<div id="Tab0" class="tab-pane fade">

							&nbsp;
						</div>
					</div>
				</div></div>





			<script type="text/javascript">

              /* Init */
              jQuery(window).ready(function () {
                $('.nav-maps li').on('click', function () {
                  $(this).find("input").attr('checked','checked') ;
                });
                $(".nav-maps li :input").each( function() {
                  if($(this).attr('checked')){
                    var obj =  $(this).closest('.nav-maps li a')  ;
                    $(obj).trigger("click");
                  }
                });
              });




			</script>

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

<!-- END: edit -->

<!-- BEGIN: manage -->
<form action="{data.link_fsearch}" method="post" name="myform">
<table width="100%" border="0" cellspacing="2" cellpadding="2" align="center" class="tableborder">
  <tr>
    <td width="15%" align="left">{LANG.totals}: &nbsp;</td>
    <td width="85%" align="left"><b class="font_err">{data.totals}</b></td>
  </tr>
  </table>
</form>
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