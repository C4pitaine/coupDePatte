const montantSelections = document.querySelectorAll('.montantSelection');
const montantTotal = document.querySelector('.total')
const inputTotal = document.querySelector('#donation_one_montant')
const reset = document.querySelector('.reset')

var totalDon = 0
inputTotal.value = 0
montantSelections.forEach((elem)=>{
    elem.addEventListener('click',()=>{
        elem.classList.toggle('active')
        totalDon += parseInt(elem.innerText)
        montantTotal.innerText = totalDon+"€"
        inputTotal.value = totalDon
    })
})

reset.addEventListener('click',()=>{
    montantTotal.innerText = 0+"€"
    inputTotal.value = 0
    totalDon = 0
})

