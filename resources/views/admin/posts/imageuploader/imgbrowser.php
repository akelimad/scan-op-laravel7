<!-- Copyright (c) 2015, Fujana Solutions - Moritz Maleck. All rights reserved. -->
<!-- For licensing, see LICENSE.md -->

<?php

// Including the plugin config file, don't delete the following row!
require (__DIR__ . '/pluginconfig.php');
// Including the functions file, don't delete the following row!
require (__DIR__ . '/function.php');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=no">
    <title>Image Browser for CKEditor</title>
    <link rel="icon" href="<?php echo asset('/packages/imageuploader/img/cd-ico-browser.ico'); ?>">
    
    <link rel="stylesheet" href="<?php echo asset('/packages/imageuploader/styles.css'); ?>">
    
    <script src="<?php echo asset('/js/vendor/jquery-1.11.0.min.js'); ?>"></script>
    <script src="<?php echo asset('/packages/imageuploader/dist/jquery.lazyload.min.js'); ?>"></script>
    <script src="<?php echo asset('/packages/imageuploader/dist/js.cookie-2.0.3.min.js'); ?>"></script>
    
    <script src="<?php echo asset('/packages/imageuploader/dist/sweetalert.min.js'); ?>"></script>
    <link rel="stylesheet" type="text/css" href="<?php echo asset('/packages/imageuploader/dist/sweetalert.css'); ?>">
    
    <script src="<?php echo asset('/packages/imageuploader/function.js'); ?>"></script>
    
</head>
<body ontouchstart="">
	
<div id="header">
    <img onclick="Cookies.remove('qEditMode');window.close();" src="<?php echo asset('/packages/imageuploader/img/cd-icon-close-grey.png'); ?>" class="headerIconRight iconHover">
    <img onclick="reloadImages();" src="<?php echo asset('/packages/imageuploader/img/cd-icon-refresh.png'); ?>" class="headerIconRight iconHover">
    <img onclick="uploadImg();" src="<?php echo asset('/packages/imageuploader/img/cd-icon-upload-grey.png'); ?>" class="headerIconCenter iconHover">
</div>
    
<div id="editbar">
    <div id="editbarView" onclick="#" class="editbarDiv"><img src="<?php echo asset('/packages/imageuploader/img/cd-icon-images.png'); ?>" class="editbarIcon editbarIconLeft"><p class="editbarText">View</p></div>
    <a href="#" id="editbarDownload" download><div class="editbarDiv"><img src="<?php echo asset('/packages/imageuploader/img/cd-icon-download.png'); ?>" class="editbarIcon editbarIconLeft"><p class="editbarText">Download</p></div></a>
    <div id="editbarUse" onclick="#" class="editbarDiv"><img src="<?php echo asset('/packages/imageuploader/img/cd-icon-use.png'); ?>" class="editbarIcon editbarIconLeft"><p class="editbarText">Use</p></div>
    <div id="editbarDelete" onclick="#" class="editbarDiv"><img src="<?php echo asset('/packages/imageuploader/img/cd-icon-qtrash.png'); ?>" class="editbarIcon editbarIconLeft"><p class="editbarText">Delete</p></div>
    <img onclick="hideEditBar();" src="<?php echo asset('/packages/imageuploader/img/cd-icon-close-black.png'); ?>" class="editbarIcon editbarIconRight">
</div>
    
<div id="updates" class="popout"></div>
    
<div id="dropzone" class="dropzone" 
     ondragenter="return false;"
     ondragover="return false;"
     ondrop="drop(event)">
    <p>
        <img src="<?php echo asset('/packages/imageuploader/img/cd-icon-upload-big.png'); ?>"><br>
        Drop your files here
    </p>
</div>

<p class="folderInfo">
	In total: <span id="finalcount"></span> Images - <span id="finalsize"></span>
</p>

<div id="files">
    <?php
	loadImages();
    ?>
</div>

<div id="imageFullSreen" class="lightbox popout">
    <div class="buttonBar">
        <button id="imageFullSreenClose" class="headerBtn" onclick="$('#imageFullSreen').hide(); $('#background').slideUp(250, 'swing');"><img src="<?php echo asset('/packages/imageuploader/img/cd-icon-close.png'); ?>" class="headerIcon"></button>
        <button class="headerBtn" id="imgActionDelete"><img src="<?php echo asset('/packages/imageuploader/img/cd-icon-delete.png'); ?>" class="headerIcon"></button>
        <a href="#" id="imgActionDownload" download><button class="headerBtn"><img src="<?php echo asset('/packages/imageuploader/img/cd-icon-download.png'); ?>" class="headerIcon"></button></a>
        <button class="headerBtn greenBtn" id="imgActionUse" onclick="#" class="imgActionP"><img src="<?php echo asset('/packages/imageuploader/img/cd-icon-use.png'); ?>" class="headerIcon"> Use</button>
    </div><br><br>
    <img id="imageFSimg" src="#" style="#"><br>
</div>
    
<div id="uploadImgDiv" class="lightbox popout">
    <div class="buttonBar">
        <button class="headerBtn" onclick="$('#uploadImgDiv').hide(); $('#background2').slideUp(250, 'swing');"><img src="<?php echo asset('/packages/imageuploader/img/cd-icon-close.png'); ?>" class="headerIcon"></button>
        <button class="headerBtn greenBtn" name="submit" onclick="$('#uploadImgForm').submit();"><img src="<?php echo asset('/packages/imageuploader/img/cd-icon-upload.png'); ?>" class="headerIcon"> Upload</button>
    </div><br><br><br>
    <form action="<?php echo action('PostController@uploadImage') ?>" method="post" enctype="multipart/form-data" id="uploadImgForm" onsubmit="return checkUpload();">
        <p class="uploadP"><img src="<?php echo asset('/packages/imageuploader/img/cd-icon-select.png'); ?>" class="headerIcon"> Please select a file:</p>
        <input type="file" name="upload" id="upload">
        <br>
        <br>
        <input type="hidden" id="CKEditorFuncNum" name="CKEditorFuncNum" value="<?php echo isset($_GET['CKEditorFuncNum'])?$_GET['CKEditorFuncNum']:$CKEditorFuncNum; ?>" />
    </form>
</div>
  
<div id="background" class="background" onclick="$('#imageFullSreenClose').trigger('click');"></div>
<div id="background2" class="background" onclick="$('#uploadImgDiv').hide(); $('#background2').slideUp(250, 'swing');"></div>
<div id="background3" class="background" onclick="$('#settingsDiv').hide(); $('#background3').slideUp(250, 'swing');"></div>

<script>
	// delete image
    function deleteImage(imgSrc) {
    	if (!confirm("Are you sure to delete this image?")) {return false;}
    	
        $.ajax({
            url: "<?php echo action('PostController@deletePostImage')?>",
            method: 'POST',
            data: {
                'imgSrc': imgSrc,
            },
            success: function (response) {
                if (response.status == 'ok') {
                    window.location.href=window.location.href;
                }
            },
            error: function (response) {
                alert("Error on delete");
            }
        });
    }
</script>
</body>
</html>
