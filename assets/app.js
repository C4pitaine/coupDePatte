import './bootstrap.js';
/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
import './styles/app.css';
import './styles/app.scss';
import './styles/home.scss';
import './styles/adoption.scss';
import './styles/formulaire.scss';
import './styles/blog.scss';
import './styles/animal.scss';
import './styles/donation.scss';
import './styles/account.scss';
import './styles/header.scss';
import './styles/footer.scss';
import './styles/bootstrap.min.css';
import 'bootstrap/dist/js/bootstrap.bundle';

// Gestion Menu Burger
const menuBurger = document.querySelector('#burger')
const header = document.querySelector('header')
const menu = document.querySelector('#menu')
const menuA = document.querySelectorAll('#menu a')
const chevronMenu = document.querySelectorAll('.chevronMenu')
const menuLink = document.querySelectorAll('.menuLink .link')

menuBurger.addEventListener('click',()=>{
    header.classList.toggle('menuOpened')
    menu.classList.toggle('menuOpened')
    chevronMenu.forEach((chevron)=>{
        chevron.classList.remove('chevronOpen')
    })
    menuLink.forEach((link)=>{
        link.classList.remove('linkOpen')
    })
})

menuA.forEach((menuA)=>{
    menu.classList.remove('menuOpened')
    menuA.addEventListener('click',()=>{
        menu.classList.remove('menuOpened')
        header.classList.remove('menuOpened')
    })
})

chevronMenu.forEach((chevron,key)=>{
    chevron.addEventListener('click',()=>{
        chevron.classList.toggle('chevronOpen')
        menuLink[key].classList.toggle('linkOpen')
    })
})

/* Affiche du sous menu */
let menuUpperHeader = document.querySelector('.menuUpperHeader')
let underHeader = document.querySelector('.underHeader')

if(menuUpperHeader && underHeader){
    menuUpperHeader.addEventListener('mouseover',()=>{
        underHeader.classList.add('active')
    })
    menuUpperHeader.addEventListener('mouseout',()=>{
        underHeader.classList.remove('active')
    })
    
    underHeader.addEventListener('mouseover',()=>{
        underHeader.classList.add('active')
    })
    underHeader.addEventListener('mouseout',()=>{
        underHeader.classList.remove('active')
    })
}

// Gestion Alerte de suppresion admin
const deleteButtons = document.querySelectorAll('.deleteButton')
const alertDelete = document.querySelectorAll('.alertDelete')
const annulerDelete = document.querySelectorAll('.annulerDelete')

deleteButtons.forEach((deleteButton,key)=>{
    deleteButton.addEventListener('click',()=>{
        alertDelete[key].classList.add('active')
    })
})

annulerDelete.forEach((annulerDelete,key)=>{
    annulerDelete.addEventListener('click',()=>{
        alertDelete[key].classList.remove('active')
    })
})


/* Affichage du nombre de friandises dans le panier */
var numberAchatHeader = document.querySelectorAll('.numberAchatHeader')
var friandises = document.querySelectorAll('.friandise')

function numberCart() {
    if(localStorage.getItem('numberAchat'))
    {
       numberAchatHeader.forEach((item)=>{
        item.innerText = localStorage.getItem('numberAchat')
       })
    }
}
numberCart()

/* Ajout au panier */
var addToCart = document.querySelectorAll('.addToCart')
var achat = localStorage.getItem('listCart') ? JSON.parse(localStorage.getItem('listCart')) : []

addToCart.forEach((cart,key)=>{
    cart.addEventListener('click',()=>{
        var numberAchat = localStorage.getItem('numberAchat') ? localStorage.getItem('numberAchat') : 0
        numberAchat= parseInt(numberAchat) + 1
        localStorage.setItem('numberAchat',numberAchat)
        numberCart()

        var friandise = {
            animal: document.querySelector('.animalName').innerText,
            name : friandises[key].querySelector('.friandiseName').innerText,
            price : friandises[key].querySelector('.friandisePrice').innerText
        }

        achat.push(friandise)
        localStorage.setItem('listCart',JSON.stringify(achat))
    })
})

/* Scroll top */

let scrollTop = document.querySelector('.scrollTop')

scrollTop.addEventListener('click',()=>{
    window.scrollTo({
        top:0,
        left:0,
        behavior: "smooth"
    })
})