/* Permet d'afficher les informations en activant la classe active d'un favori */
let favoris = document.querySelectorAll('.favori')
let show = document.querySelectorAll('.show')

favoris.forEach((favori,index)=>{

    favori.setAttribute('open',false)
    favori.addEventListener('click',()=>{
        show[index].classList.toggle('active')
    })
})