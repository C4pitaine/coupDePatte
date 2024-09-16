var numberAchatHeader = document.querySelectorAll('.numberAchatHeader')

/* Affichage du nombre de friandises dans le panier */
function numberCart() {
    if(localStorage.getItem('numberAchat'))
    {
       numberAchatHeader.forEach((item,key)=>{
        item.innerText = localStorage.getItem('numberAchat')
       })
    }
}

/* Affichage des éléments présents dans le panier */
function panierRefresh() {
    if(localStorage.getItem('listCart') && localStorage.getItem('listCart').length != 2){
        var panier = JSON.parse(localStorage.getItem('listCart'))
        var total = 0
        var ticket =  document.querySelector('#cart_cart')
        ticket.value = ""

        panier.forEach(element => {
            var divAnimal = document.createElement("p")
            var divName = document.createElement("p")
            var divPrice = document.createElement("p")
            var divAchat = document.createElement("div")
            var divDelete = document.createElement("div")
    
            divAnimal.innerHTML = element.animal
            divName.innerHTML = element.name
            divPrice.innerHTML = element.price+"€"
            divDelete.innerText = "X"
    
            divAchat.appendChild(divAnimal)
            divAchat.appendChild(divName)
            divAchat.appendChild(divPrice)
            divAchat.appendChild(divDelete)
    
            divAchat.classList.add('divAchat')
            divDelete.classList.add('delButton')
            document.querySelector('.cart').appendChild(divAchat)
    
            ticket.value += element.animal+" - "+element.name+" - "+element.price+"\n"
            
            total+= parseFloat(element.price)
        });
        document.querySelector('.total').innerHTML = "Montant total : "+total.toFixed(2)+"€"
        document.querySelector('#cart_total').value = total.toFixed(2)
        
    }else{
        divEmpty = document.createElement("p")
        divEmpty.innerText = "Votre panier est vide"
        divEmpty.classList.add('emptyCart')
        document.querySelector('.cart').appendChild(divEmpty)
        document.querySelector('#cart_cart').value = "Vide"
        document.querySelector('#cart_total').value = 0
          document.querySelector('.total').innerHTML = "Montant total : 0€"
    }
    deleteItem()
}
panierRefresh()

/* Suppression d'un élément du panier */
function deleteItem(){
    var delButtons = document.querySelectorAll('.delButton')

    delButtons.forEach((element,index)=>{
        var panier = JSON.parse(localStorage.getItem('listCart'))
        element.addEventListener('click',()=>{
            panier.splice(index,1)

            localStorage.setItem('listCart',JSON.stringify(panier))

            /* - 1 du nombre d'achats pour l'affichage du nombre */
            localStorage.setItem('numberAchat',parseInt(localStorage.getItem('numberAchat') - 1))

            document.querySelector('.cart').innerHTML = ""
            panierRefresh()
            numberCart()
        })
    })
}

