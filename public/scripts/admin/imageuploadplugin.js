var checkPass = false;
function val(obj) {
	return document.getElementById(obj);
}
function updateImage(file) {
	var HEIGHT = 280, WIDTH = 480;
	var allowExtention=".jpg,.bmp,.gif,.png";//允许上传文件的后缀名
	var extention=file.value.substring(file.value.lastIndexOf(".")+1).toLowerCase();
	var box = val('preview');
	var deptid = val('image-deptid').value;
	if(file.files && file.files[0]&&window.FileReader&&allowExtention.indexOf(extention)>-1&&deptid>-1) {
		var reader = new FileReader();
		reader.onload = function(e) {
			val('imghead').src = e.target.result;
			checkPass = true;
		}
		reader.readAsDataURL(file.files[0]);
	} else {
		alert('亲，你没有填写学院信息，或是你传的不是图片，或者是你用的浏览器不给力哦，不要使用低级的浏览器啦2333--我是younger~');
		location.reload();
		checkPass = false;
	}
}
function uploadClick() {
	val('uploadinput').click();
}
function uploadCheck() {
	if(checkPass){
		val('imageForm').submit();
	} else {
		alert('亲，还没有选择图片或者上传文件有错哦~')
	}
}
function uploadCancel(){
	checkPass = false;
	val('imageForm').reset();
	val('imghead').src = '/images/admin/upload-mornal.jpg';
}
function imageDel(imgName, time){
	var XMLHttpReq = null;
	try {  
        XMLHttpReq = new ActiveXObject('Msxml2.XMLHTTP');//IE高版本创建XMLHTTP  
    }  
    catch(E) {  
        try {  
            XMLHttpReq = new ActiveXObject('Microsoft.XMLHTTP');//IE低版本创建XMLHTTP  
        }
        catch(E) {  
            XMLHttpReq = new XMLHttpRequest();//兼容非IE浏览器，直接创建XMLHTTP对象  
        }
    }
    XMLHttpReq.open('post', 'imageajax/method/del', true);
    XMLHttpReq.setRequestHeader("Content-Type","application/x-www-form-urlencoded;charset=UTF-8"); 
    XMLHttpReq.onreadystatechange = function() {
		if (XMLHttpReq.readyState == 4 && XMLHttpReq.status == 200) { 
			if(XMLHttpReq.responseText == 1) {
				alert('删除成功！');
				location.reload();
			} else {alert('删除失败！');}
		}
	}
    XMLHttpReq.send('name='+imgName+'&time='+time);
}
