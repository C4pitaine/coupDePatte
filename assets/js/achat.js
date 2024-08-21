/* Affichage du nombre de friandises dans le panier */

var numberAchatHeader = document.querySelector('.numberAchatHeader')

function numberCart() {
    if(localStorage.getItem('numberAchat'))
    {
        numberAchatHeader.innerText = localStorage.getItem('numberAchat')
    }
}
numberCart()
/* Ajout au panier */
var addToCart = document.querySelectorAll('.addToCart')

addToCart.forEach((cart,key)=>{
    cart.addEventListener('click',()=>{
        var numberAchat = localStorage.getItem('numberAchat') ? localStorage.getItem('numberAchat') : 0
        numberAchat= parseInt(numberAchat) + 1
        localStorage.setItem('numberAchat',numberAchat)
        numberCart()
    })
})

