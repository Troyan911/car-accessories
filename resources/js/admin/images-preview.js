import '../bootstrap'

const imageSelectors = {
    imagesInput: '#images',
    imagesWrapper: '.images-wrapper',
    thumbnailInput: '#thumbnail',
    thumbnailPreview: '#thumbnail-preview'
};

$(document).ready(
    () => {
        if (window.FileReader) {
            $(imageSelectors.imagesInput).change(
                function () {
                    let counter = 0, file;
                    const template = '<div class="mb-4"><img src="__url__" style="width: 100%" /></div>'

                    $(imageSelectors.imagesWrapper).html('');

                    while (file = this.files[counter++]) {
                        const reader = new FileReader();
                        reader.onloadend = (function (){
                            return function(e) {
                                const img = template.replace('__url__', e.target.result);
                                $(imageSelectors.imagesWrapper).append(img)
                            }
                        })(file)
                        reader.readAsDataURL(file);
                    }
                });

            $(imageSelectors.thumbnailInput).change(function () {
                $(imageSelectors.thumbnailPreview).removeAttr('hidden');

                const reader = new FileReader();
                reader.onloadend =  (e) => {
                    $(imageSelectors.thumbnailPreview).attr('src', e.target.result)
                };
                reader.readAsDataURL(this.files[0]);
            });
        }
    });
