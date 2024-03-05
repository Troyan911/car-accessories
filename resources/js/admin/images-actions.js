import axios from "axios";

const selectors = {
    remove: '.image-remove',
    wrapper: '.images-wrapper',
    item: '.images-wrapper-item',
    add: '.image-add',
    input: '.image-input-add',
}

$(document).ready(function () {

    $(document).on('click', selectors.add, function (e) {
        e.preventDefault();
        $(this).parents(selectors.item).find(selectors.input).click();
    })

    $(document).on('change', selectors.input, function (e) {
        const parent = $(this).parent();
        const addItemBlock = $(this).parents(selectors.item).clone();
        const addButton = $(this).parents(selectors.item).find(selectors.add);
        const url = addButton.data('url');
        const file = this.files[0];

        let data = new FormData();
        data.append('image', file, file.name);

        axios.post(url, data, {
                headers: {'Content-Type': 'multipart/form-data'}
            }
        ).then((response) => {
            console.log('resp', response);
            if (response?.data?.url) {
                //todo add css
                parent.append(`<img src="${response.data.url}" style="max-width: 100%; max-height: 200px;" />`)
                $(this).remove();
                $(selectors.wrapper).append(addItemBlock);
                addButton.parent()
                    .html(`<button class="btn btn-danger image-remove" data-url="${response.data.imageUrl}"><i class="fa-solid fa-trash-can"></i></button>`);
            }
        }).catch((err) => {
            console.log('err', err)
        })
    });

    $(document).on('click', selectors.remove, function (e) {
        e.preventDefault();
        const button = $(this);

        axios.delete(button.data('url'), {
            responseType: 'json'
        }).then(function (response) {
            button.parents(selectors.item).remove();
        }).catch(function (err) {
            console.log('err', err)
        })
    })
});
