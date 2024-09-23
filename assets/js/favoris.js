let favoris = document.querySelectorAll('.favori')
let show = document.querySelectorAll('.show')

favoris.forEach((favori,index)=>{
    let open = false
    favori.addEventListener('click',()=>{
        if(open == false){
            show[index].classList.add('active');
            open = true;
        }else{
            show[index].classList.remove('active');
            open = false;
        }
    })
    favori.addEventListener('mouseover',()=>{
        show[index].classList.add('active')
    })
    favori.addEventListener('mouseout',()=>{
        show[index].classList.remove('active')
    })
})