/* Permet d'afficher les informations en activant la classe active d'un favori */
let favoris = document.querySelectorAll('.favori')
let show = document.querySelectorAll('.show')

favoris.forEach((favori,index)=>{

    favori.setAttribute('open',false)
    favori.addEventListener('click',()=>{
        let open = favori.getAttribute('open')
        if(open != "true"){
            favori.setAttribute('open',true)
            show[index].classList.add('active')
        }
    })

    favori.addEventListener('mouseover',()=>{
        show[index].classList.add('active')
    })
    favori.addEventListener('mouseout',()=>{
        show[index].classList.remove('active')
    })
})