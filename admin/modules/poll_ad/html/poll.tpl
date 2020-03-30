
<!-- BEGIN: edit -->
<script type="text/javascript">
	/* <![CDATA[*/
	
	var count_poll_answer = {data.poll_noquestion} ;
	function create_poll_answer() {
		// Create Elements
		var poll_tr = document.createElement("tr");
		var poll_td1 = document.createElement("td");
		var poll_td2 = document.createElement("td");
		var poll_answer = document.createElement("input");
		var poll_answer_count = document.createTextNode("Option " + (count_poll_answer+1)+" :");
		var poll_option = document.createElement("option");
		var poll_option_text = document.createTextNode((count_poll_answer+1));
		count_poll_answer++;

		// Elements - Input
		poll_answer.setAttribute('type', "text");
		poll_answer.setAttribute('name', "optionText[]");
		poll_answer.setAttribute('size', "50");

		// Elements - TD/TR
		poll_tr.setAttribute('id', "poll-answer-" + count_poll_answer);
		poll_td1.setAttribute('class', "row1");
		poll_td2.setAttribute('class', "row0");
		
		// Appending
		poll_tr.appendChild(poll_td1);
		poll_tr.appendChild(poll_td2);
		poll_td1.appendChild(poll_answer_count);
		poll_td2.appendChild(poll_answer);
		poll_option.appendChild(poll_option_text);
		document.getElementById("poll_answers").appendChild(poll_tr);

	}
	function remove_poll_answer() {
		if(count_poll_answer == 2) {
			alert("You need at least a minimum of 2 poll answers.");
		} else {
			var num_old = parseInt(document.getElementById('num_old').value);
			if(count_poll_answer > num_old){
				document.getElementById("poll_answers").removeChild(document.getElementById("poll-answer-" + count_poll_answer));
				count_poll_answer--;
			}			
			
		}
	}
	
	function delete_poll_ans(poll_id, op_id, op_order, poll_confirm) {
		delete_poll_ans_confirm = confirm(poll_confirm);
		if(delete_poll_ans_confirm) {
			$.ajax({
			 type: "POST",
			 url: "modules/poll_ad/ajax/ajax_poll.php?do=del_option",
			 data: "poll_id="+poll_id+"&op_id="+op_id+"&op_order="+op_order,
			 success: function(msg){
				 $("span#message_poll").html( msg );
				 document.getElementById("poll_answers").removeChild(document.getElementById("poll-answer-" + op_order));
				 document.getElementById('num_old').value = parseInt(document.getElementById('num_old').value) -1 ;
			 }
		 });
		}
	}
	


	/* ]]> */
</script>

{data.err}
<form action="{data.link_action}" method="post" enctype="multipart/form-data" name="myForm"  class="validate">
  <table width="100%"  border="0"  cellspacing="1" cellpadding="1" class="admintable">
		<tr class="form-required">
      <td width="25%" class="row1" >{LANG.poll_title} : </td>
      <td class="row0"><input name="pollerTitle" type="text" size="70" maxlength="250" value="{data.pollerTitle}"></td>
    </tr>
		<tr >
     <td class="row1" >{LANG.picture}: </td>
     <td class="row0">
     	{data.pic}
      <input name="chk_upload" type="radio" value="0" checked> 
      Insert URL's image &nbsp; <input name="picture" type="text" size="50" maxlength="250" > <br>
      <input name="chk_upload" type="radio" value="1"> Upload Picture &nbsp;&nbsp;&nbsp;
      <input name="image" type="file" id="image" size="30" maxlength="250">
    </td>
    </tr>
    <tr>
      <td width="25%" class="row1" >{LANG.multiple} : </td>
      <td class="row0">{data.list_multiple}</td>
    </tr>
       
    <tr>
     <td colspan="2" class="row0"><hr noshade></td>
    </tr>
     <tr class="row_title">
      <td colspan="2"><strong>{LANG.poll_option}</strong></td>
    </tr>
    <tbody id="poll_answers">
    <!-- BEGIN: html_rowold -->
    <tr id="poll-answer-{row.stt}" >
     <td class="row1">Option {row.stt} : </td>
     <td class="row0"><input name="answers[{row.op_id}]" type="text" size="50" maxlength="250" value="{row.optionText}"> {row.btn_del} &nbsp;&nbsp;&nbsp; <strong>Vote</strong> &nbsp;<input name="votes[{row.op_id}]" type="text" size="10"  value="{row.vote}"></td>
 		</tr>
    <!-- END: html_rowold -->
    
    <!-- BEGIN: html_row -->
    <tr id="poll-answer-{row.stt}" >
     <td class="row1">Option {row.stt} : </td>
     <td class="row0"><input name="optionText[]" type="text" size="50" maxlength="250" value="{row.optionText}"></td>
 		</tr>
    <!-- END: html_row -->
    
    </tbody>
    
    <tr height="20">
      <td class="row1" >&nbsp; </td>
      <td class="row0"><input type="button" value="Add Answer" onclick="create_poll_answer();" class="button" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="button" value="Remove Answer" onclick="remove_poll_answer();" class="button" /></td>
    </tr>
		<tr align="center">
    <td class="row1">&nbsp; </td>
			<td class="row0"><span id="message_poll"></span></td>
		</tr>
	</table>
  <br />

  <p align="center">
  	<input type="hidden" name="num_old" id="num_old" value="{data.num_old}" >
    <input type="hidden" name="num" id="num" size="5" value="{data.num}" >
				<input type="hidden" name="do_submit"	 value="1" />
				<input type="submit" name="btnAdd" value="Submit" class="button">
				<input type="reset" name="Submit2" value="Reset" class="button">
  </p>
</form>
<br>
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
<br />
{data.table_list}
<br />
<table width="100%"  border="0" align="center" cellspacing="1" cellpadding="1" class="bg_tab">
  <tr>
    <td  height="25">{data.nav}</td>
  </tr>
</table>
<br />
<!-- END: manage -->