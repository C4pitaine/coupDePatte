const addImage = document.querySelector('#add-image')
addImage.addEventListener('click',()=>{
    const widgetCounter = document.querySelector("#widgets-counter")
    const index = +widgetCounter.value
    const annonceImages = document.querySelector("#animal_images")

    const prototype = annonceImages.dataset.prototype.replace(/__name__/g, index)
    annonceImages.insertAdjacentHTML('beforeend', prototype)
    widgetCounter.value = index+1
    handleDeleteButtons()
})

const updateCounter = () => {
    const count = document.querySelectorAll("#animal_images div.form-group").length
    document.querySelector("#widgets-counter").value = count 
}

const handleDeleteButtons = () => {
    let deletes = document.querySelectorAll("button[data-action='delete']")
    deletes.forEach(button => {
        button.addEventListener('click', ()=>{
            const target = button.dataset.target
            const elementTarget = document.querySelector(target)
            if(elementTarget){
                elementTarget.remove()
            }
        })
    })

}

updateCounter()
handleDeleteButtons()