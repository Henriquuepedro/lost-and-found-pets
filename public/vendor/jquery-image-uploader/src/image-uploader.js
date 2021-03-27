/*! Image Uploader - v1.0.0 - 15/07/2019
 * Copyright (c) 2019 Christian Bayer; Licensed MIT */

(function ($) {

    $.fn.imageUploader = function (options) {

        // Default settings
        let defaults = {
            preloaded: [],
            imagesInputName: 'images',
            preloadedInputName: 'preloaded',
            label: 'Arraste e solte arquivos aqui ou clique para adicionar'
        };

        // Get instance
        let plugin = this;

        // Set empty settings
        plugin.settings = {};

        // Plugin constructor
        plugin.init = function () {

            // Define settings
            plugin.settings = $.extend(plugin.settings, defaults, options);

            // Run through the elements
            plugin.each(function (i, wrapper) {

                // Create the container
                let $container = createContainer();

                // Append the container to the wrapper
                $(wrapper).append($container);

                // Set some bindings
                $container.on("dragover", fileDragHover.bind($container));
                $container.on("dragleave", fileDragHover.bind($container));
                $container.on("drop", fileSelectHandler.bind($container));

                // If there are preloaded images
                if (plugin.settings.preloaded.length) {

                    // Change style
                    $container.addClass('has-files');

                    // Get the upload images container
                    let $uploadedContainer = $container.find('.uploaded');

                    // Set preloaded images preview
                    for (let i = 0; i < plugin.settings.preloaded.length; i++) {
                        $uploadedContainer.append(createImg(plugin.settings.preloaded[i].src, plugin.settings.preloaded[i].id, true));
                    }

                }

            });

        };


        let dataTransfer = new DataTransfer();

        let createContainer = function () {

            // Create the image uploader container
            let $container = $('<div>', {class: 'image-uploader'}),

                // Create the input type file and append it to the container
                $input = $('<input>', {
                    type: 'file',
                    id: plugin.settings.imagesInputName + '-' + random(),
                    name: plugin.settings.imagesInputName + '[]',
                    multiple: '',
                    accept: "image/png, image/jpeg, image/jpg",
                    title: "Extensões permitidas para envio de images: *.jpg, *.jpeg, *.png."
                }).appendTo($container),

                // Create the uploaded images container and append it to the container
                $uploadedContainer = $('<div>', {class: 'uploaded'}).appendTo($container),

                // Create the text container and append it to the container
                $textContainer = $('<div>', {
                    class: 'upload-text'
                }).appendTo($container),

                // Create the icon and append it to the text container
                $i = $('<i>', {class: 'material-icons', text: 'cloud_upload'}).appendTo($textContainer),

                // Create the text and append it to the text container
                $span = $('<span>', {text: plugin.settings.label}).appendTo($textContainer);


            // Listen to container click and trigger input file click
            $container.on('click', function (e) {
                // Prevent browser default event and stop propagation
                prevent(e);

                // Trigger input click
                $input.trigger('click');
            });

            // Stop propagation on input click
            $input.on("click", function (e) {
                e.stopPropagation();
            });

            // Listen to input files changed
            $input.on('change', fileSelectHandler.bind($container));

            return $container;
        };


        let prevent = function (e) {
            // Prevent browser default event and stop propagation
            e.preventDefault();
            e.stopPropagation();
        };

        let createImg = function (src, id) {
            // const thisOld = Number.isInteger(id) ? false : id.includes('old');
            //
            // id = thisOld ? parseInt(id.split('_')[1]) - 1 : id;

            // Create the upladed image container
            let $container = $('<div>', {class: 'uploaded-image'}),

                // Create the img tag
                $img = $('<img>', {src: src}).appendTo($container),


                // Create the primary image button
                $button_pr = $('<button>', {class: 'primary-image'}).appendTo($container);
                // Create the primary image icon
                $('<i>', {class: 'material-icons text-primary', text: 'favorite'}).appendTo($button_pr);

                // Create the delete button
                $button_del = $('<button>', {class: 'delete-image'}).appendTo($container);
                // Create the delete icon
                $('<i>', {class: 'material-icons text-danger', text: 'clear'}).appendTo($button_del);

            // If the images are preloaded
            if (plugin.settings.preloaded.length) {

                // Set a identifier
                $container.attr('data-preloaded', true);

                // Create the preloaded input and append it to the container
                let $preloaded = $('<input>', {
                    type: 'hidden',
                    name: plugin.settings.preloadedInputName + '[]',
                    value: id
                }).appendTo($container)

            } else {

                // Set the identifier
                $container.attr('data-index', id);

            }

            // Stop propagation on click
            $container.on("click", function (e) {
                // Prevent browser default event and stop propagation
                prevent(e);
            });

            // Set delete action
            $button_pr.on("click", function (e) {
                // Prevent browser default event and stop propagation
                prevent(e);
                $('.image-uploader .uploaded .uploaded-image').each(function () {
                    $(this).removeClass('primary-image');
                });
                $(this).parents('.uploaded-image').addClass('primary-image');

                // Define imagem primary para envio do formulário
                if($('input[name="primaryImage"]').length === 1)
                    $('input[name="primaryImage"]').val(id + 1);

            });

            // Set delete action
            $button_del.on("click", function (e) {
                // Prevent browser default event and stop propagation
                prevent(e);

                // If is not a preloaded image
                if ($container.data('index')) {

                    // Get the image index
                    let index = parseInt($container.data('index'));

                    // Update other indexes
                    $container.find('.uploaded-image[data-index]').each(function (i, cont) {
                        if (i > index) {
                            $(cont).attr('data-index', i - 1);
                        }
                    });

                    // Remove the file from input
                    dataTransfer.items.remove(index);
                }

                // If there is no more uploaded files
                if ($container.parents('.uploaded').find('.uploaded-image').length === 1) {

                    // Remove the 'has-files' class
                    $container.parents('.image-uploader').removeClass('has-files');

                }

                // Remove this image from the container
                $container.remove();

                let countPrImgDelete = 0;
                let valuePrImage = 0;
                $('.image-uploader .uploaded .uploaded-image').each(function () {
                    countPrImgDelete++;
                    if($( this).hasClass('primary-image')){
                        if($('input', this).val().includes('old')) valuePrImage = `old_${countPrImgDelete}1`;
                        if(!$('input', this).val().includes('old')) valuePrImage = countPrImgDelete;

                        $('[name="primaryImage"]').val(valuePrImage);
                    }
                });

            });

            return $container;
        };

        let fileDragHover = function (e) {

            // Prevent browser default event and stop propagation
            prevent(e);

            // Change the container style
            if (e.type === "dragover") {
                $(this).addClass('drag-over');
            } else {
                $(this).removeClass('drag-over');
            }
        };

        let fileSelectHandler = function (e) {

            // Prevent browser default event and stop propagation
            prevent(e);

            // Get the jQuery element instance
            let $container = $(this);

            // Change the container style
            $container.removeClass('drag-over');

            // Get the files
            let files = e.target.files || e.originalEvent.dataTransfer.files;

            // Makes the upload
            setPreview($container, files);
        };

        let setPreview = function ($container, files) {

            // Get the upload images container
            let $uploadedContainer = $container.find('.uploaded'),

                // Get the files input
                $input = $container.find('input[type="file"]');

            // Run through the files
            $(files).each(function (i, file) {

                // Add it to data transfer
                dataTransfer.items.add(file);

                // Set preview
                $uploadedContainer.append(createImg(URL.createObjectURL(file), dataTransfer.items.length - 1));

            });

            $('.uploaded-image .delete-image').on('click', function () {
                if($('.image-uploader .uploaded .uploaded-image').length == 0)
                    $('.upload-text').css("border", "1px solid #bf1616");
                else
                    $('.upload-text').css("border", "unset");
            })

            // Update input files
            $input.prop('files', dataTransfer.files);

            if($container.find('.uploaded .uploaded-image').length > 0) {
                // Add the 'has-files' class
                $container.addClass('has-files');

            }

        };

        // Generate a random id
        let random = function () {
            return Date.now() + Math.floor((Math.random() * 100) + 1);
        };

        this.init();

        // Return the instance
        return this;
    };

}(jQuery));
