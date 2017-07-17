$(function(){
    $('body .dropzone_upload').each(function(){
        init_dropzone($(this));
    });
});

function init_dropzone($dropzone) {
    var $dropzoneWrapper = $dropzone.closest('.upload-photo'),
        $imgContainer    = $dropzoneWrapper.find('.upload-photo-items'),
        $errorsPlace     = $dropzoneWrapper.find('.upload-photo-errors'),
        type             = $dropzone.attr('data-type'),
        subtype          = $dropzone.attr('data-subtype'),
        max_size         = $dropzone.attr('data-file-size'),
        max_files        = $dropzone.attr('data-max-files'),
        $preloader       = $('#' + type + '_' + subtype + '_preloader')
        ;

    $dropzone.dropzone({
        url: Routing.generate('photo_upload_dropzone'),
        acceptedFiles: 'image/jpeg,image/png',
        previewTemplate: '<div></div>',
        maxFiles: max_files,
        maxFilesize: max_size,
        dictFileTooBig: 'File is too big ({{filesize}}MB). Max file size is {{maxFilesize}}MB.',
        clickable: "#" + $dropzone.attr('id') + '_button',
        init: function() {
            this
                .on("addedfile", function(file){
                    if ($dropzoneWrapper.find('.upload-photo-item img').length >= max_files) {
                        this.removeFile(file);
                        $errorsPlace.append('<p>You can not upload any more files.</p>');
                    } else {
                        $preloader.removeClass('hidden');
                    }
                })
                .on("sending", function(file, xhr, formData){
                    formData.append("type", type);
                    formData.append("subtype", subtype);
                })
                .on("queuecomplete", function(file){
                    $preloader.addClass('hidden');
                })

        },
        success: function(file, response) {
            if (response.success) {
                var img   = $dropzone.attr('data-prototype'),
                    index = $dropzoneWrapper.find('.upload-photo-item img').length + 1;

                img = img.replace('__upload_src__', response.data.path);
                img = img.replace(/__name__/g, index);

                $imgContainer.append(img);

                $imgContainer.children().last().find('input[type=hidden]').val(response.data.id);
            } else {
                $.each(response.errors, function(k, error){
                    $errorsPlace.append('<p>' + error + '</p>');
                });
            }

            this.removeFile(file);
        },
        error: function(file, error) {
            this.removeFile(file);
            $errorsPlace.append('<p>' + error + '</p>');
        }
    });

    $imgContainer.on('click', 'i.upload-photo-del', function(){
        $(this).parent().remove();
    });

    $("#" + $dropzone.attr('id') + '_button').on('click', function(){
        $errorsPlace.html('');
    });
}