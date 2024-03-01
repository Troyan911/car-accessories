import '../bootstrap.js';
import axios from "axios";

const checkoutForm = $('#checkout-form');

function getFields() {
    const fields = checkoutForm.serializeArray()
        .reduce((obj, item) => {
            obj[item.name] = item.value;
            return obj;
        }, {})

    return fields;
}

function isEmptyFields() {
    const fields = getFields();
    return Object.values(fields).some((val) => val.length < 1)
}

function enablePayButton(data, actions) {
    actions.disable();
    checkoutForm.change(() => {
        if (!isEmptyFields()) {
            actions.enable();
        } else {
            actions.disable();
        }
    });
}

paypal.Buttons({
    style: {
        layout: 'horizontal'
    },

    onInit: function (data, actions) {
        enablePayButton(data, actions);
    },

    onClick: function (data, actions) {
        if (isEmptyFields()) {
            iziToast.warning({
                title: 'Please fill the form',
                position: 'topRight'
            })
        }
    },

    // Call your server to set up the transaction
    createOrder: function (data, actions) {
        // return fetch('/demo/checkout/api/paypal/order/create/', {
        return axios.post('/ajax/paypal/order/create/', getFields())
            .then(function (res) {
                return res.data.vendor_order_id;
            });
    },

    // Call your server to finalize the transaction
    onApprove: function (data, actions) {
        console.log('data', data)
        return axios.post(`/ajax/paypal/order/${data.orderID}/capture/`)
            .then(function (res) {
                return res.data;
            }).then(function (orderData) {
                // Three cases to handle:
                //   (1) Recoverable INSTRUMENT_DECLINED -> call actions.restart()
                //   (2) Other non-recoverable errors -> Show a failure message
                //   (3) Successful transaction -> Show confirmation or thank you

                // Successful capture! For demo purposes:
                console.log('Capture result', orderData, JSON.stringify(orderData, null, 2));
                const transaction = orderData.purchase_units[0].payments.captures[0];
                // alert('Transaction ' + transaction.status + ': ' + transaction.id + '\n\nSee console for all available details');

                iziToast.success({
                    title: 'Transaction success!',
                    position: 'topRight',
                    onClosing: () => {
                        window.location.href = `/orders/${orderData.id}/paypal/thank-you`;
                    }
                })

                // Replace the above to show a success message within this page, e.g.
                // const element = document.getElementById('paypal-button-container');
                // element.innerHTML = '';
                // element.innerHTML = '<h3>Thank you for your payment!</h3>';
                // Or go to another URL:  actions.redirect('thank_you.html');
            }).catch((orderData) => {

                // This example reads a v2/checkout/orders capture response, propagated from the server
                // You could use a different API or structure for your 'orderData'
                const errorDetail = Array.isArray(orderData.details) && orderData.details[0];

                if (errorDetail && errorDetail.issue === 'INSTRUMENT_DECLINED') {
                    return actions.restart(); // Recoverable state, per:
                    // https://developer.paypal.com/docs/checkout/integration-features/funding-failure/
                }

                if (errorDetail) {
                    let message = 'Sorry, your transaction could not be processed.';
                    if (errorDetail.description) message += '\n\n' + errorDetail.description;
                    if (orderData.debug_id) message += ' (' + orderData.debug_id + ')';

                    // Show a failure message (try to avoid alerts in production environments)
                    // return alert(msg);

                    iziToast.danger({
                        title: Warning,
                        message,
                        position: 'topRight'
                    })
                }
            });
    }
}).render('#paypal-button-container');
