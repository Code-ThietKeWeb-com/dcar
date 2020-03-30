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
				name: {
					required: true,
					minlength: 3
				},
				title: {
					required: true,
					minlength: 3
				}
	    },
	    messages: {
	    	
				name: {
						required: "{LANG.err_text_required}",
						minlength: "{LANG.err_length} 3 {LANG.char}" 
				} ,
				title: {
						required: "{LANG.err_text_required}",
						minlength: "{LANG.err_length} 3 {LANG.char}" 
				} 
		}
	});
});
</script>
{data.err}
<form action="{data.link_action}" method="post" enctype="multipart/form-data" name="myForm" id="myForm"  class="validate">
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td valign="top" style="min-width:600px;" >
    
    <table width="100%"  border="0"  cellspacing="1" cellpadding="1" class="admintable">
     
     
    <tr class="form-required">
			<td class="row1" >{LANG.page_title} : </td>
			<td  align="left" class="row0"><input name="title" id="title"  type="text" size="70" maxlength="250" value="{data.title}" onkeyup="vnTMXH.setTitle(this.value)" ></td>
		</tr>
    
    
    
		<tr >
    	<td class="row1" colspan="2" >
      <p >{LANG.page_content}</p>
      {data.html_content}
      </td>
		</tr>
    
    <tr  >
			<td class="row1" >Là loại trang Popup: </td>
			<td  align="left" class="row0">{data.list_is_popup}</td>
		</tr>
     
 

		<tr align="center">
    <td class="row1" >&nbsp; </td>
			<td class="row0" >
                <input type="hidden" name="csrf_token" id="csrf_token" value="{data.csrf_token}" />
				<input type="hidden" name="do_submit"	 value="1" />
				<input type="submit" name="btnSubmit" value="Submit" class="button">
				<input type="reset" name="btnReset" value="Reset" class="button">
			</td>
		</tr>
	</table>
  
  
     </td>
    <td width="40%" valign="top" style="padding-left:10px;min-width:400px;">
    
    
    <div class="desc">
          <table width="100%"  border="0" cellspacing="2" cellpadding="2" align=center class="admintable desc_title">
            <tr class="row_title" >
              <td  class="font_title" ><img src="{DIR_IMAGE}/toggle_minus.png" alt="bt_add" title="Collapse" align="absmiddle"/> Search Engine Optimization : </td>
            </tr>
          </table>
          <div class="desc_content">
           
           
           
            
<div class="general">
		<h4 class="wpseo-heading" style="display: none;">General</h4>
		<table width="100%"  border="0"  cellspacing="1" cellpadding="1" class="admintable">
			<tr>
      	<td class="row1"><label for="yoast_wpseo_snippetpreview">Snippet Preview:</label></td>
      	<td class="row0"><div id="wpseosnippet">
			<a href="#" class="wpseo_title"></a><br/>
		<a class="wpseo_url"  href="#"></a> 
			<p  class="desc"><span class="content" style="color: rgb(136, 136, 136);"></span></p>
		</div></td>
    
    </tr>
    
    
    <tr>
    	<td  class="row0" ><label ><strong>Friendly URL :</strong></label></td>
      
    <td class="row0"> 
      <input name="friendly_url" id="friendly_url" type="text" size="50" maxlength="250" value="{data.friendly_url}" class="textfield" style="width:98%"  >
      <br>
      <span class="font_err">({LANG.mess_friendly_url})</span>
      <div id="link_seo" style="display:none" >{data.link_seo}</div></td>
  </tr>
              
    
   <tr> 
   <td class="row1"><label for="yoast_wpseo_title">Friendly Title:</label></td>
   <td class="row0"><input type="text" class="textfield" value="{data.friendly_title}" name="friendly_title" id="friendly_title"  style="width:98%"   /><br/><p>Title display in search engines is limited to 70 chars, <span id="friendly_title-length"><span class="good">70</span></span> chars left.<br/></td>
   </tr>
   
   <tr>
   <td class="row1"><label for="metadesc">Meta Description:</label></td>
   <td class="row0"><textarea name="metadesc" id="metadesc" rows="3" class="textarea"  style="width:98%">{data.metadesc}</textarea><p>The <code>meta</code> description will be limited to 155 chars (because of date display), <span id="metadesc-length"><span class="good">155</span></span> chars left. </p><div id="metadesc_notice"></div></td>
   </tr>
   <tr>
   <td class="row1" ><label for="metakey">Meta Keyword:</label></td>
   <td class="row0"><input type="text" class="textfield" value="{data.metakey}" name="metakey"  id="metakey"  style="width:98%"/><br/><p></p><div style="width: 300px;" class="alignright"><p id="related_keywords_heading" style="display: none;">Related keywords:</p><div id="wpseo_tag_suggestions"></div></div>
		</td>
    </tr>
    </table>
</div>
 

 <br>
<div class="results">
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
        </div>
        
        <br/>
         
      
      
      
      <div class="desc">
          <table width="100%"  border="0" cellspacing="2" cellpadding="2" align=center class="admintable desc_title">
            <tr class="row_title" >
              <td  class="font_title" ><img src="{DIR_IMAGE}/toggle_minus.png" alt="bt_add" title="Collapse" align="absmiddle"/> Xem Demo tương tác FaceBook : </td>
            </tr>
            <tr>
              <td class="row0 desc_content"><div class="divFacebook"> {data.img_mxh}
                  <div class="face-info">
                    <div class="title_mxh" id="title_mxh" >{data.friendly_title}</div>
                    <div class="link_mxh" id="link_mxh" >{data.link_mxh}</div>
                    <div class="description_mxh" id="description_mxh" >{data.metadesc}</div>
                  </div>
                </div></td>
            </tr>
          </table>
        </div>
        <br/>
        
        
    </td>
  </tr>
</table>
  

</form>
<br>
<!-- END: edit -->

<!-- BEGIN: manage -->
<div class="box-fillter">
    <div class="well well-sm fillter">
        <form action="{data.link_fsearch}" method="post" name="fSearch" class="form-inline md4">

        </form>

        <div class="div-totals">
            {LANG.totals}: <b class="font_err">{data.totals}</b>
        </div>
        <div  style="position: absolute; right: 10px; top: 5px;"><a class="btn btn-sm btn-primary" href='?mod=page&act=page&sub=rebuild' ><span style="color: #ffffff"><i class="fa fa-retweet" aria-hidden="true"></i> Rebuild Link</span></a></div>

    </div>

</div>


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