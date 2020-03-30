<?php

/*================================================================================*\
|| 							Name code : skin_rss.php 		 		 													  # ||
||  				Copyright © 2007 by Thai Son - CMS vnTRUST                					# ||
\*================================================================================*/
/**
 * @version : 1.0
 * @date upgrade : 11/12/2007 by Thai Son
 **/
class skin_rss
{
  var $dirMod = "modules/rss/";
  var $dirImage = "modules/rss/images";

  function html_main ($data)
  {
    global $input, $conf, $vnT, $DB, $func;
    return <<<EOF
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td width="170" valign="top" id="block_left">{$data['box_left']}</td>
    <td id="block_middle" valign="top">
		
		
		<table width="100%" border="0" cellspacing="2" cellpadding="2">
  <tr>
    <td>RSS (vi&#7871;t t&#7855;t t&#7915; Really Simple Syndication) l&agrave; m&#7897;t ti&ecirc;u chu&#7849;n &#273;&#7883;nh d&#7841;ng  t&agrave;i li&#7879;u d&#7921;a tr&ecirc;n XML nh&#7857;m gi&uacute;p ng&#432;&#7901;i s&#7917; d&#7909;ng d&#7877; d&agrave;ng c&#7853;p nh&#7853;t v&agrave; tra  c&#7913;u th&ocirc;ng tin m&#7897;t c&aacute;ch nhanh ch&oacute;ng v&agrave; thu&#7853;n ti&#7879;n nh&#7845;t b&#7857;ng c&aacute;ch t&oacute;m  l&#432;&#7907;c th&ocirc;ng tin v&agrave;o trong m&#7897;t &#273;o&#7841;n d&#7919; li&#7879;u ng&#7855;n g&#7885;n, h&#7907;p chu&#7849;n. D&#7919; li&#7879;u  n&agrave;y &#273;&#432;&#7907;c c&aacute;c ch&#432;&#417;ng tr&igrave;nh &#273;&#7885;c tin chuy&ecirc;n bi&#7879;t (g&#7885;i l&agrave; News reader) ph&acirc;n  t&iacute;ch v&agrave; hi&#7875;n th&#7883; tr&ecirc;n m&aacute;y t&iacute;nh c&#7911;a ng&#432;&#7901;i s&#7917; d&#7909;ng. Tr&ecirc;n tr&igrave;nh &#273;&#7885;c tin  n&agrave;y, ng&#432;&#7901;i s&#7917; d&#7909;ng c&oacute; th&#7875; th&#7845;y nh&#7919;ng tin ch&iacute;nh m&#7899;i nh&#7845;t, ti&ecirc;u &#273;&#7873;, t&oacute;m  t&#7855;t v&agrave; c&#7843; &#273;&#432;&#7901;ng link &#273;&#7875; xem t&ograve;an b&#7897; tin.</td>
  </tr>
  <tr>
    <td><strong>C&aacute;c k&ecirc;nh RSS cung c&#7845;p</strong></td>
  </tr>
  <tr>
    <td style="padding-left:5px;"><table  border="0" cellspacing="2" cellpadding="2" >

	  {$data['row_rss']}
    </table></td>
  </tr>
  <tr>
    <td><strong>C&aacute;c gi&#7899;i h&#7841;n s&#7917; d&#7909;ng</strong></td>
  </tr>
  <tr>
    <td><p>C&aacute;c ngu&#7891;n k&ecirc;nh tin &#273;&#432;&#7907;c cung c&#7845;p mi&#7877;n ph&iacute; cho c&aacute;c c&aacute; nh&acirc;n v&agrave; c&aacute;c t&#7893;  ch&#7913;c phi l&#7907;i nhu&#7853;n. Ch&uacute;ng t&ocirc;i y&ecirc;u c&#7847;u b&#7841;n cung c&#7845;p r&otilde; c&aacute;c th&ocirc;ng tin c&#7847;n  thi&#7871;t khi b&#7841;n s&#7917; d&#7909;ng c&aacute;c ngu&#7891;n k&ecirc;nh tin n&agrave;y t&#7915; <strong>{$_SERVER['HTTP_HOST']}</strong>.</p>
      <p> <strong>{$_SERVER['HTTP_HOST']}</strong> c&oacute; quy&#7873;n y&ecirc;u c&#7847;u b&#7841;n ng&#7915;ng cung c&#7845;p v&agrave; ph&acirc;n t&aacute;n th&ocirc;ng tin  d&#432;&#7899;i d&#7841;ng n&agrave;y &#7903; b&#7845;t k&#7923; th&#7901;i &#273;i&#7875;m n&agrave;o v&agrave; v&#7899;i b&#7845;t k&#7923; l&yacute; do n&agrave;o.</p></td>
  </tr>
</table>
		
		</td>
    <td width="170" valign="top" id="block_right">{$data['box_right']}</td>
  </tr>
</table>


EOF;
  }
  //end class
}
?>