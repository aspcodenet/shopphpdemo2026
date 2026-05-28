function drawCart(cartItems, cartTotalPrice) {
    const cartTotalPriceElement = document.getElementById('cartTotalPrice');
    if (cartTotalPriceElement) {
        document.getElementById('cartTotalPrice').innerText = cartTotalPrice;
    }
    const cartItemElement = document.getElementById('cartItem');
    if (cartItemElement) {
        cartItemElement.innerHTML = "";
        // Rita om hela carten
        cartItems.forEach(cartItem => {
            cartItemElement.innerHTML += `
                <tr>
                    <td>${cartItem.productName}</td>
                    <td>${cartItem.productPrice}</td>
                    <td>${cartItem.quantity}</td>   
                    <td>${cartItem.productPrice * cartItem.quantity}</td>
                    <td>
                        <a  onclick="addToCart(${cartItem.productId})"   class="btn btn-primary">+</a>
                        <a  onclick="removeFromCart(${cartItem.productId})"   class="btn btn-primary">-</a>
                    </td>
                </tr>
            `;
        });
    }

}
async function removeFromCart(productId) {
    let resp = await fetch(`/javascriptRemoveFromCart?id=${productId}`);
    let data = await resp.json();
    document.getElementById('cartItemCount').innerText = data.cartItemCount;
    drawCart(data.cartItems, data.cartTotalPrice);
}

async function addToCart(productId) { 
    let resp = await fetch(`/javascriptAddToCart?id=${productId}`);
    let data = await resp.json();
    document.getElementById('cartItemCount').innerText = data.cartItemCount;
    drawCart(data.cartItems, data.cartTotalPrice);
}