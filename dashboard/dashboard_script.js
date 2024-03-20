const wrapped = document.querySelectorAll('.wrapped');

wrapped.forEach(element => {
    element.addEventListener('click', () =>{
        element.classList.toggle('unwrapped');
        element.closest('tr').nextElementSibling.classList.toggle('none');
    })
});