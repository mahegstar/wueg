/**
 *
 * You can write your JS code here, DO NOT touch the default style file
 * because it will make it harder for you to update.
 * 
 */

"use strict";

// Global error message function
function ErrorMsg(msg) {
  iziToast.error({
    title: 'Error!',
    message: msg,
    position: 'topRight'
  });
}

// Global success message function
function SuccessMsg(msg) {
  iziToast.success({
    title: 'Success!',
    message: msg,
    position: 'topRight'
  });
}

// Function to remove image
function removeImage(id, image, table) {
  if (confirm("Are you sure you want to remove this image?")) {
    $.ajax({
      url: 'removeImage',
      type: "POST",
      data: 'id=' + id + '&image_url=' + image + '&table=' + table,
      success: function(result) {
        if (result == 1) {
          $('#imageView').hide('fast');
          SuccessMsg("Image removed successfully");
        } else {
          ErrorMsg("Unable to remove image");
        }
      }
    });
  }
}