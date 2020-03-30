<!-- BEGIN: edit -->
<link href="{DIR_JS}/metabox/seo-metabox.css" rel="stylesheet" type="text/css" />
<script type="text/javascript" src="{DIR_JS}/metabox/seo-metabox.js"></script>
<script language="javascript" >
var wpseo_lang = 'en';
var wpseo_meta_desc_length = '155';
var wpseo_title = 'title';
var wpseo_content = 'content';
var wpseo_title_template = '%%title%%';
var wpseo_metadesc_template = '';
var wpseo_permalink_template = '{CONF.rooturl}%postname%.html';
var wpseo_keyword_suggest_nonce = 'a7c4d81c79'; 
			
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
	
	{data.js_preview} 
	
});
</script>
{data.err}

<div class="boxForm">
    <form action="{data.link_action}" method="post" enctype="multipart/form-data" name="myForm" id="myForm" class="validate" >
        <div class="container-fluid">
            <div class="row-title"><div class="f-title">Thông tin</div></div>


            <div class="row"  >


                <div  class="col-md-6 col-xs-12">

                    <table width="100%"  border="0"  cellspacing="1" cellpadding="1" class="admintable">

                        <tr >
                            <td class="row1" width="130"  nowrap="">{LANG.title} <span class="font_err">*</span></td>
                            <td class="row0"> <input type="text" class="form-control required" id="title" name="title" value="{data.title}"    onkeyup="vnTMXH.setTitle(this.value)"  /></td>
                        </tr>



                         <tr >
                            <td class="row1" width="130" >{LANG.picture} </td>
                            <td class="row0">

                                <div id="ext_picture" class="picture" >{data.pic}</div>
                                <input type="hidden" name="picture" id="picture" value="{data.picture}" />
                                <div id="btnU_picture" class="div_upload" {data.style_upload} ><button type="button" class="button btnBrowseMedia" value="Browse server" data-obj="picture" data-mod="{data.module}" data-folder="{data.folder_browse}" data-type="image" ><span class="img"><i class="fa fa-image"></i> Chọn hình</span></button></div>

                            </td>
                        </tr>


                    </table>

                </div>
                <div  class="col-md-6 col-xs-12">
                    <table width="100%"  border="0"  cellspacing="1" cellpadding="1" class="admintable">



                        <tr >
                            <td class="row1" width="130" >{LANG.parent_about}: </td>
                            <td  align="left" class="row0">{data.list_cat}</td>
                        </tr>


                        <tr>
                            <td class="row1" >{LANG.display}</td>
                            <td class="row0">{data.list_display}</td>
                        </tr>

                    </table>



                </div>
            </div>
        </div>






        <div class="container-fluid" style="margin-top: 15px">
            <div class="row-title"><div class="f-title">Thông tin mô tả</div></div>


            <div class="panel with-nav-tabs panel-default ">
                <div class="panel-heading">

                    <ul class="nav nav-tabs">
                        <li class="active" ><a data-toggle="tab" href="#TabDesc">{LANG.content_about}</a></li>
                        <li ><a data-toggle="tab" href="#TabSEO"><b>Search Engine Optimization</b></a></li>
                        <li ><a data-toggle="tab" href="#TabFaceBook"><b>Xem Demo tương tác FaceBook</b></a></li>
                    </ul>
                </div>
                <div class="panel-body">
                    <div class="tab-content">

                        <div id="TabDesc" class="tab-pane fade in active">
                            {data.html_content}
                        </div>

                        <div id="TabSEO" class="tab-pane fade ">
                            <div class="general">
                                <h4 class="wpseo-heading" style="display: none;">General</h4>
                                <table width="100%"  border="0"  cellspacing="1" cellpadding="1" class="admintable">
                                    <tr>
                                        <td class="row1" width="20%"><label for="yoast_wpseo_snippetpreview">Snippet Preview:</label></td>
                                        <td class="row0"><div id="wpseosnippet">
                                                <a href="#" class="wpseo_title"></a><br/>
                                                <a class="wpseo_url"  href="#"></a>
                                                <p  class="desc"><span class="content" style="color: rgb(136, 136, 136);"></span></p>
                                            </div></td>

                                    </tr>


                                    <tr>
                                        <td  class="row0" ><label ><strong>Friendly URL :</strong></label></td>

                                        <td class="row0">
                                            <input name="friendly_url" id="friendly_url" type="text" size="50" maxlength="250" value="{data.friendly_url}" class="form-control"   >
                                            <div class="font_err" style="padding-top: 5px;">({LANG.mess_friendly_url})</div>
                                        </td>
                                    </tr>


                                    <tr>
                                        <td class="row1"><label for="yoast_wpseo_title">Friendly Title:</label></td>
                                        <td class="row0"><input type="text" class="form-control" value="{data.friendly_title}" name="friendly_title" id="friendly_title"     /><br/><p>Title display in search engines is limited to 70 chars, <span id="friendly_title-length"><span class="good">70</span></span> chars left.<br/></td>
                                    </tr>

                                    <tr>
                                        <td class="row1"><label for="metadesc">Meta Description:</label></td>
                                        <td class="row0"><textarea name="metadesc" id="metadesc" rows="3" class="form-control"  >{data.metadesc}</textarea><p>The <code>meta</code> description will be limited to 155 chars (because of date display), <span id="metadesc-length"><span class="good">155</span></span> chars left. </p><div id="metadesc_notice"></div></td>
                                    </tr>
                                    <tr>
                                        <td class="row1" ><label for="metakey">Meta Keyword:</label></td>
                                        <td class="row0"><input type="text" class="form-control" value="{data.metakey}" name="metakey"  id="metakey" /></td>
                                    </tr>
                                </table>
                            </div>
                            <div class="results" style="padding: 10px 0px;">
                                <div id="focuskwresults">
                                    <div class="article_heading">
                                        <div class="label">Article Heading</div><span class="wrong">NO</span></div>
                                    <div class="page_url"><div class="label">Page URL</div><span class="wrong">NO</span></div>
                                    <div class="page_title"><div class="label">Page title</div><span class="wrong">NO</span></div>
                                    <div class="meta_desc"><div class="label">Meta description</div><span class="wrong">NO</span></div>
                                    <div class="content_result"><div class="label">Content</div><span class="wrong">NO</span></div></div>


                                <div class="clear"></div>
                            </div>
                        </div>

                        <div id="TabFaceBook" class="tab-pane fade ">
                            <div class="divFacebook"> {data.img_mxh}
                                <div class="face-info">
                                    <div class="title_mxh" id="title_mxh" >{data.friendly_title}</div>
                                    <div class="link_mxh" id="link_mxh" >{data.link_mxh}</div>
                                    <div class="description_mxh" id="description_mxh" >{data.metadesc}</div>
                                </div>
                            </div>
                        </div>


                    </div>
                </div></div>


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
<div class="box-fillter">
    <div class="well well-sm fillter">
        <form action="{data.link_fsearch}" method="post" name="fSearch" class="form-inline md4">

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