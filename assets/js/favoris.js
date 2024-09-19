let favoris = document.querySelectorAll('.favori')
let show = document.querySelectorAll('.show')

favoris.forEach((favori,index)=>{
    favori.addEventListener('click',()=>{
        show[index].classList.toggle('active');
    })
    favori.addEventListener('mouseover',()=>{
        show[index].classList.add('active')
    })
    favori.addEventListener('mouseout',()=>{
        show[index].classList.remove('active')
    })
})