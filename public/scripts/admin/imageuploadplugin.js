var checkPass = false;
function val(obj) {
	return document.getElementById(obj);
}
function updateImage(file) {
	var HEIGHT = 280, WIDTH = 480;
	var allowExtention=".jpg,.bmp,.gif,.png";//允许上传文件的后缀名
	var extention=file.value.substring(file.value.lastIndexOf(".")+1).toLowerCase();
	var box = val('preview');
	if(file.files && file.files[0]&&window.FileReader&&allowExtention.indexOf(extention)>-1) {
		var reader = new FileReader();
		reader.onload = function(e) {
			val('imghead').src = e.target.result;
			checkPass = true;
		}
		reader.readAsDataURL(file.files[0]);
	} else {
		alert('亲，你传的不是图片，或者是你用的浏览器不给力哦，不要使用低级的浏览器啦2333--我是younger~');
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