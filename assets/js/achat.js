var numberAchatHeader = document.querySelectorAll('.numberAchatHeader')
var friandises = document.querySelectorAll('.friandise')
var achat = localStorage.getItem('listCart') ? JSON.parse(localStorage.getItem('listCart')) : []

/* Affichage du nombre de friandises dans le panier */
function numberCart() {
    if(localStorage.getItem('numberAchat'))
    {
       numberAchatHeader.forEach((item,key)=>{
        item.innerText = localStorage.getItem('numberAchat')
       })
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

        var friandise = {
            name : friandises[key].querySelector('.friandiseName').innerText,
            price : friandises[key].querySelector('.friandisePrice').innerText
        }
        
        achat.push(friandise)
        localStorage.setItem('listCart',JSON.stringify(achat))
    })
})

