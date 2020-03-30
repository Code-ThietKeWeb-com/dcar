<!-- BEGIN: manage -->
<form action="{data.link_action}" method="post" name="f_config" id="f_config" >
{data.err}

 <table width="100%"  border="0"  cellspacing="1" cellpadding="1" class="admintable">

  <tr height=20 class="row_title" > 
   <td colspan="2" ><strong>{LANG.setting_facebook}</strong> <a href="https://developers.facebook.com/apps/" target="_blank">(Create a new Facebook application)</a></td>
  </tr>
   
   
   <tr  >
     <td  align="right" class="row1" width="25%" > <strong>Facebook :</strong></td>
     <td align="left" class="row0" >{data.list_facebook}</td>
    </tr>
    
    <tr  >
     <td  align="right" class="row1" > <strong>App ID/API Key :</strong></td>
     <td align="left" class="row0" ><input name="facebook_appId" type="text"  size="70" maxlength="250"  value="{data.facebook_appId}"/></td>
    </tr>
    
  	<tr  >
     <td  align="right" class="row1" > <strong>App Secret :</strong></td>
     <td align="left" class="row0" ><input name="facebook_secret" type="text"  size="70" maxlength="250" value="{data.facebook_secret}" /></td>
    </tr>


     <tr  >
         <td  align="right" class="row1" > <strong>Uer or Page ID :</strong></td>
         <td align="left" class="row0" ><input name="facebook_id" type="text"  size="70" maxlength="250" value="{data.facebook_id}" />
            <div style="padding-top: 5px;">
             <span class="font_small">Get Access Token at:  <a href="https://developers.facebook.com/tools/explorer/" target="_blank">https://developers.facebook.com/tools/explorer/</a>  </span> &nbsp; &nbsp; | &nbsp; &nbsp; <a href="https://www.facebook.com/dialog/permissions.request?_path=permissions.request&app_id={data.facebook_appId}&perms=user_birthday,user_hometown,user_website,user_about_me,user_posts,email,publish_actions,read_custom_friendlists,public_profile&redirect_uri={data.redirect_uri}"  class="button"<span style="color: #ffffff">Cài đặt mã tương thích</span></a>
</div>


         </td>
     </tr>
    <tr >
    <td align="right" class="row1"> <strong>Access Token : </strong></td>
    <td  align="left" class="row0"><input name="facebook_access_token" type="text"  size="50" maxlength="250" value="{data.facebook_access_token}" style="width: 99%" />  <div style="padding-top: 5px;"><span class="font_small">Get Access Token at:  <a href="https://developers.facebook.com/tools/accesstoken/" target="_blank">https://developers.facebook.com/tools/accesstoken/</a>  </span> &nbsp; &nbsp; | &nbsp; &nbsp; <a href="https://developers.facebook.com/tools/debug/accesstoken?access_token={data.facebook_access_token}"  class="button" target="_blank"><span style="color: #ffffff">Kiểm tra mã truy cập</span></a></div>
    </td>
  </tr>
    <tr  >
     <td  align="right" class="row1" > <strong>Link Facebook Like :</strong></td>
     <td align="left" class="row0" ><input name="facebook_linklike" type="text"  size="70" maxlength="250"  value="{data.facebook_linklike}"/></td>
    </tr>
    
    <tr  >
     <td  align="right" class="row1" > <strong>Link Fans Page :</strong></td>
     <td align="left" class="row0" ><input name="facebook_page" type="text"  size="70" maxlength="250"  value="{data.facebook_page}"/></td>
    </tr>

     <tr >
         <td align="right" class="row1"> <strong>Picture : </strong></td>
         <td  align="left" class="row0">


             <div class="input-group" style="max-width: 500px;">
                 <input type="text" name="social_network_picture"  id="social_network_picture"    class="form-control"  value="{data.social_network_picture}" />
                 <div class="input-group-btn"><button type="button" class="button btnBrowseMedia" value="Browse server" data-obj="social_network_picture" data-mod="" data-folder="File/Image" data-type="file" ><span class="img">Image</span></button></div>
             </div>

         </td>
     </tr>


  <tr height=20 class="row_title" > 
   <td colspan="2" ><strong>{LANG.setting_google}</strong>  <a href="https://code.google.com/apis/console/" target="_blank">(Create a new API Key)</a></td>
  </tr>
   
   
   <tr  >
     <td  align="right" class="row1" > <strong>Google Plus :</strong></td>
     <td align="left" class="row0" >{data.list_google}</td>
    </tr>
   <tr  >
     <td  align="right" class="row1" > <strong>Google API Key :</strong></td>
     <td align="left" class="row0" ><input name="google_apikey" type="text"  size="70" maxlength="250" value="{data.google_apikey}" /></td>
    </tr> 
    
    <tr  >
     <td  align="right" class="row1" > <strong>Google secret :</strong></td>
     <td align="left" class="row0" ><input name="google_secret" type="text"  size="70" maxlength="250" value="{data.google_secret}" /></td>
    </tr>
    
     <tr  >
     <td  align="right" class="row1" > <strong>Link Google+ Like :</strong></td>
     <td align="left" class="row0" ><input name="google_linklike" type="text"  size="70" maxlength="250"  value="{data.google_linklike}"/></td>
    </tr>
    
     <tr  >
     <td  align="right" class="row1" > <strong>Link Google+ Badge :</strong></td>
     <td align="left" class="row0" ><input name="google_badge" type="text"  size="70" maxlength="250"  value="{data.google_badge}"/></td>
    </tr>
 
 
 
  <tr height=20 class="row_title" > 
   <td colspan="2" ><strong>{LANG.setting_twitter}</strong> <a href="https://dev.twitter.com/apps" target="_blank">(Create a new Twitter application)</a></td>
  </tr>
   
   
   <tr  >
     <td  align="right" class="row1" width="25%" > <strong>Twitter :</strong></td>
     <td align="left" class="row0" >{data.list_twitter}</td>
    </tr>
    
    <tr  >
     <td  align="right" class="row1" > <strong>Consumer key :</strong></td>
     <td align="left" class="row0" ><input name="twitter_apikey" type="text"  size="70" maxlength="250"  value="{data.twitter_apikey}"/></td>
    </tr>
    
  	<tr  >
     <td  align="right" class="row1" > <strong>Consumer secret :</strong></td>
     <td align="left" class="row0" ><input name="twitter_secret" type="text"  size="70" maxlength="250" value="{data.twitter_secret}" /></td>
    </tr>
    
    <tr  >
     <td  align="right" class="row1" > <strong>Link Twitter Like (Share):</strong></td>
     <td align="left" class="row0" ><input name="twitter_linklike" type="text"  size="70" maxlength="250"  value="{data.twitter_linklike}"/></td>
    </tr>
    
    <tr  >
     <td  align="right" class="row1" > <strong>Link Twitter Follow :</strong></td>
     <td align="left" class="row0" ><input name="twitter_page" type="text"  size="70" maxlength="250"  value="{data.twitter_page}"/></td>
    </tr>
     
    
      <tr >
       <td  class="row1" >&nbsp;</td>
        <td  class="row0">
            <input type="hidden" name="csrf_token" id="csrf_token" value="{data.csrf_token}" />
  <input type="hidden" name="num" value="{data.num}">
  <input type="hidden" name="do_submit" value="1">
   <input type="submit" name="btnEdit" value="Update >>" class="button"> </td>
  </tr>
    </table>
</form>
		
<div style="padding:10px;">    
	<h3 class="font_title" style="padding-bottom:10px;">TÀI LIỆU THAM KHẢO</h3>
  <div class="list" style="padding-left:10px;">
  	<ul style="list-style:none; margin-left:10px;">
    	<li style="padding:2px;list-style-type:square"><a href="http://developers.facebook.com/docs/plugins/" target="_blank">Tài liệu tham khảo cho Facebook</a></li>
      <li  style="padding:2px;list-style-type:square"><a href="https://developers.google.com/+/web/+1button/" target="_blank">Tài liệu tham khảo cho Google Plus</a></li>
      <li  style="padding:2px;list-style-type:square"><a href="https://dev.twitter.com/docs/tweet-button" target="_blank">Tài liệu tham khảo cho Twitter</a></li>
    </ul>
  </div>
</div>

<br />
<!-- END: manage -->