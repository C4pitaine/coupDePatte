let lastPensionnaires = document.querySelectorAll('.containerPensionnaire')
let arrowLeft = document.querySelector('.arrowLeft')
let arrowRight = document.querySelector('.arrowRight')

let currentIndex = 0
let screenSize = window.innerWidth;

const sliderSize = () => {

    /* Suppression des anciens eventListener sinon cela crée une erreur avec l'incrémentation de currentIndex */
    arrowLeft.replaceWith(arrowLeft.cloneNode(true)); 
    arrowRight.replaceWith(arrowRight.cloneNode(true));
    arrowLeft = document.querySelector('.arrowLeft');
    arrowRight = document.querySelector('.arrowRight');

    if(screenSize > 1500){
        lastPensionnaires.forEach(pensionnaire=>{
            pensionnaire.classList.add('notShow')
        })
        lastPensionnaires[currentIndex].classList.remove('notShow');
        lastPensionnaires[currentIndex+1].classList.remove('notShow');
        arrowLeft.classList.add('disabled')
        
        const sliderOne = () =>{
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
            sliderOne();
        })
        
        arrowRight.addEventListener('click',()=>{
            currentIndex+=2;
            sliderOne();
        })
    }else{
        lastPensionnaires.forEach(pensionnaire=>{
            pensionnaire.classList.add('notShow')
        })
        lastPensionnaires[currentIndex].classList.remove('notShow');
        arrowLeft.classList.add('disabled')
        
        const sliderTwo = () =>{
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
            sliderTwo();
        })
        
        arrowRight.addEventListener('click',()=>{
            currentIndex++;
            sliderTwo();
        })
    }
}
sliderSize()

/* Si la taille de l'écran est modifiée */
window.addEventListener('resize',()=>{
    currentIndex = 0;
    screenSize = window.innerWidth;

    /* Evite les erreurs de suppresions des flèches si on est sur la derniers slide et qu'on modifie la taille de l'écran */
    arrowRight.classList.remove('disabled')
    arrowLeft.classList.add('disabled')

    sliderSize()
})


