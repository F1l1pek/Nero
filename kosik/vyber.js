/**
 * @typedef {string} Cart
 */

/**
 * @enum {Cart}
 * @readonly
 * */
const Cart = {
    ADD: 'add',
    REMOVE: 'remove',
    UPDATE: 'update',
}

/**
 * @param {string} product_id
 * @param {HTMLInputElement} item
 * @param {Cart} event
 * @returns {void}
 */
function update_cart_product(product_id, item, event) {
    console.log(product_id, item, event);
    let value = parseInt(item.value);
    if (event === Cart.ADD) {
        value += 1;
        item.value = value.toString();
        send_to_server(product_id, value);
    }
    if (event === Cart.REMOVE) {
        value -= 1;
        item.value = value.toString();
        if (value <= 0) change_cart_item_html(product_id,item);
        send_to_server(product_id, value);
    }
    if (event === Cart.UPDATE) {
        if (value <= 0) change_cart_item_html(product_id,item);
        send_to_server(product_id, value);
    }
}

/**
 * @param {string} product_id
 * @param {number} count
 */
function send_to_server(product_id, count) {
    fetch(`/Nero/kosik/add_to_cart?id=`+product_id+'&pocet='+count.toString(),{
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
    }).then(r => r.json())
        .then(data => {
            console.log(data);
        });
}

/**
* @param {string} product_id
* @param {HTMLInputElement|HTMLButtonElement} item
* @returns {void}
* */
function change_cart_item_html(product_id,item) {
    let value;
    if (item instanceof HTMLInputElement) {
        value = parseInt(item.value);
    } else {
        value = 1;
    }
    if (value <= 0) {
        //create button element
        const parent = item.parentElement;
        const button_element = document.createElement('button');
        button_element.innerText = "Přidat do košíku";
        button_element.addEventListener('click', () => change_cart_item_html(product_id, button_element));
        parent.innerHTML = '';
        parent.appendChild(button_element);
    }else {
        const parent = item.parentElement;
        parent.innerHTML = '';

        const input = document.createElement('input');
        input.type = 'number';
        input.value = value.toString();
        input.min = '0';
        input.addEventListener('change', () => update_cart_product(product_id, input, Cart.UPDATE));

        const decrease_button = document.createElement('button');
        decrease_button.innerText = '-';
        decrease_button.classList.add('decrease');
        decrease_button.addEventListener('click', () => update_cart_product(product_id, input, Cart.REMOVE));

        const increase_button = document.createElement('button');
        increase_button.innerText = '+';
        increase_button.classList.add('increase');
        increase_button.addEventListener('click', () => update_cart_product(product_id, input, Cart.ADD));



        parent.appendChild(decrease_button);
        parent.appendChild(input);
        parent.appendChild(increase_button);
        send_to_server(product_id, value)

    }
}


/**
 * @param {string} product_id
 * @param {HTMLButtonElement} input
 * */
function add_to_cart(product_id,input) {
    console.log(product_id,input);
}

//after the document is loaded it will add event listeners to the buttons
document.addEventListener('DOMContentLoaded', () => {
    const cart_items = document.querySelectorAll('.cart-item');
    cart_items.forEach(item => {
        const decrease_button = item.querySelector('.decrease');
        const increase_button = item.querySelector('.increase');
        const input = item.querySelector('input');
        const add_button = item.querySelector('button.add-to-cart');

        if (input !== null){
            const product_id = input.dataset.id;
            increase_button.addEventListener('click', () => update_cart_product(product_id, input, Cart.ADD));
            decrease_button.addEventListener('click', () => update_cart_product(product_id, input, Cart.REMOVE));
            input.addEventListener('change', () => update_cart_product(product_id, input, Cart.UPDATE));
        }
        if (add_button !== null) {
            const product_id = add_button.dataset.id;
            add_button.addEventListener('click', () => change_cart_item_html(product_id, add_button));
        }
    });
});