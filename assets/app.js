import './bootstrap.js';
/*
 * Welcome to your app's main JavaScript file!
 *
 * This file will be included onto the page via the importmap() Twig function,
 * which should already be in your base.html.twig.
 */
import './styles/app.css';
import './app.scss';
import './home.scss';
import './adoption.scss'
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
