let lastPensionnaires = document.querySelectorAll('.containerPensionnaire')
let arrowLeft = document.querySelector('.arrowLeft')
let arrowRight = document.querySelector('.arrowRight')

let currentIndex = 0

if(window.innerWidth > 1500){
    lastPensionnaires.forEach(pensionnaire=>{
        pensionnaire.classList.add('notShow')
    })
    lastPensionnaires[currentIndex].classList.remove('notShow');
    lastPensionnaires[currentIndex+1].classList.remove('notShow');
    arrowLeft.classList.add('disabled')
    
    const slider = () =>{
        lastPensionnaires.forEach(pensionnaire=>{
            pensionnaire.classList.add('notShow')
        })
    
        lastPensionnaires[currentIndex].classList.remove('notShow')
        lastPensionnaires[currentIndex+1].classList.remove('notShow')
    
        if(currentIndex == 0){
            arrowLeft.classList.add('disabled')
        }else{
            arrowLeft.classList.remove('disabled')
        }
        if(currentIndex == lastPensionnaires.length-2){
            arrowRight.classList.add('disabled')
        }else{
            arrowRight.classList.remove('disabled')
        }
    }
    
    arrowLeft.addEventListener('click',()=>{
        currentIndex-=2;
        slider();
        console.log(window.innerWidth)
    })
    
    arrowRight.addEventListener('click',()=>{
        currentIndex+=2;
        slider();
        console.log(window.innerWidth)
    })
}else{
    lastPensionnaires.forEach(pensionnaire=>{
        pensionnaire.classList.add('notShow')
    })
    lastPensionnaires[currentIndex].classList.remove('notShow');
    arrowLeft.classList.add('disabled')
    
    const slider = () =>{
        lastPensionnaires.forEach(pensionnaire=>{
            pensionnaire.classList.add('notShow')
        })
    
        lastPensionnaires[currentIndex].classList.remove('notShow')
    
        if(currentIndex == 0){
            arrowLeft.classList.add('disabled')
        }else{
            arrowLeft.classList.remove('disabled')
        }
        if(currentIndex == lastPensionnaires.length-1){
            arrowRight.classList.add('disabled')
        }else{
            arrowRight.classList.remove('disabled')
        }
    }
    
    arrowLeft.addEventListener('click',()=>{
        currentIndex--;
        slider();
    })
    
    arrowRight.addEventListener('click',()=>{
        currentIndex++;
        slider();
    })
}