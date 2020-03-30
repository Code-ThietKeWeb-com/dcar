<?php
/*================================================================================*\
|| 							Name code : funtions_statistics.php 		 	          	     		  # ||
||  				Copyright © 2007 by Thai Son - CMS vnTRUST                          # ||
\*================================================================================*/
/**
 * @version : 1.0
 * @date upgrade : 11/12/2007 by Thai Son
 **/
if (! defined('IN_vnT')) {
  die('Hacking attempt!');
}



//=================List_Type===============
function List_Type ($did)
{
  global $func, $DB, $conf, $vnT;
  $text = "<select size=1 name=\"selType\" style='width:20%;'  onChange=\"document.myform.submit();\">";
  if ($did == "day")
    $text .= "<option value=\"day\" selected> " . $vnT->lang['day'] . " </option>";
  else
    $text .= "<option value=\"day\" > " . $vnT->lang['day'] . "</option>";
  if ($did == "month")
    $text .= "<option value=\"month\" selected> " . $vnT->lang['month'] . " </option>";
  else
    $text .= "<option value=\"month\"> " . $vnT->lang['month'] . " </option>";
  if ($did == "year")
    $text .= "<option value=\"year\" selected>" . $vnT->lang['year'] . " </option>";
  else
    $text .= "<option value=\"year\"> " . $vnT->lang['year'] . "</option>";
  $text .= "</select>";
  return $text;
}

//---------------------------List_Thang
function List_Thang ($month)
{
  global $func, $DB, $conf, $vnT;
  $text = "<select size=\"1\" name=\"month\" id=\"month\" >";
  for ($i = 1; $i <= 12; $i ++) {
    if ($i < 10)
      $value = "0" . $i;
    else
      $value = $i;
    if ($i == $month) {
      $text .= '<option value="' . $value . '" selected>' . $value . '</option>';
    } else {
      $text .= '<option value="' . $value . '">' . $value . '</option>';
    }
  }
  $text .= "</select>";
  return $text;
}

//---------------------------List_Nam
function List_Nam ($year)
{
  global $func, $DB, $conf, $vnT;
  $toYear = date("Y");
  $text = "<select size=\"1\" name=\"year\" id=\"year\" >";
  for ($i = 2000; $i <= $toYear; $i ++) {
    if ($i == $year) {
      $text .= '<option value="' . $i . '" selected> ' . $i . ' </option>';
    } else {
      $text .= '<option value="' . $i . '"> ' . $i . ' </option>';
    }
  }
  $text .= "</select>";
  return $text;
}

//=============
function get_prozent ($zahl, $max)
{
  $prozent = 0;
  if ($zahl > 0) {
    $prozent = number_format(round((100 / $max) * $zahl, 2), 2);
  }
  return $prozent;
}
?>